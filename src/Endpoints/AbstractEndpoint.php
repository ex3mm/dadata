<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\Endpoints;

use Ex3mm\Dadata\Client\DadataClient;
use Ex3mm\Dadata\Config\DadataConfig;
use Ex3mm\Dadata\Contracts\DtoInterface;
use Ex3mm\Dadata\Contracts\EndpointInterface;
use Ex3mm\Dadata\Exceptions\ApiException;
use Ex3mm\Dadata\Exceptions\AuthenticationException;
use Ex3mm\Dadata\Exceptions\NetworkException;
use Ex3mm\Dadata\Exceptions\RateLimitException;
use Ex3mm\Dadata\Exceptions\ValidationException;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;

/**
 * Базовый класс для всех endpoint-классов DaData API.
 */
abstract class AbstractEndpoint implements EndpointInterface
{
    /** Версия Suggestions API */
    public const string API_VERSION = '4_1';

    public function __construct(
        protected readonly DadataClient $client,
        protected readonly DadataConfig $config,
    ) {
    }

    /**
     * Возвращает путь к endpoint.
     *
     * @codeCoverageIgnore Абстрактный метод, тестируется через наследников
     */
    abstract protected function getPath(): string;

    /**
     * Возвращает базовый URL для endpoint.
     *
     * @codeCoverageIgnore Абстрактный метод, тестируется через наследников
     */
    abstract protected function getBaseUrl(): string;

    /**
     * Выполняет POST-запрос к API.
     *
     * @param array<string, mixed>|array<int, string> $body Тело запроса
     *
     * @throws ApiException
     * @throws AuthenticationException
     * @throws RateLimitException
     * @throws ValidationException
     * @throws NetworkException
     */
    public function execute(array $body): DtoInterface
    {
        try {
            $response = $this->post($body);

            // Проверка на HTTP-ошибки
            if ($response->getStatusCode() >= 400) {
                $this->handleHttpError($response);
            }

            return $this->parseResponse($response);
        } catch (\JsonException $exception) {
            // Преобразуем ошибки JSON-декодирования в ValidationException
            throw new ValidationException(
                message: 'Invalid JSON response from API: ' . $exception->getMessage(),
                statusCode: 0,
                responseBody: '',
                errors: ['json_decode_error']
            );
        } catch (GuzzleException $exception) {
            // Преобразуем сетевые ошибки Guzzle в NetworkException
            throw NetworkException::fromGuzzleException($exception);
        }
    }

    /**
     * Выполняет POST-запрос.
     *
     * @param array<string, mixed>|array<int, string> $body
     *
     * @throws GuzzleException
     */
    protected function post(array $body): ResponseInterface
    {
        $url = $this->getBaseUrl() . $this->getPath();

        /** @var ResponseInterface $response */
        $response = $this->client->getClient()->request('POST', $url, [
            'json'    => $body,
            'headers' => $this->getHeaders(),
        ]);

        return $response;
    }

    /**
     * Возвращает заголовки для запроса.
     *
     * @return array<string, string>
     */
    protected function getHeaders(): array
    {
        return [
            'Authorization' => 'Token ' . $this->config->apiKey,
            'Content-Type'  => 'application/json',
            'Accept'        => 'application/json',
        ];
    }

    /**
     * Парсит ответ от API в DTO.
     *
     * @codeCoverageIgnore Абстрактный метод, тестируется через наследников
     */
    abstract protected function parseResponse(ResponseInterface $response): DtoInterface;

    /**
     * Обрабатывает HTTP-ошибки и бросает соответствующее исключение.
     *
     * @param ResponseInterface $response HTTP-ответ с ошибкой
     *
     * @throws AuthenticationException
     * @throws RateLimitException
     * @throws ValidationException
     * @throws ApiException
     */
    private function handleHttpError(ResponseInterface $response): never
    {
        $statusCode = $response->getStatusCode();

        throw match ($statusCode) {
            401, 403 => AuthenticationException::fromResponse($response),
            429 => RateLimitException::fromResponse($response),
            400, 422 => ValidationException::fromResponse($response),
            default => ApiException::fromResponse($response),
        };
    }
}

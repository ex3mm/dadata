<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\Endpoints\Raw;

use Ex3mm\Dadata\Contracts\DtoInterface;
use Ex3mm\Dadata\DTO\Response\Raw\RawResponse;
use Ex3mm\Dadata\Endpoints\AbstractEndpoint;
use Ex3mm\Dadata\Exceptions\ApiException;
use Ex3mm\Dadata\Exceptions\ValidationException;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;

/**
 * Endpoint для произвольных запросов к DaData API.
 */
final class RawEndpoint extends AbstractEndpoint
{
    private string $url    = '';
    private string $method = 'POST';
    /** @var array<string, string> */
    private array $customHeaders = [];

    /**
     * Устанавливает URL для запроса.
     *
     * @param string $url Полный URL
     */
    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    /**
     * Устанавливает HTTP-метод.
     *
     * @param string $method HTTP-метод (GET, POST)
     */
    public function setMethod(string $method): void
    {
        $this->method = strtoupper($method);
    }

    /**
     * Устанавливает дополнительные заголовки.
     *
     * @param array<string, string> $headers Заголовки
     */
    public function setCustomHeaders(array $headers): void
    {
        $this->customHeaders = $headers;
    }

    protected function getPath(): string
    {
        return '';
    }

    protected function getBaseUrl(): string
    {
        return '';
    }

    /**
     * @param array<int|string, mixed> $body
     *
     * @throws ApiException
     */
    #[\Override]
    public function execute(array $body): DtoInterface
    {
        try {
            $options = [
                'headers' => array_merge($this->getHeaders(), $this->customHeaders),
            ];

            if ($this->method === 'POST') {
                $options['json'] = $body;
            }

            $response = $this->client->getClient()->request($this->method, $this->url, $options);

            return $this->parseResponse($response);
        } catch (GuzzleException $exception) {
            throw new ApiException(
                'Ошибка при выполнении запроса к DaData API: ' . $exception->getMessage(),
                $exception->getCode(),
                ''
            );
        }
    }

    protected function parseResponse(ResponseInterface $response): DtoInterface
    {
        $body = (string) $response->getBody();
        $data = json_decode($body, true, 512, JSON_THROW_ON_ERROR);

        if (!is_array($data)) {
            throw new ValidationException(
                message: 'Invalid JSON response: expected array, got ' . gettype($data),
                statusCode: 0,
                responseBody: '',
                errors: ['invalid_json_structure'],
            );
        }

        /** @var array<string, mixed> $data */
        return RawResponse::fromArray($data, $body);
    }
}

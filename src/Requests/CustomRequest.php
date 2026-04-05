<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\Requests;

use Ex3mm\Dadata\Client\DadataClient;
use Ex3mm\Dadata\Config\DadataConfig;
use Ex3mm\Dadata\Exceptions\NetworkException;
use Ex3mm\Dadata\Exceptions\ValidationException;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;

/**
 * Request builder для произвольных запросов к DaData API.
 */
final class CustomRequest
{
    private string $method    = 'GET';
    private ?string $url      = null;
    private ?string $endpoint = null;
    private ?string $baseUrl  = null;
    /** @var array<string, string> */
    private array $headers = [];
    /** @var array<string, mixed> */
    private array $query = [];
    /** @var array<int|string, mixed>|string|null */
    private array|string|null $body = null;
    private bool $useJson           = true;

    public function __construct(
        private readonly DadataClient $client,
        private readonly DadataConfig $config,
    ) {
    }

    /**
     * HTTP метод запроса.
     */
    public function method(string $method): static
    {
        $normalized = strtoupper(trim($method));
        if ($normalized === '') {
            throw new ValidationException('HTTP-метод не может быть пустым');
        }

        $this->method = $normalized;

        return $this;
    }

    /**
     * Абсолютный URL.
     */
    public function url(string $url): static
    {
        $trimmed = trim($url);
        if ($trimmed === '') {
            throw new ValidationException('URL не может быть пустым');
        }

        $this->url = $trimmed;

        return $this;
    }

    /**
     * Относительный endpoint (например /suggestions/api/4_1/rs/suggest/address).
     */
    public function endpoint(string $endpoint): static
    {
        $trimmed = trim($endpoint);
        if ($trimmed === '') {
            throw new ValidationException('Endpoint не может быть пустым');
        }

        $this->endpoint = $trimmed;

        return $this;
    }

    /**
     * Базовый URL для относительного endpoint.
     */
    public function baseUrl(string $baseUrl): static
    {
        $trimmed = trim($baseUrl);
        if ($trimmed === '') {
            throw new ValidationException('Base URL не может быть пустым');
        }

        $this->baseUrl = rtrim($trimmed, '/');

        return $this;
    }

    /**
     * Дополнительные заголовки.
     *
     * @param array<string, string> $headers
     */
    public function headers(array $headers): static
    {
        $this->headers = array_merge($this->headers, $headers);

        return $this;
    }

    /**
     * Query-параметры.
     *
     * @param array<string, mixed> $query
     */
    public function query(array $query): static
    {
        $this->query = $query;

        return $this;
    }

    /**
     * JSON-тело запроса.
     *
     * @param array<string, mixed>|array<int, mixed> $body
     */
    public function json(array $body): static
    {
        $this->body    = $body;
        $this->useJson = true;

        return $this;
    }

    /**
     * Сырое тело запроса.
     */
    public function body(string $body): static
    {
        $this->body    = $body;
        $this->useJson = false;

        return $this;
    }

    /**
     * Выполняет запрос и возвращает оригинальный ответ DaData.
     */
    public function get(): string
    {
        $url = $this->resolveUrl();

        $headers = [
            'Authorization' => 'Token ' . $this->config->apiKey,
            'Accept'        => 'application/json',
        ];

        if ($this->config->secretKey !== '') {
            $headers['X-Secret'] = $this->config->secretKey;
        }

        $headers = array_merge($headers, $this->headers);

        /** @var array<string, mixed> $options */
        $options = [
            'headers'     => $headers,
            'query'       => $this->query,
            'http_errors' => false,
        ];

        if ($this->body !== null) {
            if ($this->useJson && is_array($this->body)) {
                $options['json'] = $this->body;
            } else {
                $options['body'] = $this->body;
            }
        }

        try {
            /** @var ResponseInterface $response */
            $response = $this->client->getClient()->request($this->method, $url, $options);
        } catch (GuzzleException $exception) {
            throw NetworkException::fromGuzzleException($exception);
        }

        return (string) $response->getBody();
    }

    /**
     * @throws ValidationException
     */
    private function resolveUrl(): string
    {
        if ($this->url !== null) {
            return $this->url;
        }

        if ($this->endpoint === null) {
            throw new ValidationException('Нужно указать endpoint() или url()');
        }

        if (str_starts_with($this->endpoint, 'http://') || str_starts_with($this->endpoint, 'https://')) {
            return $this->endpoint;
        }

        $baseUrl = $this->baseUrl ?? $this->config->baseUrlSuggestions;

        return rtrim($baseUrl, '/') . '/' . ltrim($this->endpoint, '/');
    }
}

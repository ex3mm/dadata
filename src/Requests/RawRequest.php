<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\Requests;

use Ex3mm\Dadata\DTO\Response\Raw\RawResponse;
use Ex3mm\Dadata\Endpoints\Raw\RawEndpoint;
use Ex3mm\Dadata\Exceptions\ValidationException;

/**
 * Request builder для произвольных запросов к DaData API.
 */
final class RawRequest extends AbstractRequest
{
    private string $url    = '';
    private string $method = 'POST';
    /** @var array<string, mixed> */
    private array $body = [];
    /** @var array<string, string> */
    private array $headers = [];

    /**
     * Устанавливает URL для запроса.
     *
     * @param string $url Полный URL
     */
    public function url(string $url): static
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Устанавливает HTTP-метод.
     *
     * @param string $method HTTP-метод (GET, POST)
     */
    public function method(string $method): static
    {
        $this->method = strtoupper($method);

        return $this;
    }

    /**
     * Устанавливает тело запроса.
     *
     * @param array<string, mixed> $body Тело запроса
     */
    public function body(array $body): static
    {
        $this->body = $body;

        return $this;
    }

    /**
     * Добавляет дополнительные заголовки.
     *
     * @param array<string, string> $headers Заголовки
     */
    public function withHeaders(array $headers): static
    {
        $this->headers = $headers;

        return $this;
    }

    #[\Override]
    public function send(): RawResponse
    {
        $this->validate();

        /** @var RawEndpoint $endpoint */
        $endpoint = $this->endpoint;
        $endpoint->setUrl($this->url);
        $endpoint->setMethod($this->method);
        $endpoint->setCustomHeaders($this->headers);

        /** @var RawResponse */
        return $endpoint->execute($this->body);
    }

    protected function validate(): void
    {
        if ($this->url === '' || $this->url === '0') {
            throw new ValidationException(
                message: 'URL не может быть пустым',
                statusCode: 0,
                responseBody: '',
                errors: ['required'],
            );
        }

        if (! in_array($this->method, ['GET', 'POST'], true)) {
            throw new ValidationException(
                message: 'Неподдерживаемый HTTP-метод. Разрешены: GET, POST',
                statusCode: 0,
                responseBody: '',
                errors: ['invalid'],
            );
        }
    }

    /**
     * @return array<string, mixed>
     */
    protected function toArray(): array
    {
        return $this->body;
    }
}

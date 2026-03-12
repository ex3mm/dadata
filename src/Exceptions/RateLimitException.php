<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\Exceptions;

use Psr\Http\Message\ResponseInterface;

/**
 * Исключение для превышения лимита запросов к API.
 */
final class RateLimitException extends DadataException
{
    /**
     * @param string $message Сообщение об ошибке
     * @param int $statusCode HTTP-статус код
     * @param string $responseBody Тело ответа от API
     * @param int $retryAfter Количество секунд до следующей попытки
     */
    public function __construct(
        string $message,
        private readonly int $statusCode = 429,
        private readonly string $responseBody = '',
        private readonly int $retryAfter = 0,
    ) {
        parent::__construct($message, $statusCode);
    }

    /**
     * Создаёт исключение из HTTP-ответа.
     *
     * @param ResponseInterface $response HTTP-ответ
     */
    public static function fromResponse(ResponseInterface $response): self
    {
        $statusCode = $response->getStatusCode();
        $body       = (string) $response->getBody();

        // Пытаемся получить Retry-After из заголовков
        $retryAfter = 0;
        if ($response->hasHeader('Retry-After')) {
            $retryAfterHeader = $response->getHeader('Retry-After')[0] ?? '0';
            $retryAfter       = (int) $retryAfterHeader;
        }

        $message = $retryAfter > 0
            ? "Превышен лимит запросов к API. Повторите попытку через {$retryAfter} секунд"
            : 'Превышен лимит запросов к API';

        return new self(
            message: $message,
            statusCode: $statusCode,
            responseBody: $body,
            retryAfter: $retryAfter,
        );
    }

    /**
     * Возвращает HTTP-статус код ошибки.
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * Возвращает тело ответа от API.
     */
    public function getResponseBody(): string
    {
        return $this->responseBody;
    }

    /**
     * Возвращает количество секунд до следующей попытки.
     */
    public function getRetryAfter(): int
    {
        return $this->retryAfter;
    }
}

<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\Exceptions;

use GuzzleHttp\Exception\GuzzleException;

/**
 * Исключение для сетевых ошибок при обращении к DaData API.
 *
 * Бросается при проблемах с сетевым соединением:
 * - Timeout (превышено время ожидания)
 * - Connection refused (отказ в соединении)
 * - DNS resolution failure (ошибка разрешения DNS)
 */
final class NetworkException extends DadataException
{
    /**
     * @param string $message Сообщение об ошибке
     * @param int $statusCode HTTP-статус код (0 для сетевых ошибок)
     * @param string $responseBody Тело ответа от API (пустое для сетевых ошибок)
     */
    public function __construct(
        string $message,
        private readonly int $statusCode = 0,
        private readonly string $responseBody = '',
        int $code = 0,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Создаёт исключение из Guzzle exception.
     *
     * @param GuzzleException $exception Оригинальное исключение Guzzle
     *
     * @return self
     */
    public static function fromGuzzleException(GuzzleException $exception): self
    {
        return new self(
            message: 'Сетевая ошибка при обращении к DaData API: ' . $exception->getMessage(),
            statusCode: 0,
            responseBody: '',
            code: $exception->getCode(),
            previous: $exception
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
}

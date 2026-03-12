<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\Exceptions;

use Psr\Http\Message\ResponseInterface;

/**
 * Исключение для ошибок авторизации в DaData API.
 *
 * Бросается при проблемах с API-ключами:
 * - HTTP 401: Невалидный API-ключ
 * - HTTP 403: Доступ запрещён (недостаточно прав)
 */
final class AuthenticationException extends DadataException
{
    /**
     * @param string $message Сообщение об ошибке
     * @param int $statusCode HTTP-статус код
     * @param string $responseBody Тело ответа от API
     */
    public function __construct(
        string $message,
        private readonly int $statusCode = 0,
        private readonly string $responseBody = '',
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

        $message = match ($statusCode) {
            401     => 'Невалидный API-ключ. Проверьте конфигурацию dadata.api_key',
            403     => 'Доступ запрещён. Проверьте права API-ключа',
            default => "Ошибка авторизации (HTTP {$statusCode})",
        };

        return new self(
            message: $message,
            statusCode: $statusCode,
            responseBody: $body,
        );
    }

    /**
     * Создаёт исключение для ошибки 401 (невалидный API-ключ).
     *
     * @return self
     */
    public static function invalidApiKey(): self
    {
        return new self(
            message: 'Невалидный API-ключ. Проверьте конфигурацию dadata.api_key',
            statusCode: 401
        );
    }

    /**
     * Создаёт исключение для ошибки 403 (доступ запрещён).
     *
     * @return self
     */
    public static function forbidden(): self
    {
        return new self(
            message: 'Доступ запрещён. Проверьте права API-ключа',
            statusCode: 403
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

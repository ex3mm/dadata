<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\Exceptions;

use Psr\Http\Message\ResponseInterface;

/**
 * Исключение для ошибок API DaData.
 */
final class ApiException extends DadataException
{
    private readonly string $rawResponse;

    /**
     * @param string $message Сообщение об ошибке
     * @param int $statusCode HTTP-статус код
     * @param string $rawResponse Оригинальный ответ от API (без чувствительных данных)
     */
    public function __construct(
        string $message,
        private readonly int $statusCode,
        string $rawResponse,
    ) {
        // Sanitize the raw response before storing it
        $this->rawResponse = self::sanitizeResponse($rawResponse);
        parent::__construct($message, $statusCode);
    }

    /**
     * Sanitizes sensitive data from response content.
     *
     * @param string $responseBody Response body content
     *
     * @return string Sanitized response body
     */
    private static function sanitizeResponse(string $responseBody): string
    {
        // Sanitize various API key patterns in response body
        $patterns = [
            // Bearer tokens
            '/Bearer\s+[A-Za-z0-9\-_\.]+/' => 'Bearer ***',
            // API keys with common prefixes (expanded list)
            '/(?:api_key|secret_key|sk_live|sk_test|message_key|auth_key|access_key|private_key|public_key)_[A-Za-z0-9]+/' => '***',
            // JWT tokens (basic pattern)
            '/eyJ[A-Za-z0-9\-_]+\.[A-Za-z0-9\-_]+\.[A-Za-z0-9\-_]*/' => '***',
            // Generic long alphanumeric strings that might be keys (20+ chars)
            '/\b[A-Za-z0-9]{20,}\b/' => '***',
        ];

        $sanitized = $responseBody;
        foreach ($patterns as $pattern => $replacement) {
            $result = preg_replace($pattern, $replacement, $sanitized);
            // Проверяем на ошибку PCRE (preg_replace может вернуть null)
            if ($result !== null) {
                $sanitized = $result;
            }
            // Если preg_replace вернул null, оставляем предыдущее значение
        }

        return $sanitized;
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

        // Sanitize the response body before using it in message and storing it
        $sanitizedBody = self::sanitizeResponse($body);

        $message = sprintf(
            'DaData API вернул ошибку %d: %s',
            $statusCode,
            $sanitizedBody !== '' ? $sanitizedBody : 'Нет описания ошибки'
        );

        return new self($message, $statusCode, $sanitizedBody);
    }

    /**
     * Возвращает HTTP-статус код ошибки.
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * Возвращает оригинальный ответ от API.
     */
    public function getRawResponse(): string
    {
        return $this->rawResponse;
    }
}

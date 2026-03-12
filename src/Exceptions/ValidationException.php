<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\Exceptions;

use Psr\Http\Message\ResponseInterface;

/**
 * Исключение для ошибок валидации параметров запроса.
 */
final class ValidationException extends DadataException
{
    /**
     * @param string $message Сообщение об ошибке
     * @param int $statusCode HTTP-статус код
     * @param string $responseBody Тело ответа от API
     * @param list<string> $errors Список ошибок валидации
     */
    public function __construct(
        string $message,
        private readonly int $statusCode = 0,
        private readonly string $responseBody = '',
        private readonly array $errors = [],
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

        // Пытаемся распарсить JSON для получения списка ошибок
        $errors      = [];
        $decodedBody = json_decode($body, true);
        if (is_array($decodedBody) && isset($decodedBody['errors']) && is_array($decodedBody['errors'])) {
            // Фильтруем только строковые значения для соответствия типу list<string>
            $errors = array_values(array_filter($decodedBody['errors'], 'is_string'));
        }

        $message = "Ошибка валидации параметров запроса (HTTP {$statusCode})";
        if (count($errors) > 0) {
            $message .= ': ' . implode(', ', $errors);
        }

        return new self(
            message: $message,
            statusCode: $statusCode,
            responseBody: $body,
            errors: $errors,
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
     * Возвращает список ошибок валидации.
     *
     * @return list<string>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}

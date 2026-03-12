<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\Tests\Unit\Exceptions;

use Ex3mm\Dadata\Exceptions\DadataException;
use Ex3mm\Dadata\Exceptions\ValidationException;
use Ex3mm\Dadata\Tests\TestCase;
use GuzzleHttp\Psr7\Response;

/**
 * Unit-тесты для ValidationException.
 *
 * Validates Requirements: 3.1, 3.8, 4.1, 4.2, 4.5, 4.8
 */
final class ValidationExceptionTest extends TestCase
{
    /**
     * Тест создания ValidationException через статический метод fromResponse().
     *
     * Validates: Requirement 4.5
     */
    public function test_creates_from_response(): void
    {
        $response = new Response(400, [], '{"errors": ["Invalid query parameter"]}');

        $exception = ValidationException::fromResponse($response);

        $this->assertInstanceOf(ValidationException::class, $exception);
        $this->assertInstanceOf(DadataException::class, $exception);
    }

    /**
     * Тест сохранения HTTP-статус кода.
     *
     * Validates: Requirement 4.2, 4.8
     */
    public function test_stores_status_code(): void
    {
        $response = new Response(400, [], '{}');

        $exception = ValidationException::fromResponse($response);

        $this->assertSame(400, $exception->getStatusCode());
    }

    /**
     * Тест сохранения тела ответа.
     *
     * Validates: Requirement 4.8
     */
    public function test_stores_response_body(): void
    {
        $body     = '{"errors": ["Invalid query"]}';
        $response = new Response(400, [], $body);

        $exception = ValidationException::fromResponse($response);

        $this->assertSame($body, $exception->getResponseBody());
    }

    /**
     * Тест извлечения списка ошибок из JSON.
     *
     * Validates: Requirement 4.2
     */
    public function test_extracts_errors_from_json_response(): void
    {
        $response = new Response(400, [], '{"errors": ["Error 1", "Error 2"]}');

        $exception = ValidationException::fromResponse($response);

        $errors = $exception->getErrors();
        $this->assertCount(2, $errors);
        $this->assertContains('Error 1', $errors);
        $this->assertContains('Error 2', $errors);
    }

    /**
     * Тест обработки ответа без списка ошибок.
     *
     * Validates: Requirement 4.2
     */
    public function test_handles_response_without_errors_array(): void
    {
        $response = new Response(400, [], '{"message": "Bad Request"}');

        $exception = ValidationException::fromResponse($response);

        $this->assertEmpty($exception->getErrors());
    }

    /**
     * Тест включения ошибок в сообщение исключения.
     *
     * Validates: Requirement 4.2
     */
    public function test_includes_errors_in_message(): void
    {
        $response = new Response(400, [], '{"errors": ["Invalid query", "Missing parameter"]}');

        $exception = ValidationException::fromResponse($response);

        $message = $exception->getMessage();
        $this->assertStringContainsString('Invalid query', $message);
        $this->assertStringContainsString('Missing parameter', $message);
    }

    /**
     * Тест обработки статуса 422.
     *
     * Validates: Requirement 4.2
     */
    public function test_handles_422_status_code(): void
    {
        $response = new Response(422, [], '{"errors": ["Unprocessable Entity"]}');

        $exception = ValidationException::fromResponse($response);

        $this->assertSame(422, $exception->getStatusCode());
    }

    /**
     * Тест что ValidationException наследует DadataException.
     *
     * Validates: Requirement 4.1
     */
    public function test_extends_dadata_exception(): void
    {
        $response = new Response(400, [], '{}');

        $exception = ValidationException::fromResponse($response);

        $this->assertInstanceOf(DadataException::class, $exception);
        $this->assertInstanceOf(\RuntimeException::class, $exception);
    }
}

<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\Tests\Unit\Exceptions;

use Ex3mm\Dadata\Exceptions\ApiException;
use Ex3mm\Dadata\Exceptions\DadataException;
use Ex3mm\Dadata\Tests\TestCase;
use GuzzleHttp\Psr7\Response;

/**
 * Unit-тесты для ApiException.
 *
 * Validates Requirements: 3.1, 3.8, 4.1, 4.2, 4.5, 4.8
 */
final class ApiExceptionTest extends TestCase
{
    /**
     * Тест создания ApiException через статический метод fromResponse().
     *
     * Validates: Requirement 4.5
     */
    public function test_creates_from_response(): void
    {
        $response = new Response(500, [], '{"error": "Internal Server Error"}');

        $exception = ApiException::fromResponse($response);

        $this->assertInstanceOf(ApiException::class, $exception);
        $this->assertInstanceOf(DadataException::class, $exception);
    }

    /**
     * Тест сохранения HTTP-статус кода.
     *
     * Validates: Requirement 4.8
     */
    public function test_stores_status_code(): void
    {
        $response = new Response(500, [], '{"error": "Internal Server Error"}');

        $exception = ApiException::fromResponse($response);

        $this->assertSame(500, $exception->getStatusCode());
    }

    /**
     * Тест сохранения тела ответа.
     *
     * Validates: Requirement 4.8
     */
    public function test_stores_response_body(): void
    {
        $body     = '{"error": "Internal Server Error"}';
        $response = new Response(500, [], $body);

        $exception = ApiException::fromResponse($response);

        $this->assertStringContainsString('Internal Server Error', $exception->getRawResponse());
    }

    /**
     * Тест корректного сообщения об ошибке.
     *
     * Validates: Requirement 4.2
     */
    public function test_contains_correct_error_message(): void
    {
        $response = new Response(500, [], '{"error": "Internal Server Error"}');

        $exception = ApiException::fromResponse($response);

        $this->assertStringContainsString('DaData API вернул ошибку', $exception->getMessage());
        $this->assertStringContainsString('500', $exception->getMessage());
    }

    /**
     * Тест что ApiException наследует DadataException.
     *
     * Validates: Requirement 4.1
     */
    public function test_extends_dadata_exception(): void
    {
        $response = new Response(500, [], '{}');

        $exception = ApiException::fromResponse($response);

        $this->assertInstanceOf(DadataException::class, $exception);
        $this->assertInstanceOf(\RuntimeException::class, $exception);
    }

    /**
     * Тест маскировки чувствительных данных в ответе.
     *
     * Validates: Requirement 4.8
     */
    public function test_sanitizes_sensitive_data_in_response(): void
    {
        $body     = '{"api_key": "secret_key_12345", "token": "Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9"}';
        $response = new Response(400, [], $body);

        $exception = ApiException::fromResponse($response);

        $rawResponse = $exception->getRawResponse();
        $this->assertStringNotContainsString('secret_key_12345', $rawResponse);
        $this->assertStringNotContainsString('eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9', $rawResponse);
        $this->assertStringContainsString('***', $rawResponse);
    }

    /**
     * Тест обработки различных HTTP статусов.
     *
     * Validates: Requirement 4.2
     */
    public function test_handles_different_http_statuses(): void
    {
        $statuses = [400, 404, 500, 502, 503];

        foreach ($statuses as $status) {
            $response  = new Response($status, [], '{"error": "Error"}');
            $exception = ApiException::fromResponse($response);

            $this->assertSame($status, $exception->getStatusCode());
            $this->assertStringContainsString((string) $status, $exception->getMessage());
        }
    }
}

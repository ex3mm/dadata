<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\Tests\Unit\Exceptions;

use Ex3mm\Dadata\Exceptions\DadataException;
use Ex3mm\Dadata\Exceptions\RateLimitException;
use Ex3mm\Dadata\Tests\TestCase;
use GuzzleHttp\Psr7\Response;

/**
 * Unit-тесты для RateLimitException.
 *
 * Validates Requirements: 3.1, 3.8, 4.1, 4.3, 4.5, 4.8
 */
final class RateLimitExceptionTest extends TestCase
{
    /**
     * Тест создания RateLimitException через статический метод fromResponse().
     *
     * Validates: Requirement 4.5
     */
    public function test_creates_from_response(): void
    {
        $response = new Response(429, [], '{"error": "Too Many Requests"}');

        $exception = RateLimitException::fromResponse($response);

        $this->assertInstanceOf(RateLimitException::class, $exception);
        $this->assertInstanceOf(DadataException::class, $exception);
    }

    /**
     * Тест сохранения HTTP-статус кода 429.
     *
     * Validates: Requirement 4.3, 4.8
     */
    public function test_stores_status_code_429(): void
    {
        $response = new Response(429, [], '{}');

        $exception = RateLimitException::fromResponse($response);

        $this->assertSame(429, $exception->getStatusCode());
    }

    /**
     * Тест сохранения тела ответа.
     *
     * Validates: Requirement 4.8
     */
    public function test_stores_response_body(): void
    {
        $body     = '{"error": "Too Many Requests"}';
        $response = new Response(429, [], $body);

        $exception = RateLimitException::fromResponse($response);

        $this->assertSame($body, $exception->getResponseBody());
    }

    /**
     * Тест извлечения Retry-After из заголовков.
     *
     * Validates: Requirement 4.3
     */
    public function test_extracts_retry_after_from_headers(): void
    {
        $response = new Response(429, ['Retry-After' => '60'], '{}');

        $exception = RateLimitException::fromResponse($response);

        $this->assertSame(60, $exception->getRetryAfter());
        $this->assertStringContainsString('60 секунд', $exception->getMessage());
    }

    /**
     * Тест обработки отсутствия заголовка Retry-After.
     *
     * Validates: Requirement 4.3
     */
    public function test_handles_missing_retry_after_header(): void
    {
        $response = new Response(429, [], '{}');

        $exception = RateLimitException::fromResponse($response);

        $this->assertSame(0, $exception->getRetryAfter());
        $this->assertStringContainsString('Превышен лимит запросов к API', $exception->getMessage());
    }

    /**
     * Тест корректного сообщения об ошибке.
     *
     * Validates: Requirement 4.3
     */
    public function test_contains_correct_error_message(): void
    {
        $response = new Response(429, [], '{}');

        $exception = RateLimitException::fromResponse($response);

        $this->assertStringContainsString('Превышен лимит запросов', $exception->getMessage());
    }

    /**
     * Тест что RateLimitException наследует DadataException.
     *
     * Validates: Requirement 4.1
     */
    public function test_extends_dadata_exception(): void
    {
        $response = new Response(429, [], '{}');

        $exception = RateLimitException::fromResponse($response);

        $this->assertInstanceOf(DadataException::class, $exception);
        $this->assertInstanceOf(\RuntimeException::class, $exception);
    }
}

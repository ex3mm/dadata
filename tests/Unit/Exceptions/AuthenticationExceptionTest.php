<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\Tests\Unit\Exceptions;

use Ex3mm\Dadata\Exceptions\AuthenticationException;
use Ex3mm\Dadata\Exceptions\DadataException;
use Ex3mm\Dadata\Tests\TestCase;

/**
 * Unit-тесты для AuthenticationException.
 *
 * Validates Requirements: 6.1, 6.2, 6.3, 6.4, 6.5, 6.6
 */
final class AuthenticationExceptionTest extends TestCase
{
    /**
     * Тест создания AuthenticationException через статический метод invalidApiKey().
     *
     * Validates: Requirement 6.2, 6.4
     */
    public function test_creates_invalid_api_key_exception(): void
    {
        $exception = AuthenticationException::invalidApiKey();

        $this->assertInstanceOf(AuthenticationException::class, $exception);
        $this->assertInstanceOf(DadataException::class, $exception);
    }

    /**
     * Тест корректного сообщения для invalidApiKey().
     *
     * Validates: Requirement 6.2, 6.4
     */
    public function test_invalid_api_key_contains_correct_message(): void
    {
        $exception = AuthenticationException::invalidApiKey();

        $this->assertStringContainsString(
            'Невалидный API-ключ',
            $exception->getMessage()
        );
        $this->assertStringContainsString(
            'dadata.api_key',
            $exception->getMessage()
        );
    }

    /**
     * Тест корректного кода 401 для invalidApiKey().
     *
     * Validates: Requirement 6.2, 6.4
     */
    public function test_invalid_api_key_has_401_code(): void
    {
        $exception = AuthenticationException::invalidApiKey();

        $this->assertSame(401, $exception->getCode());
    }

    /**
     * Тест создания AuthenticationException через статический метод forbidden().
     *
     * Validates: Requirement 6.3, 6.5
     */
    public function test_creates_forbidden_exception(): void
    {
        $exception = AuthenticationException::forbidden();

        $this->assertInstanceOf(AuthenticationException::class, $exception);
        $this->assertInstanceOf(DadataException::class, $exception);
    }

    /**
     * Тест корректного сообщения для forbidden().
     *
     * Validates: Requirement 6.3, 6.5
     */
    public function test_forbidden_contains_correct_message(): void
    {
        $exception = AuthenticationException::forbidden();

        $this->assertStringContainsString(
            'Доступ запрещён',
            $exception->getMessage()
        );
        $this->assertStringContainsString(
            'права API-ключа',
            $exception->getMessage()
        );
    }

    /**
     * Тест корректного кода 403 для forbidden().
     *
     * Validates: Requirement 6.3, 6.5
     */
    public function test_forbidden_has_403_code(): void
    {
        $exception = AuthenticationException::forbidden();

        $this->assertSame(403, $exception->getCode());
    }

    /**
     * Тест что AuthenticationException наследует DadataException.
     *
     * Validates: Requirement 6.1
     */
    public function test_extends_dadata_exception(): void
    {
        $exception = AuthenticationException::invalidApiKey();

        $this->assertInstanceOf(DadataException::class, $exception);
        $this->assertInstanceOf(\RuntimeException::class, $exception);
    }

    /**
     * Тест различия между invalidApiKey() и forbidden().
     *
     * Validates: Requirement 6.2, 6.3
     */
    public function test_invalid_api_key_and_forbidden_are_different(): void
    {
        $invalidApiKey = AuthenticationException::invalidApiKey();
        $forbidden     = AuthenticationException::forbidden();

        $this->assertNotSame($invalidApiKey->getCode(), $forbidden->getCode());
        $this->assertNotSame($invalidApiKey->getMessage(), $forbidden->getMessage());
        $this->assertSame(401, $invalidApiKey->getCode());
        $this->assertSame(403, $forbidden->getCode());
    }

    /**
     * Тест создания AuthenticationException через fromResponse() для 401.
     *
     * Validates: Requirement 4.2, 4.5
     */
    public function test_creates_from_response_401(): void
    {
        $response = new \GuzzleHttp\Psr7\Response(401, [], '{"error": "Unauthorized"}');

        $exception = AuthenticationException::fromResponse($response);

        $this->assertInstanceOf(AuthenticationException::class, $exception);
        $this->assertSame(401, $exception->getStatusCode());
        $this->assertStringContainsString('Невалидный API-ключ', $exception->getMessage());
    }

    /**
     * Тест создания AuthenticationException через fromResponse() для 403.
     *
     * Validates: Requirement 4.3, 4.5
     */
    public function test_creates_from_response_403(): void
    {
        $response = new \GuzzleHttp\Psr7\Response(403, [], '{"error": "Forbidden"}');

        $exception = AuthenticationException::fromResponse($response);

        $this->assertInstanceOf(AuthenticationException::class, $exception);
        $this->assertSame(403, $exception->getStatusCode());
        $this->assertStringContainsString('Доступ запрещён', $exception->getMessage());
    }

    /**
     * Тест сохранения тела ответа.
     *
     * Validates: Requirement 4.8
     */
    public function test_stores_response_body(): void
    {
        $body     = '{"error": "Unauthorized"}';
        $response = new \GuzzleHttp\Psr7\Response(401, [], $body);

        $exception = AuthenticationException::fromResponse($response);

        $this->assertSame($body, $exception->getResponseBody());
    }

    /**
     * Тест наличия методов getStatusCode() и getResponseBody().
     *
     * Validates: Requirement 4.8
     */
    public function test_has_status_code_and_response_body_getters(): void
    {
        $exception = AuthenticationException::invalidApiKey();

        $this->assertSame(401, $exception->getStatusCode());
        $this->assertSame('', $exception->getResponseBody());
    }
}

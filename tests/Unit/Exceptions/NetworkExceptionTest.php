<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\Tests\Unit\Exceptions;

use Ex3mm\Dadata\Exceptions\DadataException;
use Ex3mm\Dadata\Exceptions\NetworkException;
use Ex3mm\Dadata\Tests\TestCase;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;

/**
 * Unit-тесты для NetworkException.
 *
 * Validates Requirements: 5.1, 5.2, 5.4, 5.6
 */
final class NetworkExceptionTest extends TestCase
{
    /**
     * Тест создания NetworkException через статический метод fromGuzzleException().
     *
     * Validates: Requirement 5.2
     */
    public function test_creates_from_guzzle_exception(): void
    {
        $request         = new Request('GET', 'https://suggestions.dadata.ru/suggestions/api/4_1/rs/suggest/address');
        $guzzleException = new ConnectException(
            'Connection refused',
            $request
        );

        $networkException = NetworkException::fromGuzzleException($guzzleException);

        $this->assertInstanceOf(NetworkException::class, $networkException);
        $this->assertInstanceOf(DadataException::class, $networkException);
    }

    /**
     * Тест сохранения оригинального GuzzleException в свойстве $previous.
     *
     * Validates: Requirement 5.4
     */
    public function test_stores_original_guzzle_exception_in_previous(): void
    {
        $request         = new Request('POST', 'https://suggestions.dadata.ru/suggestions/api/4_1/rs/suggest/address');
        $guzzleException = new ConnectException(
            'Connection refused',
            $request
        );

        $networkException = NetworkException::fromGuzzleException($guzzleException);

        $this->assertSame($guzzleException, $networkException->getPrevious());
        $this->assertInstanceOf(ConnectException::class, $networkException->getPrevious());
    }

    /**
     * Тест корректного сообщения "Сетевая ошибка при обращении к DaData API".
     *
     * Validates: Requirement 5.6
     */
    public function test_contains_correct_error_message(): void
    {
        $request         = new Request('GET', 'https://suggestions.dadata.ru/suggestions/api/4_1/rs/suggest/address');
        $guzzleException = new ConnectException(
            'Connection refused',
            $request
        );

        $networkException = NetworkException::fromGuzzleException($guzzleException);

        $this->assertStringContainsString(
            'Сетевая ошибка при обращении к DaData API',
            $networkException->getMessage()
        );
        $this->assertStringContainsString(
            'Connection refused',
            $networkException->getMessage()
        );
    }

    /**
     * Тест сохранения кода ошибки из оригинального исключения.
     *
     * Validates: Requirement 5.1, 5.2
     */
    public function test_preserves_error_code_from_guzzle_exception(): void
    {
        $request         = new Request('GET', 'https://suggestions.dadata.ru/suggestions/api/4_1/rs/suggest/address');
        $guzzleException = new ConnectException(
            'Connection timed out',
            $request
        );

        $networkException = NetworkException::fromGuzzleException($guzzleException);

        $this->assertSame($guzzleException->getCode(), $networkException->getCode());
    }

    /**
     * Тест создания NetworkException для различных типов сетевых ошибок.
     *
     * Validates: Requirement 5.1, 5.2, 5.4
     */
    public function test_handles_different_network_error_types(): void
    {
        $request = new Request('POST', 'https://suggestions.dadata.ru/suggestions/api/4_1/rs/suggest/address');

        // Timeout error
        $timeoutException = new RequestException(
            'cURL error 28: Operation timed out after 5000 milliseconds',
            $request
        );
        $networkException = NetworkException::fromGuzzleException($timeoutException);

        $this->assertStringContainsString('Сетевая ошибка при обращении к DaData API', $networkException->getMessage());
        $this->assertStringContainsString('timed out', $networkException->getMessage());
        $this->assertSame($timeoutException, $networkException->getPrevious());

        // Connection refused
        $connectionException = new ConnectException(
            'cURL error 7: Failed to connect to suggestions.dadata.ru port 443: Connection refused',
            $request
        );
        $networkException = NetworkException::fromGuzzleException($connectionException);

        $this->assertStringContainsString('Сетевая ошибка при обращении к DaData API', $networkException->getMessage());
        $this->assertStringContainsString('Connection refused', $networkException->getMessage());
        $this->assertSame($connectionException, $networkException->getPrevious());

        // DNS resolution failure
        $dnsException = new RequestException(
            'cURL error 6: Could not resolve host: suggestions.dadata.ru',
            $request
        );
        $networkException = NetworkException::fromGuzzleException($dnsException);

        $this->assertStringContainsString('Сетевая ошибка при обращении к DaData API', $networkException->getMessage());
        $this->assertStringContainsString('Could not resolve host', $networkException->getMessage());
        $this->assertSame($dnsException, $networkException->getPrevious());
    }

    /**
     * Тест что NetworkException наследует DadataException.
     *
     * Validates: Requirement 5.1
     */
    public function test_extends_dadata_exception(): void
    {
        $request         = new Request('GET', 'https://suggestions.dadata.ru/suggestions/api/4_1/rs/suggest/address');
        $guzzleException = new ConnectException('Network error', $request);

        $networkException = NetworkException::fromGuzzleException($guzzleException);

        $this->assertInstanceOf(DadataException::class, $networkException);
        $this->assertInstanceOf(\RuntimeException::class, $networkException);
    }

    /**
     * Тест наличия методов getStatusCode() и getResponseBody().
     *
     * Validates: Requirement 4.8
     */
    public function test_has_status_code_and_response_body_getters(): void
    {
        $request         = new Request('GET', 'https://suggestions.dadata.ru/suggestions/api/4_1/rs/suggest/address');
        $guzzleException = new ConnectException('Network error', $request);

        $networkException = NetworkException::fromGuzzleException($guzzleException);

        $this->assertSame(0, $networkException->getStatusCode());
        $this->assertSame('', $networkException->getResponseBody());
    }
}

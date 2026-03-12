<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\Tests\Unit\Exceptions;

use Ex3mm\Dadata\Exceptions\ConfigurationException;
use Ex3mm\Dadata\Exceptions\DadataException;
use Ex3mm\Dadata\Tests\TestCase;

/**
 * Unit-тесты для ConfigurationException.
 *
 * Validates Requirements: 3.1, 4.1, 8.7
 */
final class ConfigurationExceptionTest extends TestCase
{
    /**
     * Тест создания ConfigurationException.
     *
     * Validates: Requirement 4.1
     */
    public function test_creates_configuration_exception(): void
    {
        $exception = new ConfigurationException('Invalid configuration');

        $this->assertInstanceOf(ConfigurationException::class, $exception);
        $this->assertInstanceOf(DadataException::class, $exception);
    }

    /**
     * Тест что ConfigurationException наследует DadataException.
     *
     * Validates: Requirement 4.1
     */
    public function test_extends_dadata_exception(): void
    {
        $exception = new ConfigurationException('Test message');

        $this->assertInstanceOf(DadataException::class, $exception);
        $this->assertInstanceOf(\RuntimeException::class, $exception);
    }

    /**
     * Тест сохранения сообщения об ошибке.
     *
     * Validates: Requirement 8.7
     */
    public function test_stores_error_message(): void
    {
        $message   = 'DaData API key is required';
        $exception = new ConfigurationException($message);

        $this->assertSame($message, $exception->getMessage());
    }

    /**
     * Тест создания с кодом ошибки.
     *
     * Validates: Requirement 4.1
     */
    public function test_creates_with_error_code(): void
    {
        $exception = new ConfigurationException('Invalid config', 100);

        $this->assertSame(100, $exception->getCode());
    }
}

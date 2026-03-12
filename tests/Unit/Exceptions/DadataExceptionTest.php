<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\Tests\Unit\Exceptions;

use Ex3mm\Dadata\Exceptions\DadataException;
use Ex3mm\Dadata\Tests\TestCase;

/**
 * Unit-тесты для базового DadataException.
 *
 * Validates Requirements: 3.1, 4.1
 */
final class DadataExceptionTest extends TestCase
{
    /**
     * Тест создания базового DadataException.
     *
     * Validates: Requirement 4.1
     */
    public function test_creates_dadata_exception(): void
    {
        $exception = new DadataException('Test error');

        $this->assertInstanceOf(DadataException::class, $exception);
        $this->assertInstanceOf(\RuntimeException::class, $exception);
    }

    /**
     * Тест что DadataException наследует RuntimeException.
     *
     * Validates: Requirement 4.1
     */
    public function test_extends_runtime_exception(): void
    {
        $exception = new DadataException('Test error');

        $this->assertInstanceOf(\RuntimeException::class, $exception);
        $this->assertInstanceOf(\Exception::class, $exception);
    }

    /**
     * Тест сохранения сообщения об ошибке.
     *
     * Validates: Requirement 4.1
     */
    public function test_stores_error_message(): void
    {
        $message   = 'Test error message';
        $exception = new DadataException($message);

        $this->assertSame($message, $exception->getMessage());
    }

    /**
     * Тест создания с кодом ошибки.
     *
     * Validates: Requirement 4.1
     */
    public function test_creates_with_error_code(): void
    {
        $exception = new DadataException('Test error', 500);

        $this->assertSame(500, $exception->getCode());
    }

    /**
     * Тест создания с предыдущим исключением.
     *
     * Validates: Requirement 4.1
     */
    public function test_creates_with_previous_exception(): void
    {
        $previous  = new \RuntimeException('Previous error');
        $exception = new DadataException('Test error', 0, $previous);

        $this->assertSame($previous, $exception->getPrevious());
    }
}

<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\Tests\Unit\Config;

use Ex3mm\Dadata\Config\HttpConfig;
use Ex3mm\Dadata\Exceptions\ConfigurationException;
use PHPUnit\Framework\TestCase;

/**
 * Тесты для HttpConfig.
 */
final class HttpConfigTest extends TestCase
{
    public function test_creates_with_default_values(): void
    {
        $config = HttpConfig::fromArray([]);

        $this->assertSame(10, $config->connectTimeout);
        $this->assertSame(30, $config->timeout);
        $this->assertSame(3, $config->retryAttempts);
        $this->assertSame(100, $config->retryDelay);
    }

    public function test_creates_with_custom_values(): void
    {
        $config = HttpConfig::fromArray([
            'connect_timeout' => 15,
            'timeout'         => 45,
            'retry_attempts'  => 5,
            'retry_delay'     => 200,
        ]);

        $this->assertSame(15, $config->connectTimeout);
        $this->assertSame(45, $config->timeout);
        $this->assertSame(5, $config->retryAttempts);
        $this->assertSame(200, $config->retryDelay);
    }

    public function test_throws_exception_for_negative_connect_timeout(): void
    {
        $this->expectException(ConfigurationException::class);
        $this->expectExceptionMessage('Таймаут подключения (connect_timeout) должен быть положительным числом');

        HttpConfig::fromArray(['connect_timeout' => -1]);
    }

    public function test_throws_exception_for_zero_connect_timeout(): void
    {
        $this->expectException(ConfigurationException::class);
        $this->expectExceptionMessage('Таймаут подключения (connect_timeout) должен быть положительным числом');

        HttpConfig::fromArray(['connect_timeout' => 0]);
    }

    public function test_throws_exception_for_negative_timeout(): void
    {
        $this->expectException(ConfigurationException::class);
        $this->expectExceptionMessage('Таймаут запроса (timeout) должен быть положительным числом');

        HttpConfig::fromArray(['timeout' => -1]);
    }

    public function test_throws_exception_for_zero_timeout(): void
    {
        $this->expectException(ConfigurationException::class);
        $this->expectExceptionMessage('Таймаут запроса (timeout) должен быть положительным числом');

        HttpConfig::fromArray(['timeout' => 0]);
    }

    public function test_throws_exception_for_negative_retry_attempts(): void
    {
        $this->expectException(ConfigurationException::class);
        $this->expectExceptionMessage('Количество повторных попыток (retry_attempts) не может быть отрицательным');

        HttpConfig::fromArray(['retry_attempts' => -1]);
    }

    public function test_allows_zero_retry_attempts(): void
    {
        $config = HttpConfig::fromArray(['retry_attempts' => 0]);

        $this->assertSame(0, $config->retryAttempts);
    }

    public function test_throws_exception_for_negative_retry_delay(): void
    {
        $this->expectException(ConfigurationException::class);
        $this->expectExceptionMessage('Задержка между попытками (retry_delay) должна быть положительным числом');

        HttpConfig::fromArray(['retry_delay' => -1]);
    }

    public function test_throws_exception_for_zero_retry_delay(): void
    {
        $this->expectException(ConfigurationException::class);
        $this->expectExceptionMessage('Задержка между попытками (retry_delay) должна быть положительным числом');

        HttpConfig::fromArray(['retry_delay' => 0]);
    }
}

<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\Tests\Unit\Config;

use Ex3mm\Dadata\Config\RateLimitConfig;
use Ex3mm\Dadata\Exceptions\ConfigurationException;
use PHPUnit\Framework\TestCase;

/**
 * Тесты для RateLimitConfig.
 */
final class RateLimitConfigTest extends TestCase
{
    public function test_creates_with_default_values(): void
    {
        $config = RateLimitConfig::fromArray([]);

        $this->assertTrue($config->enabled);
        $this->assertSame(20, $config->limit);
    }

    public function test_creates_with_custom_values(): void
    {
        $config = RateLimitConfig::fromArray([
            'rate_limit_enabled' => false,
            'rate_limit'         => 10,
        ]);

        $this->assertFalse($config->enabled);
        $this->assertSame(10, $config->limit);
    }

    public function test_throws_exception_for_negative_limit(): void
    {
        $this->expectException(ConfigurationException::class);
        $this->expectExceptionMessage('Лимит запросов (rate_limit) должен быть положительным числом');

        RateLimitConfig::fromArray(['rate_limit' => -1]);
    }

    public function test_throws_exception_for_zero_limit(): void
    {
        $this->expectException(ConfigurationException::class);
        $this->expectExceptionMessage('Лимит запросов (rate_limit) должен быть положительным числом');

        RateLimitConfig::fromArray(['rate_limit' => 0]);
    }
}

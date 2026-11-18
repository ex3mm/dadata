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
        $this->assertSame(30, $config->suggestionsLimit);
        $this->assertSame(20, $config->cleanerLimit);
    }

    public function test_creates_with_separate_custom_limits(): void
    {
        $config = RateLimitConfig::fromArray([
            'rate_limit_enabled'     => false,
            'rate_limit_suggestions' => 25,
            'rate_limit_cleaner'     => 15,
        ]);

        $this->assertFalse($config->enabled);
        $this->assertSame(25, $config->suggestionsLimit);
        $this->assertSame(15, $config->cleanerLimit);
    }

    public function test_throws_exception_for_non_positive_suggestions_limit(): void
    {
        $this->expectException(ConfigurationException::class);
        $this->expectExceptionMessage('Лимит запросов Suggestions (rate_limit_suggestions) должен быть положительным числом');

        RateLimitConfig::fromArray(['rate_limit_suggestions' => 0]);
    }

    public function test_throws_exception_for_non_positive_cleaner_limit(): void
    {
        $this->expectException(ConfigurationException::class);
        $this->expectExceptionMessage('Лимит запросов Cleaner (rate_limit_cleaner) должен быть положительным числом');

        RateLimitConfig::fromArray(['rate_limit_cleaner' => 0]);
    }
}

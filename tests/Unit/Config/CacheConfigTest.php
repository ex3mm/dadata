<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\Tests\Unit\Config;

use Ex3mm\Dadata\Config\CacheConfig;
use Ex3mm\Dadata\Exceptions\ConfigurationException;
use PHPUnit\Framework\TestCase;

/**
 * Тесты для CacheConfig.
 */
final class CacheConfigTest extends TestCase
{
    public function test_creates_with_default_values(): void
    {
        $config = CacheConfig::fromArray([]);

        $this->assertTrue($config->enabled);
        $this->assertSame(3600, $config->ttl);
        $this->assertNull($config->store);
    }

    public function test_creates_with_custom_values(): void
    {
        $config = CacheConfig::fromArray([
            'cache_enabled' => false,
            'cache_ttl'     => 7200,
            'cache_store'   => 'redis',
        ]);

        $this->assertFalse($config->enabled);
        $this->assertSame(7200, $config->ttl);
        $this->assertSame('redis', $config->store);
    }

    public function test_throws_exception_for_negative_ttl(): void
    {
        $this->expectException(ConfigurationException::class);
        $this->expectExceptionMessage('Время жизни кеша (cache_ttl) должно быть положительным числом');

        CacheConfig::fromArray(['cache_ttl' => -1]);
    }

    public function test_throws_exception_for_zero_ttl(): void
    {
        $this->expectException(ConfigurationException::class);
        $this->expectExceptionMessage('Время жизни кеша (cache_ttl) должно быть положительным числом');

        CacheConfig::fromArray(['cache_ttl' => 0]);
    }
}

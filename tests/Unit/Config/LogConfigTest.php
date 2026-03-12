<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\Tests\Unit\Config;

use Ex3mm\Dadata\Config\LogConfig;
use PHPUnit\Framework\TestCase;

/**
 * Тесты для LogConfig.
 */
final class LogConfigTest extends TestCase
{
    public function test_creates_with_default_values(): void
    {
        $config = LogConfig::fromArray([]);

        $this->assertSame('info', $config->level);
        $this->assertFalse($config->requestBody);
        $this->assertFalse($config->responseBody);
        $this->assertNull($config->channel);
    }

    public function test_creates_with_custom_values(): void
    {
        $config = LogConfig::fromArray([
            'log_level'         => 'debug',
            'log_request_body'  => true,
            'log_response_body' => true,
            'log_channel'       => 'dadata',
        ]);

        $this->assertSame('debug', $config->level);
        $this->assertTrue($config->requestBody);
        $this->assertTrue($config->responseBody);
        $this->assertSame('dadata', $config->channel);
    }
}

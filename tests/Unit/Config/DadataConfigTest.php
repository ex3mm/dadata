<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\Tests\Unit\Config;

use Ex3mm\Dadata\Config\CacheConfig;
use Ex3mm\Dadata\Config\DadataConfig;
use Ex3mm\Dadata\Config\HttpConfig;
use Ex3mm\Dadata\Config\LogConfig;
use Ex3mm\Dadata\Config\RateLimitConfig;
use Ex3mm\Dadata\Exceptions\ConfigurationException;
use Ex3mm\Dadata\Tests\TestCase;

final class DadataConfigTest extends TestCase
{
    public function test_from_array_creates_valid_config(): void
    {
        $config = DadataConfig::fromArray([
            'api_key'    => 'test_api_key',
            'secret_key' => 'test_secret_key',
        ]);

        $this->assertSame('test_api_key', $config->apiKey);
        $this->assertSame('test_secret_key', $config->secretKey);
        $this->assertSame(DadataConfig::CLEANER_BASE_URL, $config->baseUrlCleaner);
        $this->assertSame(DadataConfig::SUGGESTIONS_BASE_URL, $config->baseUrlSuggestions);
        $this->assertInstanceOf(HttpConfig::class, $config->http);
        $this->assertInstanceOf(CacheConfig::class, $config->cache);
        $this->assertInstanceOf(LogConfig::class, $config->log);
        $this->assertInstanceOf(RateLimitConfig::class, $config->rateLimit);
    }

    public function test_empty_api_key_throws_exception(): void
    {
        $this->expectException(ConfigurationException::class);
        $this->expectExceptionMessage('API ключ (api_key) не может быть пустым');

        DadataConfig::fromArray([
            'api_key'    => '',
            'secret_key' => 'test_secret',
        ]);
    }

    public function test_missing_api_key_throws_exception(): void
    {
        $this->expectException(ConfigurationException::class);
        $this->expectExceptionMessage('API ключ (api_key) не может быть пустым');

        DadataConfig::fromArray([
            'secret_key' => 'test_secret',
        ]);
    }

    public function test_non_string_api_key_throws_exception(): void
    {
        $this->expectException(ConfigurationException::class);
        $this->expectExceptionMessage('API ключ (api_key) должен быть строкой, передан integer');

        DadataConfig::fromArray([
            'api_key'    => 123,
            'secret_key' => 'test_secret',
        ]);
    }

    public function test_empty_secret_key_throws_exception(): void
    {
        $this->expectException(ConfigurationException::class);
        $this->expectExceptionMessage('Секретный ключ (secret_key) не может быть пустым');

        DadataConfig::fromArray([
            'api_key'    => 'test_api',
            'secret_key' => '',
        ]);
    }

    public function test_missing_secret_key_throws_exception(): void
    {
        $this->expectException(ConfigurationException::class);
        $this->expectExceptionMessage('Секретный ключ (secret_key) не может быть пустым');

        DadataConfig::fromArray([
            'api_key' => 'test_api',
        ]);
    }

    public function test_non_string_secret_key_throws_exception(): void
    {
        $this->expectException(ConfigurationException::class);
        $this->expectExceptionMessage('Секретный ключ (secret_key) должен быть строкой, передан array');

        DadataConfig::fromArray([
            'api_key'    => 'test_api',
            'secret_key' => [],
        ]);
    }

    public function test_negative_retry_attempts_throws_exception(): void
    {
        $this->expectException(ConfigurationException::class);
        $this->expectExceptionMessage('Количество повторных попыток (retry_attempts) не может быть отрицательным');

        DadataConfig::fromArray([
            'api_key'        => 'test_api',
            'secret_key'     => 'test_secret',
            'retry_attempts' => -1,
        ]);
    }

    public function test_base_url_override_works(): void
    {
        $config = DadataConfig::fromArray([
            'api_key'              => 'test_api',
            'secret_key'           => 'test_secret',
            'base_url_cleaner'     => 'https://mock-cleaner.test',
            'base_url_suggestions' => 'https://mock-suggestions.test',
        ]);

        $this->assertSame('https://mock-cleaner.test', $config->baseUrlCleaner);
        $this->assertSame('https://mock-suggestions.test', $config->baseUrlSuggestions);
    }

    public function test_all_fields_are_correctly_typed(): void
    {
        $config = DadataConfig::fromArray([
            'api_key'            => 'test_api',
            'secret_key'         => 'test_secret',
            'connect_timeout'    => 15,
            'timeout'            => 45,
            'cache_enabled'      => false,
            'cache_ttl'          => 7200,
            'cache_store'        => 'redis',
            'log_level'          => 'debug',
            'log_request_body'   => true,
            'log_response_body'  => true,
            'log_channel'        => 'dadata',
            'rate_limit_enabled' => false,
            'rate_limit'         => 10,
            'retry_attempts'     => 5,
            'retry_delay'        => 200,
        ]);

        $this->assertSame(15, $config->http->connectTimeout);
        $this->assertSame(45, $config->http->timeout);
        $this->assertFalse($config->cache->enabled);
        $this->assertSame(7200, $config->cache->ttl);
        $this->assertSame('redis', $config->cache->store);
        $this->assertSame('debug', $config->log->level);
        $this->assertTrue($config->log->requestBody);
        $this->assertTrue($config->log->responseBody);
        $this->assertSame('dadata', $config->log->channel);
        $this->assertFalse($config->rateLimit->enabled);
        $this->assertSame(10, $config->rateLimit->limit);
        $this->assertSame(5, $config->http->retryAttempts);
        $this->assertSame(200, $config->http->retryDelay);
    }

    public function test_nested_config_objects_are_created(): void
    {
        $config = DadataConfig::fromArray([
            'api_key'    => 'test_api',
            'secret_key' => 'test_secret',
        ]);

        $this->assertInstanceOf(HttpConfig::class, $config->http);
        $this->assertInstanceOf(CacheConfig::class, $config->cache);
        $this->assertInstanceOf(LogConfig::class, $config->log);
        $this->assertInstanceOf(RateLimitConfig::class, $config->rateLimit);
    }
}

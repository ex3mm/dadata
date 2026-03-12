<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\Tests\Unit;

use Ex3mm\Dadata\Client\DadataClient;
use Ex3mm\Dadata\DadataFactory;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Psr\SimpleCache\CacheInterface;

final class DadataFactoryTest extends TestCase
{
    public function testCreateClientWithMinimalParams(): void
    {
        $client = DadataFactory::create('test-api-key', 'test-secret-key');

        $this->assertInstanceOf(DadataClient::class, $client);
    }

    public function testCreateClientWithCustomOptions(): void
    {
        $options = [
            'base_url_cleaner'     => 'https://custom-cleaner.example.com',
            'base_url_suggestions' => 'https://custom-suggestions.example.com',
            'connect_timeout'      => 5,
            'timeout'              => 15,
            'cache_enabled'        => false,
            'cache_ttl'            => 7200,
            'log_level'            => 'debug',
            'log_request_body'     => true,
            'log_response_body'    => true,
            'rate_limit_enabled'   => false,
            'rate_limit'           => 10,
            'retry_attempts'       => 5,
            'retry_delay'          => 200,
        ];

        $client = DadataFactory::create('test-api-key', 'test-secret-key', $options);

        $this->assertInstanceOf(DadataClient::class, $client);
    }

    public function testCreateClientWithCustomCache(): void
    {
        $cache = $this->createMock(CacheInterface::class);

        $client = DadataFactory::create('test-api-key', 'test-secret-key', [], $cache);

        $this->assertInstanceOf(DadataClient::class, $client);
    }

    public function testCreateClientWithCustomLogger(): void
    {
        $logger = $this->createMock(LoggerInterface::class);

        $client = DadataFactory::create('test-api-key', 'test-secret-key', [], null, $logger);

        $this->assertInstanceOf(DadataClient::class, $client);
    }
}

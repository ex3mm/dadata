<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\Tests\Unit\Client;

use Ex3mm\Dadata\Client\DadataClient;
use Ex3mm\Dadata\Client\GuzzleClientFactory;
use Ex3mm\Dadata\Config\DadataConfig;
use Ex3mm\Dadata\Tests\TestCase;
use GuzzleHttp\Client;
use Psr\Log\NullLogger;
use Psr\SimpleCache\CacheInterface;

final class DadataClientTest extends TestCase
{
    public function test_lazy_initialization_does_not_create_client_immediately(): void
    {
        $config = DadataConfig::fromArray(['api_key' => 'test', 'secret_key' => 'test']);
        $cache  = $this->createMock(CacheInterface::class);
        $logger = new NullLogger();

        // Используем реальную фабрику - клиент не должен быть создан при инициализации
        $factory = new GuzzleClientFactory($config);

        $client = new DadataClient($config, $factory, $logger, $cache);

        // Проверяем через рефлексию, что httpClient еще null
        $reflection = new \ReflectionClass($client);
        $property   = $reflection->getProperty('httpClient');

        $this->assertNull($property->getValue($client), 'HTTP client should not be created immediately');
    }

    public function test_get_client_creates_http_client_on_first_call(): void
    {
        $config = DadataConfig::fromArray(['api_key' => 'test', 'secret_key' => 'test']);
        $cache  = $this->createMock(CacheInterface::class);
        $logger = new NullLogger();

        // Используем реальную фабрику
        $factory = new GuzzleClientFactory($config);

        $client = new DadataClient($config, $factory, $logger, $cache);

        // Первый вызов создает клиент
        $result1 = $client->getClient();
        $this->assertInstanceOf(Client::class, $result1);

        // Второй вызов возвращает тот же экземпляр
        $result2 = $client->getClient();
        $this->assertSame($result1, $result2, 'Should return the same client instance');
    }
}

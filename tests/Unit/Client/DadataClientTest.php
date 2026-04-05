<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\Tests\Unit\Client;

use Ex3mm\Dadata\Client\DadataClient;
use Ex3mm\Dadata\Client\GuzzleClientFactory;
use Ex3mm\Dadata\Config\DadataConfig;
use Ex3mm\Dadata\Requests\CleanAddressRequest;
use Ex3mm\Dadata\Requests\CustomRequest;
use Ex3mm\Dadata\Requests\FindAffiliatedRequest;
use Ex3mm\Dadata\Requests\FindBankRequest;
use Ex3mm\Dadata\Requests\FindPartyRequest;
use Ex3mm\Dadata\Requests\SuggestAddressRequest;
use Ex3mm\Dadata\Requests\SuggestBankRequest;
use Ex3mm\Dadata\Requests\SuggestPartyRequest;
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

    public function test_suggest_address_returns_request_builder(): void
    {
        $config  = DadataConfig::fromArray(['api_key' => 'test', 'secret_key' => 'test']);
        $cache   = $this->createMock(CacheInterface::class);
        $logger  = new NullLogger();
        $factory = new GuzzleClientFactory($config);

        $client = new DadataClient($config, $factory, $logger, $cache);

        $result = $client->suggestAddress();

        $this->assertInstanceOf(SuggestAddressRequest::class, $result);
    }

    public function test_custom_returns_request_builder(): void
    {
        $config  = DadataConfig::fromArray(['api_key' => 'test', 'secret_key' => 'test']);
        $cache   = $this->createMock(CacheInterface::class);
        $logger  = new NullLogger();
        $factory = new GuzzleClientFactory($config);

        $client = new DadataClient($config, $factory, $logger, $cache);

        $result = $client->custom();

        $this->assertInstanceOf(CustomRequest::class, $result);
    }

    public function test_clean_address_returns_request_builder(): void
    {
        $config  = DadataConfig::fromArray(['api_key' => 'test', 'secret_key' => 'test']);
        $cache   = $this->createMock(CacheInterface::class);
        $logger  = new NullLogger();
        $factory = new GuzzleClientFactory($config);

        $client = new DadataClient($config, $factory, $logger, $cache);

        $result = $client->cleanAddress();

        $this->assertInstanceOf(CleanAddressRequest::class, $result);
    }

    public function test_suggest_bank_returns_request_builder(): void
    {
        $config  = DadataConfig::fromArray(['api_key' => 'test', 'secret_key' => 'test']);
        $cache   = $this->createMock(CacheInterface::class);
        $logger  = new NullLogger();
        $factory = new GuzzleClientFactory($config);

        $client = new DadataClient($config, $factory, $logger, $cache);

        $result = $client->suggestBank();

        $this->assertInstanceOf(SuggestBankRequest::class, $result);
    }

    public function test_suggest_party_returns_request_builder(): void
    {
        $config  = DadataConfig::fromArray(['api_key' => 'test', 'secret_key' => 'test']);
        $cache   = $this->createMock(CacheInterface::class);
        $logger  = new NullLogger();
        $factory = new GuzzleClientFactory($config);

        $client = new DadataClient($config, $factory, $logger, $cache);

        $result = $client->suggestParty();

        $this->assertInstanceOf(SuggestPartyRequest::class, $result);
    }

    public function test_find_affiliated_returns_request_builder(): void
    {
        $config  = DadataConfig::fromArray(['api_key' => 'test', 'secret_key' => 'test']);
        $cache   = $this->createMock(CacheInterface::class);
        $logger  = new NullLogger();
        $factory = new GuzzleClientFactory($config);

        $client = new DadataClient($config, $factory, $logger, $cache);

        $result = $client->findAffiliated();

        $this->assertInstanceOf(FindAffiliatedRequest::class, $result);
    }

    public function test_find_bank_returns_request_builder(): void
    {
        $config  = DadataConfig::fromArray(['api_key' => 'test', 'secret_key' => 'test']);
        $cache   = $this->createMock(CacheInterface::class);
        $logger  = new NullLogger();
        $factory = new GuzzleClientFactory($config);

        $client = new DadataClient($config, $factory, $logger, $cache);

        $result = $client->findBank();

        $this->assertInstanceOf(FindBankRequest::class, $result);
    }

    public function test_find_party_returns_request_builder(): void
    {
        $config  = DadataConfig::fromArray(['api_key' => 'test', 'secret_key' => 'test']);
        $cache   = $this->createMock(CacheInterface::class);
        $logger  = new NullLogger();
        $factory = new GuzzleClientFactory($config);

        $client = new DadataClient($config, $factory, $logger, $cache);

        $result = $client->findParty();

        $this->assertInstanceOf(FindPartyRequest::class, $result);
    }
}

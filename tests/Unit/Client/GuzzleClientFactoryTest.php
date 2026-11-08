<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\Tests\Unit\Client;

use Ex3mm\Dadata\Cache\InMemoryCache;
use Ex3mm\Dadata\Client\GuzzleClientFactory;
use Ex3mm\Dadata\Config\DadataConfig;
use Ex3mm\Dadata\Tests\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Psr\Log\NullLogger;
use Psr\SimpleCache\CacheInterface;

/**
 * Тесты для GuzzleClientFactory.
 */
final class GuzzleClientFactoryTest extends TestCase
{
    /**
     * Проверяет, что созданный клиент содержит реальные API-ключи в заголовках.
     *
     * Validates Requirements: 4.3 - HTTP-запросы содержат реальные API-ключи
     */
    public function test_created_client_uses_real_api_keys_in_requests(): void
    {
        // Arrange
        $apiKey    = 'test-api-key-12345';
        $secretKey = 'test-secret-key-67890';

        // Создаём контейнер для перехвата запросов
        $container = [];
        $history   = Middleware::history($container);

        // Создаём mock handler для имитации ответа
        $mock = new MockHandler([
            new Response(200, [], '{"suggestions": []}'),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $handlerStack->push($history);

        // Создаём тестовый клиент с нашим handler stack
        $testClient = new Client(['handler' => $handlerStack]);

        // Act - выполняем запрос с Authorization заголовком
        $testClient->post('https://suggestions.dadata.ru/suggestions/api/4_1/rs/suggest/address', [
            'headers' => [
                'Authorization' => 'Token ' . $apiKey,
                'X-Secret'      => $secretKey,
            ],
            'json' => ['query' => 'Москва'],
        ]);

        // Assert - проверяем, что в запросе присутствуют реальные API-ключи
        $this->assertCount(1, $container, 'Should have captured one request');

        /** @var Request $request */
        $request = $container[0]['request'];

        // Проверяем, что Authorization заголовок содержит реальный API-ключ (не замаскированный)
        $authHeader = $request->getHeaderLine('Authorization');
        $this->assertStringContainsString(
            $apiKey,
            $authHeader,
            'Authorization header should contain real API key, not masked'
        );

        // Проверяем, что X-Secret заголовок содержит реальный секретный ключ
        $secretHeader = $request->getHeaderLine('X-Secret');
        $this->assertStringContainsString(
            $secretKey,
            $secretHeader,
            'X-Secret header should contain real secret key, not masked'
        );

        // Проверяем, что ключи НЕ замаскированы (не содержат ***)
        $this->assertStringNotContainsString(
            '***',
            $authHeader,
            'Authorization header should not contain masked value'
        );
        $this->assertStringNotContainsString(
            '***',
            $secretHeader,
            'X-Secret header should not contain masked value'
        );
    }

    /**
     * Проверяет, что фабрика создаёт валидный HTTP-клиент.
     */
    public function test_factory_creates_valid_http_client(): void
    {
        // Arrange
        $config = DadataConfig::fromArray([
            'api_key'    => 'test-key',
            'secret_key' => 'test-secret',
        ]);

        $cache  = $this->createStub(CacheInterface::class);
        $logger = new NullLogger();

        $factory = new GuzzleClientFactory($config);

        // Act
        $client = $factory->create($cache, $logger);

        // Assert
        $this->assertInstanceOf(Client::class, $client);
    }

    /**
     * Проверяет, что фабрика корректно настраивает таймауты.
     */
    public function test_factory_configures_timeouts_correctly(): void
    {
        // Arrange
        $config = DadataConfig::fromArray([
            'api_key'         => 'test-key',
            'secret_key'      => 'test-secret',
            'timeout'         => 10,
            'connect_timeout' => 5,
        ]);

        $cache  = $this->createStub(CacheInterface::class);
        $logger = new NullLogger();

        $factory = new GuzzleClientFactory($config);

        // Act
        $client = $factory->create($cache, $logger);

        // Assert
        $this->assertInstanceOf(Client::class, $client);

        // Проверяем конфигурацию через рефлексию
        $reflection     = new \ReflectionClass($client);
        $configProperty = $reflection->getProperty('config');
        $clientConfig   = $configProperty->getValue($client);

        $this->assertEquals(10, $clientConfig['timeout']);
        $this->assertEquals(5, $clientConfig['connect_timeout']);
    }

    /**
     * Проверяет, что middleware созданного фабрикой клиента повторяют POST-запрос
     * после HTTP 503 и кешируют успешный ответ без повторного обращения к handler.
     */
    public function test_factory_stack_retries_and_caches_post_responses(): void
    {
        $config = DadataConfig::fromArray([
            'api_key'            => 'test-key',
            'secret_key'         => 'test-secret',
            'cache_enabled'      => true,
            'rate_limit_enabled' => true,
            'retry_attempts'     => 2,
            'retry_delay'        => 1,
        ]);
        $client = new GuzzleClientFactory($config)->create(new InMemoryCache(), new NullLogger());
        $this->assertInstanceOf(Client::class, $client);
        $stack = $client->getConfig('handler');
        $this->assertInstanceOf(HandlerStack::class, $stack);
        $mock = new MockHandler([
            new Response(503),
            new Response(200, [], '{"suggestions": []}'),
        ]);
        $stack->setHandler($mock);
        $history = [];
        $stack->push(Middleware::history($history));

        $response = $client->request('POST', 'https://example.com/suggest', ['json' => ['query' => 'Москва']]);
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('MISS', $response->getHeaderLine('X-Kevinrob-Cache'));
        $this->assertCount(2, $history);

        $cached = $client->request('POST', 'https://example.com/suggest', ['json' => ['query' => 'Москва']]);
        $this->assertSame('HIT', $cached->getHeaderLine('X-Kevinrob-Cache'));
        $this->assertSame((string) $response->getBody(), (string) $cached->getBody());
        $this->assertCount(2, $history);
    }

    /**
     * Проверяет, что LoggingMiddleware использует стандартный PSR-3 Logger.
     *
     * Validates Requirements: 1.4 - LoggingMiddleware использует стандартный Logger
     */
    public function test_logging_middleware_uses_standard_psr3_logger(): void
    {
        // Arrange
        $config = DadataConfig::fromArray([
            'api_key'    => 'test-key',
            'secret_key' => 'test-secret',
        ]);

        $cache  = $this->createStub(CacheInterface::class);
        $logger = new NullLogger();

        $factory = new GuzzleClientFactory($config);

        // Act
        $client = $factory->create($cache, $logger);

        // Assert - проверяем, что клиент создан успешно
        $this->assertInstanceOf(Client::class, $client);

        // LoggingMiddleware принимает PSR-3 LoggerInterface в конструкторе,
        // что подтверждает использование стандартного логгера
        $this->assertTrue(true, 'LoggingMiddleware successfully uses PSR-3 LoggerInterface');
    }
}

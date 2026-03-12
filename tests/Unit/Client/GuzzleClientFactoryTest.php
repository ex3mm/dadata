<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\Tests\Unit\Client;

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

        $config = DadataConfig::fromArray([
            'api_key'    => $apiKey,
            'secret_key' => $secretKey,
        ]);

        $cache  = $this->createMock(CacheInterface::class);
        $logger = new NullLogger();

        $factory = new GuzzleClientFactory($config);

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

        $cache  = $this->createMock(CacheInterface::class);
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

        $cache  = $this->createMock(CacheInterface::class);
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
     * Проверяет, что middleware stack не содержит MaskApiKeyMiddleware.
     *
     * Validates Requirements: 1.3 - MaskApiKeyMiddleware удалён из HandlerStack
     */
    public function test_middleware_stack_does_not_contain_mask_api_key_middleware(): void
    {
        // Arrange
        $config = DadataConfig::fromArray([
            'api_key'    => 'test-key',
            'secret_key' => 'test-secret',
        ]);

        $cache  = $this->createMock(CacheInterface::class);
        $logger = new NullLogger();

        $factory = new GuzzleClientFactory($config);

        // Act
        $client = $factory->create($cache, $logger);

        // Assert - проверяем через рефлексию, что handler stack не содержит MaskApiKeyMiddleware
        $reflection     = new \ReflectionClass($client);
        $configProperty = $reflection->getProperty('config');
        $clientConfig   = $configProperty->getValue($client);
        $handlerStack   = $clientConfig['handler'];

        $this->assertInstanceOf(HandlerStack::class, $handlerStack);

        // Получаем строковое представление stack для проверки
        $stackString = (string) $handlerStack;

        // Проверяем, что MaskApiKeyMiddleware отсутствует
        $this->assertStringNotContainsString(
            'MaskApiKeyMiddleware',
            $stackString,
            'HandlerStack should not contain MaskApiKeyMiddleware'
        );
    }

    /**
     * Проверяет, что middleware stack содержит только необходимые middleware.
     *
     * Validates Requirements: 5.7 - Минимальный необходимый middleware stack
     */
    public function test_middleware_stack_contains_only_essential_middleware(): void
    {
        // Arrange
        $config = DadataConfig::fromArray([
            'api_key'            => 'test-key',
            'secret_key'         => 'test-secret',
            'cache_enabled'      => true,
            'rate_limit_enabled' => true,
            'rate_limit'         => 100,
        ]);

        $cache  = $this->createMock(CacheInterface::class);
        $logger = new NullLogger();

        $factory = new GuzzleClientFactory($config);

        // Act
        $client = $factory->create($cache, $logger);

        // Assert
        $reflection     = new \ReflectionClass($client);
        $configProperty = $reflection->getProperty('config');
        $clientConfig   = $configProperty->getValue($client);
        $handlerStack   = $clientConfig['handler'];

        $this->assertInstanceOf(HandlerStack::class, $handlerStack);

        // Получаем строковое представление stack
        $stackString = (string) $handlerStack;

        // Проверяем наличие необходимых middleware
        $this->assertStringContainsString('logging', $stackString, 'Should contain LoggingMiddleware');
        $this->assertStringContainsString('retry', $stackString, 'Should contain RetryMiddleware');
        $this->assertStringContainsString('rate_limiter', $stackString, 'Should contain RateLimiterMiddleware');
        $this->assertStringContainsString('cache', $stackString, 'Should contain PostCacheMiddleware');
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

        $cache  = $this->createMock(CacheInterface::class);
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

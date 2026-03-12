<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\Tests\Integration;

use Ex3mm\Dadata\Cache\InMemoryCache;
use Ex3mm\Dadata\Client\DadataClient;
use Ex3mm\Dadata\Client\GuzzleClientFactory;
use Ex3mm\Dadata\Config\DadataConfig;
use Ex3mm\Dadata\Exceptions\AuthenticationException;
use Ex3mm\Dadata\Tests\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Psr\Log\NullLogger;

/**
 * Интеграционные тесты для обработки AuthenticationException в AbstractEndpoint.
 *
 * Validates Requirements: 6.4, 6.5, 6.6
 */
final class AuthenticationExceptionIntegrationTest extends TestCase
{
    /**
     * Тест что AbstractEndpoint бросает AuthenticationException при статусе 401.
     *
     * Validates: Requirement 6.4, 6.6
     */
    public function test_throws_authentication_exception_on_401_status(): void
    {
        $this->expectException(AuthenticationException::class);
        $this->expectExceptionMessage('Невалидный API-ключ');
        $this->expectExceptionCode(401);

        $client = $this->createClientWithMockedResponse(401, 'Unauthorized');

        // Пытаемся выполнить запрос - должен бросить AuthenticationException
        $client->suggestAddress()
            ->query('Москва')
            ->send();
    }

    /**
     * Тест что AbstractEndpoint бросает AuthenticationException при статусе 403.
     *
     * Validates: Requirement 6.5, 6.6
     */
    public function test_throws_authentication_exception_on_403_status(): void
    {
        $this->expectException(AuthenticationException::class);
        $this->expectExceptionMessage('Доступ запрещён');
        $this->expectExceptionCode(403);

        $client = $this->createClientWithMockedResponse(403, 'Forbidden');

        // Пытаемся выполнить запрос - должен бросить AuthenticationException
        $client->suggestAddress()
            ->query('Москва')
            ->send();
    }

    /**
     * Тест что AbstractEndpoint корректно обрабатывает успешный ответ (не бросает исключение).
     *
     * Validates: Requirement 6.6
     */
    public function test_does_not_throw_on_successful_response(): void
    {
        $responseBody = json_encode([
            'suggestions' => [
                [
                    'value'              => 'г Москва, ул Тверская',
                    'unrestricted_value' => 'г Москва, ул Тверская',
                    'data'               => [
                        'postal_code' => '125009',
                        'country'     => 'Россия',
                        'region'      => 'Москва',
                        'city'        => 'Москва',
                        'street'      => 'Тверская',
                    ],
                ],
            ],
        ]);

        $client = $this->createClientWithMockedResponse(200, $responseBody);

        $result = $client->suggestAddress()
            ->query('Москва')
            ->send();

        $this->assertNotNull($result);
        $this->assertIsArray($result->suggestions);
    }

    /**
     * Тест что AbstractEndpoint проверяет статус перед другими ошибками API.
     *
     * Validates: Requirement 6.6
     */
    public function test_checks_authentication_before_other_api_errors(): void
    {
        $this->expectException(AuthenticationException::class);
        $this->expectExceptionCode(401);

        // Даже если тело ответа содержит другую ошибку, 401 должен обрабатываться как AuthenticationException
        $client = $this->createClientWithMockedResponse(
            401,
            json_encode(['error' => 'Some other error'])
        );

        $client->suggestAddress()
            ->query('test')
            ->send();
    }

    /**
     * Тест различия между 401 и 403 исключениями.
     *
     * Validates: Requirement 6.4, 6.5
     */
    public function test_distinguishes_between_401_and_403(): void
    {
        // Тест 401
        try {
            $client401 = $this->createClientWithMockedResponse(401, 'Unauthorized');
            $client401->suggestAddress()->query('test')->send();
            $this->fail('Expected AuthenticationException for 401');
        } catch (AuthenticationException $e) {
            $this->assertSame(401, $e->getCode());
            $this->assertStringContainsString('Невалидный API-ключ', $e->getMessage());
        }

        // Тест 403
        try {
            $client403 = $this->createClientWithMockedResponse(403, 'Forbidden');
            $client403->suggestAddress()->query('test')->send();
            $this->fail('Expected AuthenticationException for 403');
        } catch (AuthenticationException $e) {
            $this->assertSame(403, $e->getCode());
            $this->assertStringContainsString('Доступ запрещён', $e->getMessage());
        }
    }

    /**
     * Создаёт DadataClient с замоканным HTTP-ответом.
     */
    private function createClientWithMockedResponse(int $statusCode, string $body): DadataClient
    {
        // Создаём mock handler с заданным ответом
        $mock = new MockHandler([
            new Response($statusCode, [], $body),
            new Response($statusCode, [], $body), // Добавляем второй ответ для второго теста в test_distinguishes_between_401_and_403
        ]);

        $handlerStack = HandlerStack::create($mock);

        // Создаём Guzzle клиент с отключенной проверкой HTTP-ошибок
        $guzzleClient = new Client([
            'handler'     => $handlerStack,
            'http_errors' => false, // Отключаем автоматическое бросание исключений для 4xx/5xx
        ]);

        // Создаём конфигурацию
        $config = DadataConfig::fromArray([
            'api_key'            => 'test-api-key',
            'secret_key'         => 'test-secret-key',
            'rate_limit_enabled' => false, // Отключаем rate limiter для тестов
            'cache_enabled'      => false, // Отключаем кеш для тестов
            'retry_attempts'     => 0, // Отключаем retry для тестов
        ]);

        // Создаём реальную фабрику
        $factory = new GuzzleClientFactory($config);

        // Создаём DadataClient
        $dadataClient = new DadataClient(
            $config,
            $factory,
            new NullLogger(),
            new InMemoryCache()
        );

        // Используем рефлексию для установки замоканного HTTP-клиента
        $reflection = new \ReflectionClass($dadataClient);
        $property   = $reflection->getProperty('httpClient');
        $property->setValue($dadataClient, $guzzleClient);

        return $dadataClient;
    }
}

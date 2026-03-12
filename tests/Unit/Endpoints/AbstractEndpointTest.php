<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\Tests\Unit\Endpoints;

use Ex3mm\Dadata\Config\DadataConfig;
use Ex3mm\Dadata\Contracts\DtoInterface;
use Ex3mm\Dadata\Endpoints\AbstractEndpoint;
use Ex3mm\Dadata\Exceptions\ApiException;
use Ex3mm\Dadata\Exceptions\AuthenticationException;
use Ex3mm\Dadata\Exceptions\RateLimitException;
use Ex3mm\Dadata\Exceptions\ValidationException;
use Ex3mm\Dadata\Tests\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;

/**
 * Тесты для AbstractEndpoint.
 */
final class AbstractEndpointTest extends TestCase
{
    /**
     * Проверяет, что метод handleHttpError бросает AuthenticationException для статуса 401.
     *
     * Validates Requirements: 4.2, 4.3 - Маппинг HTTP 401 на AuthenticationException
     */
    public function test_handle_http_error_throws_authentication_exception_for_401(): void
    {
        // Arrange
        $mock = new MockHandler([
            new Response(401, [], '{"error": "Unauthorized"}'),
        ]);

        $endpoint = $this->createTestEndpoint($mock);

        // Assert
        $this->expectException(AuthenticationException::class);
        $this->expectExceptionMessage('Невалидный API-ключ');

        // Act
        $endpoint->execute(['query' => 'test']);
    }

    /**
     * Проверяет, что метод handleHttpError бросает AuthenticationException для статуса 403.
     *
     * Validates Requirements: 4.2, 4.3 - Маппинг HTTP 403 на AuthenticationException
     */
    public function test_handle_http_error_throws_authentication_exception_for_403(): void
    {
        // Arrange
        $mock = new MockHandler([
            new Response(403, [], '{"error": "Forbidden"}'),
        ]);

        $endpoint = $this->createTestEndpoint($mock);

        // Assert
        $this->expectException(AuthenticationException::class);
        $this->expectExceptionMessage('Доступ запрещён');

        // Act
        $endpoint->execute(['query' => 'test']);
    }

    /**
     * Проверяет, что метод handleHttpError бросает RateLimitException для статуса 429.
     *
     * Validates Requirements: 4.2, 4.4 - Маппинг HTTP 429 на RateLimitException
     */
    public function test_handle_http_error_throws_rate_limit_exception_for_429(): void
    {
        // Arrange
        $mock = new MockHandler([
            new Response(429, ['Retry-After' => '60'], '{"error": "Too many requests"}'),
        ]);

        $endpoint = $this->createTestEndpoint($mock);

        // Assert
        $this->expectException(RateLimitException::class);
        $this->expectExceptionMessage('Превышен лимит запросов');

        // Act
        $endpoint->execute(['query' => 'test']);
    }

    /**
     * Проверяет, что метод handleHttpError бросает ValidationException для статуса 400.
     *
     * Validates Requirements: 4.2 - Маппинг HTTP 400 на ValidationException
     */
    public function test_handle_http_error_throws_validation_exception_for_400(): void
    {
        // Arrange
        $mock = new MockHandler([
            new Response(400, [], '{"errors": ["Invalid query parameter"]}'),
        ]);

        $endpoint = $this->createTestEndpoint($mock);

        // Assert
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Ошибка валидации');

        // Act
        $endpoint->execute(['query' => 'test']);
    }

    /**
     * Проверяет, что метод handleHttpError бросает ValidationException для статуса 422.
     *
     * Validates Requirements: 4.2 - Маппинг HTTP 422 на ValidationException
     */
    public function test_handle_http_error_throws_validation_exception_for_422(): void
    {
        // Arrange
        $mock = new MockHandler([
            new Response(422, [], '{"errors": ["Unprocessable entity"]}'),
        ]);

        $endpoint = $this->createTestEndpoint($mock);

        // Assert
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Ошибка валидации');

        // Act
        $endpoint->execute(['query' => 'test']);
    }

    /**
     * Проверяет, что метод handleHttpError бросает ApiException для других статусов ошибок.
     *
     * Validates Requirements: 4.2 - Маппинг других HTTP ошибок на ApiException
     */
    public function test_handle_http_error_throws_api_exception_for_other_errors(): void
    {
        // Arrange
        $mock = new MockHandler([
            new Response(500, [], '{"error": "Internal server error"}'),
        ]);

        $endpoint = $this->createTestEndpoint($mock);

        // Assert
        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('DaData API вернул ошибку 500');

        // Act
        $endpoint->execute(['query' => 'test']);
    }

    /**
     * Проверяет, что успешный запрос возвращает DTO.
     */
    public function test_successful_request_returns_dto(): void
    {
        // Arrange
        $mock = new MockHandler([
            new Response(200, [], '{"suggestions": []}'),
        ]);

        $endpoint = $this->createTestEndpoint($mock);

        // Act
        $result = $endpoint->execute(['query' => 'test']);

        // Assert
        $this->assertInstanceOf(DtoInterface::class, $result);
    }

    /**
     * Создаёт тестовый endpoint с mock handler.
     */
    private function createTestEndpoint(MockHandler $mock): TestEndpoint
    {
        $handlerStack = HandlerStack::create($mock);
        $httpClient   = new Client(['handler' => $handlerStack]);

        $config = DadataConfig::fromArray([
            'api_key'    => 'test-key',
            'secret_key' => 'test-secret',
        ]);

        return new TestEndpoint($httpClient, $config);
    }
}

/**
 * Тестовый endpoint для проверки AbstractEndpoint.
 */
class TestEndpoint extends AbstractEndpoint
{
    private Client $mockClient;

    public function __construct(Client $mockClient, DadataConfig $config)
    {
        $this->mockClient = $mockClient;
        $this->config     = $config;
    }

    protected function getPath(): string
    {
        return '/test';
    }

    protected function getBaseUrl(): string
    {
        return 'https://api.test.com';
    }

    protected function parseResponse(ResponseInterface $response): DtoInterface
    {
        return new TestDto();
    }

    protected function post(array $body): ResponseInterface
    {
        $url = $this->getBaseUrl() . $this->getPath();

        /** @var ResponseInterface $response */
        $response = $this->mockClient->request('POST', $url, [
            'json'        => $body,
            'headers'     => $this->getHeaders(),
            'http_errors' => false, // Отключаем автоматический выброс исключений для 4xx/5xx
        ]);

        return $response;
    }
}

/**
 * Тестовый DTO.
 */
class TestDto implements DtoInterface
{
    public function toArray(): array
    {
        return [];
    }

    public static function fromArray(array $data, string $rawResponse): static
    {
        return new self();
    }
}

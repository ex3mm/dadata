<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\Tests\Unit\Endpoints;

use Ex3mm\Dadata\Client\DadataClient;
use Ex3mm\Dadata\Client\GuzzleClientFactory;
use Ex3mm\Dadata\Config\DadataConfig;
use Ex3mm\Dadata\DTO\Response\CollectionResponse;
use Ex3mm\Dadata\Endpoints\Suggest\SuggestAddressEndpoint;
use Ex3mm\Dadata\Exceptions\ValidationException;
use Ex3mm\Dadata\Tests\TestCase;
use GuzzleHttp\Psr7\Response;
use Psr\Log\NullLogger;
use Psr\SimpleCache\CacheInterface;
use ReflectionClass;

final class SuggestAddressEndpointTest extends TestCase
{
    private function createEndpoint(): SuggestAddressEndpoint
    {
        $config = DadataConfig::fromArray([
            'api_key'              => 'test',
            'secret_key'           => 'test',
            'base_url_suggestions' => 'https://suggestions.dadata.ru',
        ]);
        $cache   = $this->createMock(CacheInterface::class);
        $logger  = new NullLogger();
        $factory = new GuzzleClientFactory($config);
        $client  = new DadataClient($config, $factory, $logger, $cache);

        return new SuggestAddressEndpoint($client, $config);
    }

    public function test_get_path_returns_correct_api_path(): void
    {
        $endpoint = $this->createEndpoint();

        // Используем рефлексию для доступа к protected методу
        $reflection = new ReflectionClass($endpoint);
        $method     = $reflection->getMethod('getPath');

        $result = $method->invoke($endpoint);

        $this->assertSame('/suggestions/api/4_1/rs/suggest/address', $result);
    }

    public function test_get_base_url_returns_suggestions_url(): void
    {
        $endpoint = $this->createEndpoint();

        // Используем рефлексию для доступа к protected методу
        $reflection = new ReflectionClass($endpoint);
        $method     = $reflection->getMethod('getBaseUrl');

        $result = $method->invoke($endpoint);

        $this->assertSame('https://suggestions.dadata.ru', $result);
    }

    public function test_parse_response_returns_collection_with_suggestions(): void
    {
        $endpoint = $this->createEndpoint();

        // Создаём mock HTTP-ответ с реальными данными
        $responseBody = json_encode([
            'suggestions' => [
                [
                    'value'              => 'г Москва, ул Хабаровская',
                    'unrestricted_value' => 'г Москва, р-н Гольяново, ул Хабаровская',
                    'data'               => [
                        'postal_code' => '107370',
                        'country'     => 'Россия',
                        'region'      => 'Москва',
                        'city'        => 'Москва',
                        'street'      => 'Хабаровская',
                        'house'       => null,
                        'flat'        => null,
                        'fias_id'     => 'test-fias-id',
                        'fias_level'  => '7',
                        'kladr_id'    => 'test-kladr-id',
                        'qc_geo'      => '0',
                        'geo_lat'     => '55.811252',
                        'geo_lon'     => '37.798718',
                    ],
                ],
            ],
        ]);

        $httpResponse = new Response(200, [], $responseBody);

        // Используем рефлексию для доступа к protected методу
        $reflection = new ReflectionClass($endpoint);
        $method     = $reflection->getMethod('parseResponse');

        $result = $method->invoke($endpoint, $httpResponse);

        $this->assertInstanceOf(CollectionResponse::class, $result);
        $this->assertCount(1, $result->items);
        $this->assertSame(1, $result->total);
        $this->assertSame($responseBody, $result->raw);
    }

    public function test_parse_response_throws_exception_for_invalid_json(): void
    {
        $endpoint = $this->createEndpoint();

        // Создаём mock HTTP-ответ с невалидным JSON (строка вместо массива)
        $responseBody = json_encode('invalid');
        $httpResponse = new Response(200, [], $responseBody);

        // Используем рефлексию для доступа к protected методу
        $reflection = new ReflectionClass($endpoint);
        $method     = $reflection->getMethod('parseResponse');

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Invalid JSON response: expected array, got string');

        $method->invoke($endpoint, $httpResponse);
    }

    public function test_parse_response_handles_empty_suggestions(): void
    {
        $endpoint = $this->createEndpoint();

        // Создаём mock HTTP-ответ с пустым массивом подсказок
        $responseBody = json_encode(['suggestions' => []]);
        $httpResponse = new Response(200, [], $responseBody);

        // Используем рефлексию для доступа к protected методу
        $reflection = new ReflectionClass($endpoint);
        $method     = $reflection->getMethod('parseResponse');

        $result = $method->invoke($endpoint, $httpResponse);

        $this->assertInstanceOf(CollectionResponse::class, $result);
        $this->assertCount(0, $result->items);
        $this->assertSame(0, $result->total);
    }

    public function test_execute_throws_validation_exception_for_malformed_json(): void
    {
        $endpoint = $this->createEndpoint();

        // Мокируем HTTP-клиент, который вернёт невалидный JSON
        $httpResponse = new Response(200, [], '{invalid json}');

        $clientMock = $this->createMock(\GuzzleHttp\ClientInterface::class);
        $clientMock->method('request')->willReturn($httpResponse);

        // Подменяем клиент через рефлексию
        $reflection     = new ReflectionClass($endpoint);
        $clientProperty = $reflection->getProperty('client');
        $dadataClient   = $clientProperty->getValue($endpoint);

        $dadataReflection = new ReflectionClass($dadataClient);
        $httpClientProp   = $dadataReflection->getProperty('httpClient');
        $httpClientProp->setValue($dadataClient, $clientMock);

        // Проверяем, что JsonException преобразуется в ValidationException
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Invalid JSON response from API');

        $endpoint->execute(['query' => 'test']);
    }
}

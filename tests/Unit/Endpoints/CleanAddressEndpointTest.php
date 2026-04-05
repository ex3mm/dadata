<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\Tests\Unit\Endpoints;

use Ex3mm\Dadata\Client\DadataClient;
use Ex3mm\Dadata\Client\GuzzleClientFactory;
use Ex3mm\Dadata\Config\DadataConfig;
use Ex3mm\Dadata\DTO\Response\CollectionResponse;
use Ex3mm\Dadata\Endpoints\Cleaner\CleanAddressEndpoint;
use Ex3mm\Dadata\Tests\TestCase;
use GuzzleHttp\Psr7\Response;
use Psr\Log\NullLogger;
use Psr\SimpleCache\CacheInterface;
use ReflectionClass;

final class CleanAddressEndpointTest extends TestCase
{
    private function createEndpoint(): CleanAddressEndpoint
    {
        $config = DadataConfig::fromArray([
            'api_key'          => 'test-api',
            'secret_key'       => 'test-secret',
            'base_url_cleaner' => 'https://cleaner.dadata.ru',
        ]);

        $cache   = $this->createMock(CacheInterface::class);
        $logger  = new NullLogger();
        $factory = new GuzzleClientFactory($config);
        $client  = new DadataClient($config, $factory, $logger, $cache);

        return new CleanAddressEndpoint($client, $config);
    }

    public function test_get_path_returns_clean_address_path(): void
    {
        $endpoint   = $this->createEndpoint();
        $reflection = new ReflectionClass($endpoint);
        $method     = $reflection->getMethod('getPath');

        $result = $method->invoke($endpoint);

        $this->assertSame('/api/v1/clean/address', $result);
    }

    public function test_get_base_url_returns_cleaner_url(): void
    {
        $endpoint   = $this->createEndpoint();
        $reflection = new ReflectionClass($endpoint);
        $method     = $reflection->getMethod('getBaseUrl');

        $result = $method->invoke($endpoint);

        $this->assertSame('https://cleaner.dadata.ru', $result);
    }

    public function test_get_headers_contains_x_secret(): void
    {
        $endpoint   = $this->createEndpoint();
        $reflection = new ReflectionClass($endpoint);
        $method     = $reflection->getMethod('getHeaders');

        /** @var array<string, string> $headers */
        $headers = $method->invoke($endpoint);

        $this->assertSame('Token test-api', $headers['Authorization']);
        $this->assertSame('test-secret', $headers['X-Secret']);
        $this->assertSame('application/json', $headers['Content-Type']);
    }

    public function test_parse_response_handles_single_object_fixture(): void
    {
        $endpoint = $this->createEndpoint();
        $fixture  = file_get_contents(__DIR__ . '/../../fixtures/clean_address_response.json');
        $this->assertNotFalse($fixture);

        $response = new Response(200, [], $fixture);

        $reflection = new ReflectionClass($endpoint);
        $method     = $reflection->getMethod('parseResponse');

        $result = $method->invoke($endpoint, $response);

        $this->assertInstanceOf(CollectionResponse::class, $result);
        $this->assertSame(1, $result->total);
    }
}

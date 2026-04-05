<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\Tests\Unit\Endpoints;

use Ex3mm\Dadata\Client\DadataClient;
use Ex3mm\Dadata\Client\GuzzleClientFactory;
use Ex3mm\Dadata\Config\DadataConfig;
use Ex3mm\Dadata\DTO\Response\CollectionResponse;
use Ex3mm\Dadata\Endpoints\Suggest\FindBankEndpoint;
use Ex3mm\Dadata\Tests\TestCase;
use GuzzleHttp\Psr7\Response;
use Psr\Log\NullLogger;
use Psr\SimpleCache\CacheInterface;
use ReflectionClass;

final class FindBankEndpointTest extends TestCase
{
    private function createEndpoint(): FindBankEndpoint
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

        return new FindBankEndpoint($client, $config);
    }

    public function test_get_path_returns_correct_api_path(): void
    {
        $endpoint   = $this->createEndpoint();
        $reflection = new ReflectionClass($endpoint);
        $method     = $reflection->getMethod('getPath');

        $this->assertSame('/suggestions/api/4_1/rs/findById/bank', $method->invoke($endpoint));
    }

    public function test_parse_response_returns_collection(): void
    {
        $endpoint = $this->createEndpoint();
        $fixture  = file_get_contents(__DIR__ . '/../../fixtures/find_bank_response.json');
        $this->assertNotFalse($fixture);
        $decoded = json_decode($fixture, true);
        $this->assertIsArray($decoded);
        $expectedTotal = isset($decoded['suggestions']) && is_array($decoded['suggestions']) ? count($decoded['suggestions']) : 0;

        $response   = new Response(200, [], $fixture);
        $reflection = new ReflectionClass($endpoint);
        $method     = $reflection->getMethod('parseResponse');

        $result = $method->invoke($endpoint, $response);

        $this->assertInstanceOf(CollectionResponse::class, $result);
        $this->assertSame($expectedTotal, $result->total);
    }
}

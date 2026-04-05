<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\Tests\Unit\Requests;

use Ex3mm\Dadata\Client\DadataClient;
use Ex3mm\Dadata\Client\GuzzleClientFactory;
use Ex3mm\Dadata\Config\DadataConfig;
use Ex3mm\Dadata\Exceptions\NetworkException;
use Ex3mm\Dadata\Exceptions\ValidationException;
use Ex3mm\Dadata\Tests\TestCase;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Psr\Log\NullLogger;
use Psr\SimpleCache\CacheInterface;

final class CustomRequestTest extends TestCase
{
    private DadataConfig $config;
    private DadataClient $client;
    private ClientInterface $httpClient;

    protected function setUp(): void
    {
        parent::setUp();

        $this->config = DadataConfig::fromArray([
            'api_key'    => 'test-api-key',
            'secret_key' => 'test-secret',
        ]);

        $cache        = $this->createMock(CacheInterface::class);
        $factory      = new GuzzleClientFactory($this->config);
        $this->client = new DadataClient($this->config, $factory, new NullLogger(), $cache);

        $this->httpClient = $this->createMock(ClientInterface::class);
        $reflection       = new \ReflectionClass($this->client);
        $property         = $reflection->getProperty('httpClient');
        $property->setValue($this->client, $this->httpClient);
    }

    public function test_custom_request_returns_raw_response(): void
    {
        $this->httpClient->expects($this->once())
            ->method('request')
            ->with(
                'POST',
                'https://suggestions.dadata.ru/suggest/custom',
                $this->callback(function (array $options): bool {
                    $this->assertArrayHasKey('headers', $options);
                    $this->assertSame('Token test-api-key', $options['headers']['Authorization']);
                    $this->assertSame('test-secret', $options['headers']['X-Secret']);
                    $this->assertSame(['q' => 'Москва'], $options['json']);
                    $this->assertFalse($options['http_errors']);

                    return true;
                })
            )
            ->willReturn(new Response(200, ['X-Test' => ['1']], '{"result":"ok"}'));

        $response = $this->client->custom()
            ->method('POST')
            ->endpoint('/suggest/custom')
            ->json(['q' => 'Москва'])
            ->get();

        $this->assertSame('{"result":"ok"}', $response);
    }

    public function test_custom_request_uses_absolute_url(): void
    {
        $this->httpClient->expects($this->once())
            ->method('request')
            ->with(
                'GET',
                'https://cleaner.dadata.ru/api/v1/clean/address',
                $this->arrayHasKey('http_errors')
            )
            ->willReturn(new Response(200, [], '[]'));

        $response = $this->client->custom()
            ->url('https://cleaner.dadata.ru/api/v1/clean/address')
            ->get();

        $this->assertSame('[]', $response);
    }

    public function test_custom_request_accepts_absolute_url_in_endpoint_method(): void
    {
        $this->httpClient->expects($this->once())
            ->method('request')
            ->with(
                'POST',
                'https://suggestions.dadata.ru/suggestions/api/4_1/rs/suggest/address',
                $this->arrayHasKey('http_errors')
            )
            ->willReturn(new Response(200, [], '{"ok":true}'));

        $response = $this->client->custom()
            ->method('POST')
            ->endpoint('https://suggestions.dadata.ru/suggestions/api/4_1/rs/suggest/address')
            ->json(['query' => 'Москва'])
            ->get();

        $this->assertSame('{"ok":true}', $response);
    }

    public function test_custom_request_throws_validation_exception_when_target_is_missing(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Нужно указать endpoint() или url()');

        $this->client->custom()->get();
    }

    public function test_custom_request_returns_raw_body_for_http_errors(): void
    {
        $this->httpClient->expects($this->once())
            ->method('request')
            ->willReturn(new Response(429, ['Retry-After' => ['3']], '{"message":"limit"}'));

        $response = $this->client->custom()
            ->endpoint('/any/path')
            ->get();

        $this->assertSame('{"message":"limit"}', $response);
    }

    public function test_custom_request_maps_guzzle_exception_to_network_exception(): void
    {
        $this->httpClient->expects($this->once())
            ->method('request')
            ->willThrowException(
                new ConnectException('Connection failed', new Request('GET', 'https://suggestions.dadata.ru'))
            );

        $this->expectException(NetworkException::class);

        $this->client->custom()
            ->endpoint('/any/path')
            ->get();
    }

    public function test_custom_request_can_send_raw_body(): void
    {
        $this->httpClient->expects($this->once())
            ->method('request')
            ->with(
                'POST',
                'https://suggestions.dadata.ru/raw/path',
                $this->callback(function (array $options): bool {
                    $this->assertSame('plain-body', $options['body']);
                    $this->assertArrayNotHasKey('json', $options);

                    return true;
                })
            )
            ->willReturn(new Response(200, [], '{"ok":true}'));

        $response = $this->client->custom()
            ->method('POST')
            ->endpoint('/raw/path')
            ->body('plain-body')
            ->get();

        $this->assertSame('{"ok":true}', $response);
    }
}

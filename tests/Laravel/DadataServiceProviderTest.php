<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\Tests\Laravel;

use Ex3mm\Dadata\Client\DadataClient;

final class DadataServiceProviderTest extends LaravelTestCase
{
    public function testServiceProviderRegistersConfig(): void
    {
        $this->assertNotNull(config('dadata'));
        $this->assertIsArray(config('dadata'));
        $this->assertEquals('test_api_key_value', config('dadata.api_key'));
        $this->assertEquals('test_secret_key_value', config('dadata.secret_key'));
    }

    public function testServiceProviderBindsDadataClientInterface(): void
    {
        $client = $this->app->make(\Ex3mm\Dadata\Contracts\DadataClientInterface::class);

        $this->assertInstanceOf(DadataClient::class, $client);
    }

    public function testServiceProviderBindsDadataClientAsSingleton(): void
    {
        $client1 = $this->app->make(\Ex3mm\Dadata\Contracts\DadataClientInterface::class);
        $client2 = $this->app->make(\Ex3mm\Dadata\Contracts\DadataClientInterface::class);

        $this->assertSame($client1, $client2);
    }

    public function testServiceProviderAliasesDadataClient(): void
    {
        $client = $this->app->make('dadata');

        $this->assertInstanceOf(DadataClient::class, $client);
    }
}

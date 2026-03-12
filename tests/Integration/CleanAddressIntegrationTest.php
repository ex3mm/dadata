<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\Tests\Integration;

use Ex3mm\Dadata\Client\DadataClient;
use Ex3mm\Dadata\Config\DadataConfig;
use Ex3mm\Dadata\DTO\Response\CleanAddress\CleanAddressResponse;
use Ex3mm\Dadata\Endpoints\Cleaner\CleanAddressEndpoint;

/**
 * Integration test for address cleaning endpoint.
 *
 * @group integration
 */
class CleanAddressIntegrationTest extends IntegrationTestCase
{
    private CleanAddressEndpoint $endpoint;

    protected function setUp(): void
    {
        parent::setUp();

        $config         = new DadataConfig($this->getDadataConfig());
        $client         = new DadataClient($config);
        $this->endpoint = new CleanAddressEndpoint($client);
    }

    public function testCleanAddressWithValidInput(): void
    {
        // Test with a real Russian address
        $response = $this->endpoint->clean('Москва, Красная площадь, 1');

        $this->assertInstanceOf(CleanAddressResponse::class, $response);
        $this->assertNotEmpty($response->result);
        $this->assertStringContainsString('Москва', $response->result);
    }

    public function testCleanAddressWithMultipleAddresses(): void
    {
        // Test batch cleaning
        $addresses = [
            'Москва, Красная площадь, 1',
            'Санкт-Петербург, Невский проспект, 1',
        ];

        foreach ($addresses as $address) {
            $response = $this->endpoint->clean($address);
            $this->assertInstanceOf(CleanAddressResponse::class, $response);
            $this->assertNotEmpty($response->result);
        }
    }
}

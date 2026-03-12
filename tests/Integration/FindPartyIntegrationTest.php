<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\Tests\Integration;

use Ex3mm\Dadata\Client\DadataClient;
use Ex3mm\Dadata\Config\DadataConfig;
use Ex3mm\Dadata\DTO\Response\FindParty\FindPartyResponse;
use Ex3mm\Dadata\Endpoints\FindParty\FindPartyEndpoint;

/**
 * Integration test for company/party find endpoint.
 *
 * @group integration
 */
class FindPartyIntegrationTest extends IntegrationTestCase
{
    private FindPartyEndpoint $endpoint;

    protected function setUp(): void
    {
        parent::setUp();

        $config         = new DadataConfig($this->getDadataConfig());
        $client         = new DadataClient($config);
        $this->endpoint = new FindPartyEndpoint($client);
    }

    public function testFindPartyByInn(): void
    {
        // Test finding company by INN (Sberbank's INN)
        $response = $this->endpoint->findById('7707083893');

        $this->assertInstanceOf(FindPartyResponse::class, $response);
        $this->assertNotEmpty($response->suggestions);

        // Verify company details
        $company = $response->suggestions[0];
        $this->assertEquals('7707083893', $company->data->inn);
        $this->assertNotEmpty($company->data->name->full);
    }

    public function testFindPartyByOgrn(): void
    {
        // Test finding company by OGRN (Sberbank's OGRN)
        $response = $this->endpoint->findById('1027700132195', 'OGRN');

        $this->assertInstanceOf(FindPartyResponse::class, $response);
        $this->assertNotEmpty($response->suggestions);

        // Verify company details
        $company = $response->suggestions[0];
        $this->assertEquals('1027700132195', $company->data->ogrn);
    }
}

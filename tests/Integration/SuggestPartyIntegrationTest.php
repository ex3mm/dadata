<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\Tests\Integration;

use Ex3mm\Dadata\Client\DadataClient;
use Ex3mm\Dadata\Config\DadataConfig;
use Ex3mm\Dadata\DTO\Response\SuggestParty\SuggestPartyResponse;
use Ex3mm\Dadata\Endpoints\Suggest\SuggestPartyEndpoint;

/**
 * Integration test for company/party suggestions endpoint.
 *
 * @group integration
 */
class SuggestPartyIntegrationTest extends IntegrationTestCase
{
    private SuggestPartyEndpoint $endpoint;

    protected function setUp(): void
    {
        parent::setUp();

        $config         = new DadataConfig($this->getDadataConfig());
        $client         = new DadataClient($config);
        $this->endpoint = new SuggestPartyEndpoint($client);
    }

    public function testSuggestPartyWithValidQuery(): void
    {
        // Test company suggestions with a well-known company
        $response = $this->endpoint->suggest('Сбербанк');

        $this->assertInstanceOf(SuggestPartyResponse::class, $response);
        $this->assertNotEmpty($response->suggestions);
        $this->assertGreaterThan(0, count($response->suggestions));

        // Verify first suggestion structure
        $firstSuggestion = $response->suggestions[0];
        $this->assertNotEmpty($firstSuggestion->value);
        $this->assertNotNull($firstSuggestion->data);
        $this->assertNotEmpty($firstSuggestion->data->inn);
    }

    public function testSuggestPartyByInn(): void
    {
        // Test search by INN (Sberbank's INN)
        $response = $this->endpoint->suggest('7707083893');

        $this->assertInstanceOf(SuggestPartyResponse::class, $response);
        $this->assertNotEmpty($response->suggestions);

        // Verify INN matches
        $firstSuggestion = $response->suggestions[0];
        $this->assertEquals('7707083893', $firstSuggestion->data->inn);
    }
}

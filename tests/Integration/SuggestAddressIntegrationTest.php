<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\Tests\Integration;

use Ex3mm\Dadata\Client\DadataClient;
use Ex3mm\Dadata\Config\DadataConfig;
use Ex3mm\Dadata\DTO\Response\SuggestAddress\SuggestAddressResponse;
use Ex3mm\Dadata\Endpoints\Suggest\SuggestAddressEndpoint;

/**
 * Integration test for address suggestions endpoint.
 *
 * @group integration
 */
class SuggestAddressIntegrationTest extends IntegrationTestCase
{
    private SuggestAddressEndpoint $endpoint;

    protected function setUp(): void
    {
        parent::setUp();

        $config         = new DadataConfig($this->getDadataConfig());
        $client         = new DadataClient($config);
        $this->endpoint = new SuggestAddressEndpoint($client);
    }

    public function testSuggestAddressWithValidQuery(): void
    {
        // Test address suggestions
        $response = $this->endpoint->suggest('Москва, Красная');

        $this->assertInstanceOf(SuggestAddressResponse::class, $response);
        $this->assertNotEmpty($response->suggestions);
        $this->assertGreaterThan(0, count($response->suggestions));

        // Verify first suggestion structure
        $firstSuggestion = $response->suggestions[0];
        $this->assertNotEmpty($firstSuggestion->value);
        $this->assertNotNull($firstSuggestion->data);
    }

    public function testSuggestAddressWithLimit(): void
    {
        // Test with limit parameter
        $response = $this->endpoint->suggest('Москва', ['count' => 5]);

        $this->assertInstanceOf(SuggestAddressResponse::class, $response);
        $this->assertLessThanOrEqual(5, count($response->suggestions));
    }
}

<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\Tests\Integration;

/**
 * Test to verify integration test infrastructure is working.
 *
 * @group integration
 */
class IntegrationTestInfrastructureTest extends IntegrationTestCase
{
    public function testIntegrationTestInfrastructureIsWorking(): void
    {
        // This test verifies that the integration test infrastructure is set up correctly
        $this->assertTrue(true, 'Integration test infrastructure is working');

        // Verify that configuration method works
        $config = $this->getDadataConfig();
        $this->assertIsArray($config);
        $this->assertArrayHasKey('api_key', $config);
        $this->assertArrayHasKey('secret_key', $config);
        $this->assertArrayHasKey('base_url', $config);
        $this->assertArrayHasKey('clean_url', $config);
        $this->assertArrayHasKey('timeout', $config);
    }
}

<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\Tests\Integration;

use PHPUnit\Framework\TestCase as BaseTestCase;

/**
 * Base class for integration tests that make real HTTP requests to DaData API.
 *
 * @group integration
 */
abstract class IntegrationTestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Skip integration tests if credentials are not provided
        if (empty($this->getApiKey()) || empty($this->getSecretKey())) {
            $this->markTestSkipped(
                'Integration tests require DADATA_API_KEY and DADATA_SECRET_KEY environment variables. ' .
                'See tests/Integration/README.md for setup instructions.'
            );
        }
    }

    protected function getApiKey(): string
    {
        return $_ENV['DADATA_API_KEY'] ?? '';
    }

    protected function getSecretKey(): string
    {
        return $_ENV['DADATA_SECRET_KEY'] ?? '';
    }

    protected function getDadataConfig(): array
    {
        return [
            'api_key'    => $this->getApiKey(),
            'secret_key' => $this->getSecretKey(),
            'base_url'   => 'https://suggestions.dadata.ru/suggestions/api/4_1/rs',
            'clean_url'  => 'https://cleaner.dadata.ru/api/v1/clean',
            'timeout'    => 30,
        ];
    }
}

<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\Client;

use Ex3mm\Dadata\Config\DadataConfig;
use Ex3mm\Dadata\Contracts\DadataClientInterface;
use Ex3mm\Dadata\Requests\CleanAddressRequest;
use Ex3mm\Dadata\Requests\FindPartyRequest;
use Ex3mm\Dadata\Requests\RawRequest;
use Ex3mm\Dadata\Requests\SuggestAddressRequest;
use Ex3mm\Dadata\Requests\SuggestPartyRequest;
use GuzzleHttp\ClientInterface;
use Psr\Log\LoggerInterface;
use Psr\SimpleCache\CacheInterface;

/**
 * Главный клиент для работы с DaData API.
 * Использует lazy initialization для HTTP-клиента.
 */
final class DadataClient implements DadataClientInterface
{
    private ?ClientInterface $httpClient = null;

    public function __construct(
        private readonly DadataConfig $config,
        private readonly GuzzleClientFactory $factory,
        private readonly LoggerInterface $logger,
        private readonly CacheInterface $cache,
    ) {
    }

    /**
     * Получает HTTP-клиент (создаётся при первом обращении).
     */
    public function getClient(): ClientInterface
    {
        return $this->httpClient ??= $this->factory->create($this->cache, $this->logger);
    }

    public function cleanAddress(): CleanAddressRequest
    {
        return new CleanAddressRequest(
            new \Ex3mm\Dadata\Endpoints\Cleaner\CleanAddressEndpoint($this, $this->config)
        );
    }

    public function suggestAddress(): SuggestAddressRequest
    {
        return new SuggestAddressRequest(
            new \Ex3mm\Dadata\Endpoints\Suggest\SuggestAddressEndpoint($this, $this->config)
        );
    }

    public function suggestParty(): SuggestPartyRequest
    {
        return new SuggestPartyRequest(
            new \Ex3mm\Dadata\Endpoints\Suggest\SuggestPartyEndpoint($this, $this->config)
        );
    }

    public function findParty(): FindPartyRequest
    {
        return new FindPartyRequest(
            new \Ex3mm\Dadata\Endpoints\FindParty\FindPartyEndpoint($this, $this->config)
        );
    }

    public function raw(): RawRequest
    {
        return new RawRequest(
            new \Ex3mm\Dadata\Endpoints\Raw\RawEndpoint($this, $this->config)
        );
    }
}

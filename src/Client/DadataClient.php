<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\Client;

use Ex3mm\Dadata\Config\DadataConfig;
use Ex3mm\Dadata\Contracts\DadataClientInterface;
use Ex3mm\Dadata\Requests\CleanAddressRequest;
use Ex3mm\Dadata\Requests\CustomRequest;
use Ex3mm\Dadata\Requests\FindAffiliatedRequest;
use Ex3mm\Dadata\Requests\FindBankRequest;
use Ex3mm\Dadata\Requests\FindPartyRequest;
use Ex3mm\Dadata\Requests\SuggestAddressRequest;
use Ex3mm\Dadata\Requests\SuggestBankRequest;
use Ex3mm\Dadata\Requests\SuggestFioRequest;
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

    public function suggestAddress(): SuggestAddressRequest
    {
        return new SuggestAddressRequest(
            new \Ex3mm\Dadata\Endpoints\Suggest\SuggestAddressEndpoint($this, $this->config)
        );
    }

    public function suggestBank(): SuggestBankRequest
    {
        return new SuggestBankRequest(
            new \Ex3mm\Dadata\Endpoints\Suggest\SuggestBankEndpoint($this, $this->config)
        );
    }

    public function suggestParty(): SuggestPartyRequest
    {
        return new SuggestPartyRequest(
            new \Ex3mm\Dadata\Endpoints\Suggest\SuggestPartyEndpoint($this, $this->config)
        );
    }

    public function suggestFio(): SuggestFioRequest
    {
        return new SuggestFioRequest(
            new \Ex3mm\Dadata\Endpoints\Suggest\SuggestFioEndpoint($this, $this->config)
        );
    }

    public function findAffiliated(): FindAffiliatedRequest
    {
        return new FindAffiliatedRequest(
            new \Ex3mm\Dadata\Endpoints\Suggest\FindAffiliatedEndpoint($this, $this->config)
        );
    }

    public function findBank(): FindBankRequest
    {
        return new FindBankRequest(
            new \Ex3mm\Dadata\Endpoints\Suggest\FindBankEndpoint($this, $this->config)
        );
    }

    public function findParty(): FindPartyRequest
    {
        return new FindPartyRequest(
            new \Ex3mm\Dadata\Endpoints\Suggest\FindPartyEndpoint($this, $this->config)
        );
    }

    public function cleanAddress(): CleanAddressRequest
    {
        return new CleanAddressRequest(
            new \Ex3mm\Dadata\Endpoints\Cleaner\CleanAddressEndpoint($this, $this->config)
        );
    }

    public function custom(): CustomRequest
    {
        return new CustomRequest($this, $this->config);
    }
}

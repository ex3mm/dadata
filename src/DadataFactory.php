<?php

declare(strict_types=1);

namespace Ex3mm\Dadata;

use Ex3mm\Dadata\Cache\InMemoryCache;
use Ex3mm\Dadata\Client\DadataClient;
use Ex3mm\Dadata\Client\GuzzleClientFactory;
use Ex3mm\Dadata\Config\DadataConfig;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Psr\SimpleCache\CacheInterface;

/**
 * Фабрика для создания DadataClient в standalone режиме.
 */
final class DadataFactory
{
    /**
     * Создаёт настроенный DadataClient.
     *
     * @param string $apiKey API ключ
     * @param string $secretKey Секретный ключ
     * @param array<string, mixed> $options Дополнительные опции конфигурации
     * @param CacheInterface|null $cache PSR-16 кеш (по умолчанию InMemoryCache)
     * @param LoggerInterface|null $logger PSR-3 логгер (по умолчанию NullLogger)
     */
    public static function create(
        string $apiKey,
        string $secretKey,
        array $options = [],
        ?CacheInterface $cache = null,
        ?LoggerInterface $logger = null,
    ): DadataClient {
        /** @var array{api_key: non-empty-string, secret_key: non-empty-string, base_url_cleaner?: non-empty-string, base_url_suggestions?: non-empty-string, connect_timeout?: int<1, max>, timeout?: int<1, max>, cache_enabled?: bool, cache_ttl?: int<1, max>, ...} $configArray */
        $configArray = array_merge([
            'api_key'              => $apiKey,
            'secret_key'           => $secretKey,
            'base_url_cleaner'     => DadataConfig::CLEANER_BASE_URL,
            'base_url_suggestions' => DadataConfig::SUGGESTIONS_BASE_URL,
            'connect_timeout'      => 10,
            'timeout'              => 30,
            'cache_enabled'        => true,
            'cache_ttl'            => 3600,
            'cache_store'          => null,
            'log_level'            => 'info',
            'log_request_body'     => false,
            'log_response_body'    => false,
            'log_channel'          => null,
            'rate_limit_enabled'   => true,
            'rate_limit'           => 20,
            'retry_attempts'       => 3,
            'retry_delay'          => 100,
        ], $options);

        $config = DadataConfig::fromArray($configArray);

        $cache  ??= new InMemoryCache();
        $logger ??= new NullLogger();

        return new DadataClient(
            $config,
            new GuzzleClientFactory($config),
            $logger,
            $cache
        );
    }
}

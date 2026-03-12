<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\Laravel;

use Ex3mm\Dadata\Client\DadataClient;
use Ex3mm\Dadata\Client\GuzzleClientFactory;
use Ex3mm\Dadata\Config\DadataConfig;
use Ex3mm\Dadata\Contracts\DadataClientInterface;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Psr\Log\LoggerInterface;
use Psr\SimpleCache\CacheInterface;

/**
 * Service Provider для интеграции с Laravel.
 */
final class DadataServiceProvider extends ServiceProvider
{
    /**
     * Регистрация сервисов в контейнере.
     */
    #[\Override]
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/dadata.php', 'dadata');

        $this->app->singleton(DadataClientInterface::class, function (Application $app): DadataClient {
            /** @var \Illuminate\Contracts\Config\Repository $configRepository */
            $configRepository = $app->make('config');

            /** @var array<string, mixed> $rawConfig */
            $rawConfig = $configRepository->get('dadata', []);

            /** @var array{api_key: non-empty-string, secret_key: non-empty-string, base_url_cleaner?: non-empty-string, base_url_suggestions?: non-empty-string, connect_timeout?: int<1, max>, timeout?: int<1, max>, cache_enabled?: bool, cache_ttl?: int<1, max>, ...} $configArray */
            $configArray = $rawConfig;

            // Валидация происходит в DadataConfig::fromArray()
            $config = DadataConfig::fromArray($configArray);

            $cache  = $this->resolveCacheAdapter($app, $config);
            $logger = $this->resolveLogger($app, $config);

            return new DadataClient(
                $config,
                new GuzzleClientFactory($config),
                $logger,
                $cache
            );
        });

        $this->app->alias(DadataClientInterface::class, 'dadata');
    }

    /**
     * Загрузка сервисов после регистрации.
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../../config/dadata.php' => config_path('dadata.php'),
        ], 'dadata-config');
    }

    /**
     * Резолвит PSR-16 кеш из Laravel.
     */
    private function resolveCacheAdapter(Application $app, DadataConfig $config): CacheInterface
    {
        /** @var \Illuminate\Cache\CacheManager $cacheManager */
        $cacheManager = $app->make('cache');

        /** @var \Illuminate\Contracts\Cache\Repository $laravelCache */
        $laravelCache = $config->cache->store !== null
            ? $cacheManager->store($config->cache->store)
            : $cacheManager->store();

        return $laravelCache;
    }

    /**
     * Резолвит PSR-3 логгер из Laravel.
     */
    private function resolveLogger(Application $app, DadataConfig $config): LoggerInterface
    {
        /** @var \Illuminate\Log\LogManager $logManager */
        $logManager = $app->make('log');

        if ($config->log->channel !== null) {
            return $logManager->channel($config->log->channel);
        }

        return $logManager->channel();
    }
}

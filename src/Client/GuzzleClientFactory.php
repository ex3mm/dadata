<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\Client;

use Ex3mm\Dadata\Client\Middleware\LoggingMiddleware;
use Ex3mm\Dadata\Client\Middleware\RateLimiterMiddleware;
use Ex3mm\Dadata\Client\Middleware\RetryMiddleware;
use Ex3mm\Dadata\Config\DadataConfig;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\HandlerStack;
use Psr\Log\LoggerInterface;
use Psr\SimpleCache\CacheInterface;

/**
 * Фабрика для создания настроенного Guzzle HTTP-клиента.
 */
final class GuzzleClientFactory
{
    public function __construct(
        private readonly DadataConfig $config,
    ) {
    }

    /**
     * Создаёт настроенный HTTP-клиент с middleware stack.
     *
     * @param CacheInterface $cache PSR-16 кеш
     * @param LoggerInterface $logger PSR-3 логгер
     */
    public function create(CacheInterface $cache, LoggerInterface $logger): ClientInterface
    {
        $stack = HandlerStack::create();

        // Порядок middleware (выполняются снизу вверх при запросе):
        // 1. RateLimiterMiddleware
        if ($this->config->rateLimit->enabled) {
            $rateLimiter = new DadataRateLimiter(
                $cache,
                $this->config->rateLimit->limit,
                1 // decay seconds
            );
            $stack->push(
                new RateLimiterMiddleware($rateLimiter),
                'rate_limiter'
            );
        }

        // 2. RetryMiddleware
        if ($this->config->http->retryAttempts > 0) {
            $stack->push(
                new RetryMiddleware(
                    $this->config->http->retryAttempts,
                    $this->config->http->retryDelay,
                    $logger
                ),
                'retry'
            );
        }

        // 3. LoggingMiddleware (логирует все запросы/ответы, включая кешированные)
        $stack->push(
            new LoggingMiddleware(
                $logger,
                $this->config->log->requestBody,
                $this->config->log->responseBody
            ),
            'logging'
        );

        // 4. PostCacheMiddleware - последний (проверяет кеш перед отправкой запроса)
        if ($this->config->cache->enabled) {
            $stack->push(
                new \Ex3mm\Dadata\Client\Middleware\PostCacheMiddleware(
                    $cache,
                    $this->config->cache->ttl,
                    $logger
                ),
                'cache'
            );
        }

        // Создаём клиент
        $clientConfig = [
            'handler'         => $stack,
            'connect_timeout' => $this->config->http->connectTimeout,
            'timeout'         => $this->config->http->timeout,
        ];

        return new Client($clientConfig);
    }
}

<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\Client;

use Ex3mm\Dadata\Contracts\RateLimiterInterface;
use Psr\SimpleCache\CacheInterface;

/**
 * Реализация rate limiter с использованием sliding window алгоритма.
 *
 * Sliding window обеспечивает более точное ограничение частоты запросов
 * по сравнению с fixed window, так как учитывает точное время каждого запроса.
 */
final class DadataRateLimiter implements RateLimiterInterface
{
    private const string CACHE_KEY_PREFIX = 'dadata_rate_limiter_';

    public function __construct(
        private readonly CacheInterface $storage,
        private readonly int $maxAttempts,
        private readonly int $decaySeconds = 1,
    ) {
    }

    public function attempt(string $key): bool
    {
        if ($this->tooManyAttempts($key)) {
            return false;
        }

        $this->hit($key);
        return true;
    }

    public function tooManyAttempts(string $key): bool
    {
        $timestamps = $this->getTimestamps($key);
        return count($timestamps) >= $this->maxAttempts;
    }

    public function availableIn(string $key): int
    {
        $timestamps = $this->getTimestamps($key);

        if (count($timestamps) === 0) {
            return 0;
        }

        $oldest      = min($timestamps);
        $availableAt = $oldest + $this->decaySeconds;
        $now         = microtime(true);

        return max(0, (int) ceil($availableAt - $now));
    }

    /**
     * Добавляет timestamp текущего запроса.
     */
    private function hit(string $key): void
    {
        $timestamps   = $this->getTimestamps($key);
        $timestamps[] = microtime(true);

        $cacheKey = $this->getCacheKey($key);
        $this->storage->set($cacheKey, $timestamps, $this->decaySeconds + 1);
    }

    /**
     * Получает список timestamps с фильтрацией устаревших.
     *
     * @return list<float>
     */
    private function getTimestamps(string $key): array
    {
        $cacheKey   = $this->getCacheKey($key);
        $timestamps = $this->storage->get($cacheKey, []);

        if (!is_array($timestamps)) {
            return [];
        }

        $now    = microtime(true);
        $cutoff = $now - $this->decaySeconds;

        // Фильтруем устаревшие timestamps (sliding window)
        // Явно проверяем тип каждого элемента для PHPStan
        $filtered = [];
        foreach ($timestamps as $ts) {
            if (is_float($ts) && $ts > $cutoff) {
                $filtered[] = $ts;
            }
        }

        return $filtered;
    }

    private function getCacheKey(string $key): string
    {
        return self::CACHE_KEY_PREFIX . $key;
    }
}

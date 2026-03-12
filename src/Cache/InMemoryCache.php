<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\Cache;

use DateInterval;
use Psr\SimpleCache\CacheInterface;

/**
 * Простая in-memory реализация PSR-16 кеша для standalone режима.
 *
 * @phpstan-type CacheEntry array{value: mixed, expires: float|null}
 */
final class InMemoryCache implements CacheInterface
{
    /**
     * @var array<string, CacheEntry>
     */
    private array $store = [];

    public function get(string $key, mixed $default = null): mixed
    {
        if (! $this->has($key)) {
            return $default;
        }

        return $this->store[$key]['value'];
    }

    public function set(string $key, mixed $value, null|int|DateInterval $ttl = null): bool
    {
        $expires = $this->calculateExpiration($ttl);

        $this->store[$key] = [
            'value'   => $value,
            'expires' => $expires,
        ];

        return true;
    }

    public function delete(string $key): bool
    {
        unset($this->store[$key]);

        return true;
    }

    public function clear(): bool
    {
        $this->store = [];

        return true;
    }

    /**
     * @param iterable<string> $keys
     *
     * @return iterable<string, mixed>
     */
    public function getMultiple(iterable $keys, mixed $default = null): iterable
    {
        $result = [];

        foreach ($keys as $key) {
            $result[$key] = $this->get($key, $default);
        }

        return $result;
    }

    /**
     * @param iterable<string, mixed> $values
     *
     * @phpstan-ignore method.childParameterType
     */
    public function setMultiple(iterable $values, null|int|DateInterval $ttl = null): bool
    {
        foreach ($values as $key => $value) {
            if (!is_string($key)) {
                continue;
            }
            $this->set($key, $value, $ttl);
        }

        return true;
    }

    /**
     * @param iterable<string> $keys
     */
    public function deleteMultiple(iterable $keys): bool
    {
        foreach ($keys as $key) {
            $this->delete($key);
        }

        return true;
    }

    public function has(string $key): bool
    {
        if (! isset($this->store[$key])) {
            return false;
        }

        $entry = $this->store[$key];

        // Проверяем истечение TTL
        if ($entry['expires'] !== null && microtime(true) > $entry['expires']) {
            unset($this->store[$key]);

            return false;
        }

        return true;
    }

    /**
     * Вычисляет время истечения кеша.
     */
    private function calculateExpiration(null|int|DateInterval $ttl): ?float
    {
        if ($ttl === null) {
            return null;
        }

        if ($ttl instanceof DateInterval) {
            $now = new \DateTimeImmutable();
            $end = $now->add($ttl);

            return (float) $end->format('U.u');
        }

        return microtime(true) + $ttl;
    }
}

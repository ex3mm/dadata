<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\Config;

use Ex3mm\Dadata\Exceptions\ConfigurationException;

/**
 * Конфигурация кеширования.
 *
 * @phpstan-type CacheConfigArray array{
 *   cache_enabled?: bool,
 *   cache_ttl?: positive-int,
 *   cache_store?: string|null,
 * }
 */
final readonly class CacheConfig
{
    /**
     * @param bool $enabled Включить кеширование ответов
     * @param positive-int $ttl Время жизни кеша в секундах
     * @param string|null $store Имя хранилища кеша (Laravel)
     */
    public function __construct(
        public bool $enabled = true,
        public int $ttl = 3600,
        public ?string $store = null,
    ) {
    }

    /**
     * Создаёт конфигурацию кеша из массива с валидацией.
     *
     * @param CacheConfigArray $config Массив конфигурации
     *
     * @throws ConfigurationException При невалидной конфигурации
     */
    public static function fromArray(array $config): self
    {
        $enabled = $config['cache_enabled'] ?? true;
        $ttl     = $config['cache_ttl']     ?? 3600;
        $store   = $config['cache_store']   ?? null;

        if ($ttl <= 0) {
            throw new ConfigurationException('Время жизни кеша (cache_ttl) должно быть положительным числом');
        }

        return new self(
            enabled: $enabled,
            ttl: $ttl,
            store: $store,
        );
    }
}

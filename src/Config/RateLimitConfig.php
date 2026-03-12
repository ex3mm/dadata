<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\Config;

use Ex3mm\Dadata\Exceptions\ConfigurationException;

/**
 * Конфигурация ограничения частоты запросов.
 *
 * @phpstan-type RateLimitConfigArray array{
 *   rate_limit_enabled?: bool,
 *   rate_limit?: positive-int,
 * }
 */
final readonly class RateLimitConfig
{
    /**
     * @param bool $enabled Включить ограничение частоты запросов
     * @param positive-int $limit Максимальное количество запросов в секунду
     */
    public function __construct(
        public bool $enabled = true,
        public int $limit = 20,
    ) {
    }

    /**
     * Создаёт конфигурацию rate limit из массива с валидацией.
     *
     * @param RateLimitConfigArray $config Массив конфигурации
     *
     * @throws ConfigurationException При невалидной конфигурации
     */
    public static function fromArray(array $config): self
    {
        $enabled = $config['rate_limit_enabled'] ?? true;
        $limit   = $config['rate_limit']         ?? 20;

        if ($limit <= 0) {
            throw new ConfigurationException('Лимит запросов (rate_limit) должен быть положительным числом');
        }

        return new self(
            enabled: $enabled,
            limit: $limit,
        );
    }
}

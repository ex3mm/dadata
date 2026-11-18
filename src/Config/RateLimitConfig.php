<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\Config;

use Ex3mm\Dadata\Exceptions\ConfigurationException;

/**
 * Конфигурация ограничения частоты запросов.
 *
 * @phpstan-type RateLimitConfigArray array{
 *   rate_limit_enabled?: bool,
 *   rate_limit_suggestions?: positive-int,
 *   rate_limit_cleaner?: positive-int,
 * }
 */
final readonly class RateLimitConfig
{
    /**
     * @param bool $enabled Включить ограничение частоты запросов
     * @param positive-int $suggestionsLimit Лимит запросов к Suggestions API
     * @param positive-int $cleanerLimit Лимит запросов к Cleaner API
     */
    public function __construct(
        public bool $enabled = true,
        public int $suggestionsLimit = 30,
        public int $cleanerLimit = 20,
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
        $enabled          = $config['rate_limit_enabled']     ?? true;
        $suggestionsLimit = $config['rate_limit_suggestions'] ?? 30;
        $cleanerLimit     = $config['rate_limit_cleaner']     ?? 20;

        if ($suggestionsLimit <= 0) {
            throw new ConfigurationException('Лимит запросов Suggestions (rate_limit_suggestions) должен быть положительным числом');
        }

        if ($cleanerLimit <= 0) {
            throw new ConfigurationException('Лимит запросов Cleaner (rate_limit_cleaner) должен быть положительным числом');
        }

        return new self(
            enabled: $enabled,
            suggestionsLimit: $suggestionsLimit,
            cleanerLimit: $cleanerLimit,
        );
    }
}

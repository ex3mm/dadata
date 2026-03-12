<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\Config;

use Ex3mm\Dadata\Exceptions\ConfigurationException;

/**
 * Типизированная конфигурация для DaData клиента.
 *
 * @phpstan-type ConfigArray array{
 *   api_key: non-empty-string,
 *   secret_key: non-empty-string,
 *   base_url_cleaner?: non-empty-string,
 *   base_url_suggestions?: non-empty-string,
 *   connect_timeout?: positive-int,
 *   timeout?: positive-int,
 *   cache_enabled?: bool,
 *   cache_ttl?: positive-int,
 *   cache_store?: string|null,
 *   log_level?: string,
 *   log_request_body?: bool,
 *   log_response_body?: bool,
 *   log_channel?: string|null,
 *   rate_limit_enabled?: bool,
 *   rate_limit?: positive-int,
 *   retry_attempts?: int<0, max>,
 *   retry_delay?: positive-int,
 * }
 */
final readonly class DadataConfig
{
    /** URL для Cleaner API */
    public const string CLEANER_BASE_URL = 'https://cleaner.dadata.ru';

    /** URL для Suggestions API */
    public const string SUGGESTIONS_BASE_URL = 'https://suggestions.dadata.ru';

    /**
     * @param non-empty-string $apiKey API ключ для аутентификации
     * @param non-empty-string $secretKey Секретный ключ для Cleaner API
     * @param non-empty-string $baseUrlCleaner Базовый URL для Cleaner API
     * @param non-empty-string $baseUrlSuggestions Базовый URL для Suggestions API
     * @param HttpConfig $http Конфигурация HTTP-клиента
     * @param CacheConfig $cache Конфигурация кеширования
     * @param LogConfig $log Конфигурация логирования
     * @param RateLimitConfig $rateLimit Конфигурация ограничения частоты запросов
     */
    public function __construct(
        public string $apiKey,
        public string $secretKey,
        public string $baseUrlCleaner,
        public string $baseUrlSuggestions,
        public HttpConfig $http,
        public CacheConfig $cache,
        public LogConfig $log,
        public RateLimitConfig $rateLimit,
    ) {
    }

    /**
     * Создаёт конфигурацию из массива с валидацией.
     *
     * @param ConfigArray $config Массив конфигурации
     *
     * @throws ConfigurationException При невалидной конфигурации
     */
    public static function fromArray(array $config): self
    {
        // Валидация обязательных полей
        if (!isset($config['api_key']) || $config['api_key'] === '') {
            throw new ConfigurationException('API ключ (api_key) не может быть пустым');
        }

        if (!is_string($config['api_key'])) {
            throw new ConfigurationException('API ключ (api_key) должен быть строкой, передан ' . gettype($config['api_key']));
        }

        if (!isset($config['secret_key']) || $config['secret_key'] === '') {
            throw new ConfigurationException('Секретный ключ (secret_key) не может быть пустым');
        }

        if (!is_string($config['secret_key'])) {
            throw new ConfigurationException('Секретный ключ (secret_key) должен быть строкой, передан ' . gettype($config['secret_key']));
        }

        return new self(
            apiKey: $config['api_key'],
            secretKey: $config['secret_key'],
            baseUrlCleaner: $config['base_url_cleaner']         ?? self::CLEANER_BASE_URL,
            baseUrlSuggestions: $config['base_url_suggestions'] ?? self::SUGGESTIONS_BASE_URL,
            http: HttpConfig::fromArray($config),
            cache: CacheConfig::fromArray($config),
            log: LogConfig::fromArray($config),
            rateLimit: RateLimitConfig::fromArray($config),
        );
    }
}

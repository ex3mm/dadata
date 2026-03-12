<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\Config;

/**
 * Конфигурация логирования.
 *
 * @phpstan-type LogConfigArray array{
 *   log_level?: string,
 *   log_request_body?: bool,
 *   log_response_body?: bool,
 *   log_channel?: string|null,
 * }
 */
final readonly class LogConfig
{
    /**
     * @param string $level Уровень логирования (debug, info, warning, error)
     * @param bool $requestBody Логировать тело запроса
     * @param bool $responseBody Логировать тело ответа
     * @param string|null $channel Канал логирования (Laravel)
     */
    public function __construct(
        public string $level = 'info',
        public bool $requestBody = false,
        public bool $responseBody = false,
        public ?string $channel = null,
    ) {
    }

    /**
     * Создаёт конфигурацию логирования из массива.
     *
     * @param LogConfigArray $config Массив конфигурации
     */
    public static function fromArray(array $config): self
    {
        return new self(
            level: $config['log_level']                ?? 'info',
            requestBody: $config['log_request_body']   ?? false,
            responseBody: $config['log_response_body'] ?? false,
            channel: $config['log_channel']            ?? null,
        );
    }
}

<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\Config;

use Ex3mm\Dadata\Exceptions\ConfigurationException;

/**
 * Конфигурация HTTP-клиента.
 *
 * @phpstan-type HttpConfigArray array{
 *   connect_timeout?: positive-int,
 *   timeout?: positive-int,
 *   retry_attempts?: int<0, max>,
 *   retry_delay?: positive-int,
 * }
 */
final readonly class HttpConfig
{
    /**
     * @param positive-int $connectTimeout Таймаут подключения в секундах
     * @param positive-int $timeout Таймаут запроса в секундах
     * @param int<0, max> $retryAttempts Количество повторных попыток при ошибках
     * @param positive-int $retryDelay Начальная задержка между попытками в миллисекундах
     */
    public function __construct(
        public int $connectTimeout = 10,
        public int $timeout = 30,
        public int $retryAttempts = 3,
        public int $retryDelay = 100,
    ) {
    }

    /**
     * Создаёт конфигурацию HTTP из массива с валидацией.
     *
     * @param HttpConfigArray $config Массив конфигурации
     *
     * @throws ConfigurationException При невалидной конфигурации
     */
    public static function fromArray(array $config): self
    {
        $connectTimeout = $config['connect_timeout'] ?? 10;
        $timeout        = $config['timeout']         ?? 30;
        $retryAttempts  = $config['retry_attempts']  ?? 3;
        $retryDelay     = $config['retry_delay']     ?? 100;

        if ($connectTimeout <= 0) {
            throw new ConfigurationException('Таймаут подключения (connect_timeout) должен быть положительным числом');
        }

        if ($timeout <= 0) {
            throw new ConfigurationException('Таймаут запроса (timeout) должен быть положительным числом');
        }

        if ($retryAttempts < 0) {
            throw new ConfigurationException('Количество повторных попыток (retry_attempts) не может быть отрицательным');
        }

        if ($retryDelay <= 0) {
            throw new ConfigurationException('Задержка между попытками (retry_delay) должна быть положительным числом');
        }

        return new self(
            connectTimeout: $connectTimeout,
            timeout: $timeout,
            retryAttempts: $retryAttempts,
            retryDelay: $retryDelay,
        );
    }
}

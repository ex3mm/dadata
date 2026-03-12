<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\Contracts;

/**
 * Интерфейс для компонента ограничения частоты запросов.
 *
 * Реализации этого интерфейса должны обеспечивать ограничение
 * количества запросов к API в единицу времени.
 */
interface RateLimiterInterface
{
    /**
     * Пытается выполнить запрос в рамках лимита.
     *
     * @param string $key Ключ для идентификации лимита
     *
     * @return bool True если запрос разрешён, false если превышен лимит
     */
    public function attempt(string $key): bool;

    /**
     * Проверяет, превышен ли лимит запросов.
     *
     * @param string $key Ключ для идентификации лимита
     *
     * @return bool True если лимит превышен
     */
    public function tooManyAttempts(string $key): bool;

    /**
     * Возвращает количество секунд до следующей доступной попытки.
     *
     * @param string $key Ключ для идентификации лимита
     *
     * @return int Количество секунд
     */
    public function availableIn(string $key): int;
}

<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\Contracts;

use Psr\Http\Message\ResponseInterface;

/**
 * Интерфейс для стратегий кеширования HTTP-ответов.
 */
interface CacheStrategyInterface
{
    /**
     * Определяет, можно ли кешировать данный ответ.
     *
     * @param ResponseInterface $response HTTP-ответ
     *
     * @return bool true если ответ можно кешировать
     */
    public function canCacheResponse(ResponseInterface $response): bool;
}

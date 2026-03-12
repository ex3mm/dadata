<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\Cache;

use Kevinrob\GuzzleCache\Strategy\PrivateCacheStrategy;
use Psr\Http\Message\ResponseInterface;

/**
 * Стратегия кеширования только успешных ответов (2xx).
 */
final class SuccessOnlyCacheStrategy extends PrivateCacheStrategy
{
    /**
     * {@inheritDoc}
     */
    #[\Override]
    protected function getCacheObject(\Psr\Http\Message\RequestInterface $request, ResponseInterface $response): ?\Kevinrob\GuzzleCache\CacheEntry
    {
        $statusCode = $response->getStatusCode();

        // Кешируем только успешные ответы (2xx)
        if ($statusCode < 200 || $statusCode >= 300) {
            return null;
        }

        return parent::getCacheObject($request, $response);
    }
}

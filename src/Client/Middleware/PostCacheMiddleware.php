<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\Client\Middleware;

use GuzzleHttp\Promise\PromiseInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Psr\SimpleCache\CacheInterface;

/**
 * Middleware для кеширования POST-запросов.
 *
 * Кеширует успешные POST-запросы (2xx) на основе URL и тела запроса.
 */
final class PostCacheMiddleware
{
    /**
     * @param CacheInterface $cache PSR-16 кеш
     * @param int $ttl Время жизни кеша в секундах
     * @param LoggerInterface|null $logger Логгер для отладки (опционально)
     */
    public function __construct(
        private readonly CacheInterface $cache,
        private readonly int $ttl = 3600,
        private readonly ?LoggerInterface $logger = null,
    ) {
    }

    /**
     * Возвращает Guzzle middleware функцию.
     */
    public function __invoke(callable $handler): callable
    {
        return function (RequestInterface $request, array $options) use ($handler): PromiseInterface {
            // Кешируем только POST-запросы
            if ($request->getMethod() !== 'POST') {
                /** @var PromiseInterface */
                return $handler($request, $options);
            }

            // Генерируем ключ кеша на основе URL и тела запроса
            $body     = (string) $request->getBody();
            $cacheKey = 'dadata_post_' . md5($request->getUri() . $body);

            // Сбрасываем позицию stream после чтения
            if ($request->getBody()->isSeekable()) {
                $request->getBody()->rewind();
            }

            // Проверяем кеш
            try {
                /** @var array{status: int, headers: array<string, array<string>>, body: string}|null $cachedData */
                $cachedData = $this->cache->get($cacheKey);

                if ($cachedData !== null && is_array($cachedData)) {
                    // Восстанавливаем ответ из кеша
                    $response = new \GuzzleHttp\Psr7\Response(
                        status: $cachedData['status'],
                        headers: array_merge($cachedData['headers'], ['X-Kevinrob-Cache' => 'HIT']),
                        body: $cachedData['body']
                    );

                    /** @var PromiseInterface */
                    return \GuzzleHttp\Promise\Create::promiseFor($response);
                }
            } catch (\Throwable $e) {
                $this->logger?->error('[CACHE] Error reading cache: ' . $e->getMessage());
            }

            /** @var PromiseInterface $promise */
            $promise = $handler($request, $options);

            return $promise->then(
                function (ResponseInterface $response) use ($cacheKey): ResponseInterface {
                    $statusCode = $response->getStatusCode();

                    // Кешируем только успешные ответы (2xx)
                    if ($statusCode >= 200 && $statusCode < 300) {
                        try {
                            // Читаем тело ответа
                            $body = (string) $response->getBody();

                            // Сохраняем в кеш
                            $this->cache->set($cacheKey, [
                                'status'  => $statusCode,
                                'headers' => $response->getHeaders(),
                                'body'    => $body,
                            ], $this->ttl);

                            // Восстанавливаем тело ответа (stream был прочитан)
                            $response = $response->withBody(\GuzzleHttp\Psr7\Utils::streamFor($body));

                            // Добавляем заголовок MISS
                            $response = $response->withHeader('X-Kevinrob-Cache', 'MISS');
                        } catch (\Throwable $e) {
                            $this->logger?->error('[CACHE] Error saving to cache: ' . $e->getMessage());
                        }
                    }

                    return $response;
                }
            );
        };
    }
}

<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\Client\Middleware;

use GuzzleHttp\Promise\PromiseInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * Middleware для логирования HTTP-запросов и ответов.
 */
final class LoggingMiddleware
{
    /**
     * @param LoggerInterface $logger Логгер для записи запросов и ответов
     * @param string $level Уровень логирования (debug, info, warning, error)
     * @param bool $logRequestBody Логировать тело запроса
     * @param bool $logResponseBody Логировать тело ответа
     */
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly string $level,
        private readonly bool $logRequestBody,
        private readonly bool $logResponseBody,
    ) {
    }

    /**
     * Возвращает Guzzle middleware функцию.
     */
    public function __invoke(callable $handler): callable
    {
        return function (RequestInterface $request, array $options) use ($handler): PromiseInterface {
            $startTime = microtime(true);

            // Логируем запрос
            $this->logger->log(
                $this->level,
                sprintf('%s %s', $request->getMethod(), (string) $request->getUri())
            );

            if ($this->logRequestBody) {
                $body = (string) $request->getBody();

                // Декодируем JSON и кодируем обратно с читаемой кириллицей
                $decoded = json_decode($body, true);
                if (is_array($decoded)) {
                    $body = json_encode($decoded, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                }

                $this->logger->debug('Request body: ' . $body);
            }

            /** @var PromiseInterface $promise */
            $promise = $handler($request, $options);

            return $promise->then(
                function (ResponseInterface $response) use ($startTime, $request): ResponseInterface {
                    $duration = round((microtime(true) - $startTime) * 1000, 2);

                    // Проверяем статус кеша
                    $cacheStatus = '';
                    if ($response->hasHeader('X-Kevinrob-Cache')) {
                        $cacheHeader = $response->getHeaderLine('X-Kevinrob-Cache');
                        $cacheStatus = match ($cacheHeader) {
                            'HIT'   => ' [CACHED]',
                            'MISS'  => ' [NOT CACHED]',
                            'STALE' => ' [STALE CACHE]',
                            default => '',
                        };
                    }

                    $this->logger->log(
                        $this->level,
                        sprintf(
                            'Response %d for %s %s (%s ms)%s',
                            $response->getStatusCode(),
                            $request->getMethod(),
                            (string) $request->getUri(),
                            $duration,
                            $cacheStatus
                        )
                    );

                    if ($this->logResponseBody) {
                        $body = (string) $response->getBody();

                        // Декодируем JSON и кодируем обратно с читаемой кириллицей
                        $decoded = json_decode($body, true);
                        if (is_array($decoded)) {
                            $body = json_encode($decoded, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                        }

                        $this->logger->debug('Response body: ' . $body);
                    }

                    return $response;
                },
                function (\Throwable $exception) use ($startTime, $request): never {
                    $duration = round((microtime(true) - $startTime) * 1000, 2);

                    $this->logger->error(
                        sprintf(
                            'Error for %s %s after %s ms: %s',
                            $request->getMethod(),
                            (string) $request->getUri(),
                            $duration,
                            $exception->getMessage()
                        )
                    );

                    throw $exception;
                }
            );
        };
    }
}

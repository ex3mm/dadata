<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\Client\Middleware;

use Ex3mm\Dadata\Contracts\RateLimiterInterface;
use Ex3mm\Dadata\Exceptions\RateLimitException;
use GuzzleHttp\Promise\PromiseInterface;
use Psr\Http\Message\RequestInterface;

/**
 * Middleware для ограничения частоты запросов.
 *
 * Использует RateLimiterInterface для проверки лимитов.
 * Каждый клиент должен иметь уникальный ключ для изоляции лимитов.
 */
final class RateLimiterMiddleware
{
    /**
     * @param RateLimiterInterface $rateLimiter Компонент ограничения частоты запросов
     * @param string $key Уникальный ключ для идентификации лимита (обычно хеш токена)
     */
    public function __construct(
        private readonly RateLimiterInterface $rateLimiter,
        private readonly string $key,
    ) {
    }

    /**
     * Возвращает Guzzle middleware функцию.
     *
     * @return callable(RequestInterface, array<string, mixed>): PromiseInterface
     */
    public function __invoke(callable $handler): callable
    {
        return function (RequestInterface $request, array $options) use ($handler): PromiseInterface {
            if ($this->rateLimiter->tooManyAttempts($this->key)) {
                $retryAfter = $this->rateLimiter->availableIn($this->key);
                throw new RateLimitException(
                    message: 'Превышен лимит запросов к DaData API',
                    retryAfter: $retryAfter
                );
            }

            $this->rateLimiter->attempt($this->key);

            /** @var PromiseInterface */
            return $handler($request, $options);
        };
    }
}

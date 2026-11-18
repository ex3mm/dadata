<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\Client\Middleware;

use Ex3mm\Dadata\Contracts\RateLimiterInterface;
use Ex3mm\Dadata\Exceptions\RateLimitException;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;

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
     * @param string|null $baseUrl Базовый URL запросов, к которым применяется лимит
     */
    public function __construct(
        private readonly RateLimiterInterface $rateLimiter,
        private readonly string $key,
        ?string $baseUrl = null,
    ) {
        $this->baseUri = $baseUrl !== null ? new Uri($baseUrl) : null;
    }

    private readonly ?UriInterface $baseUri;

    /**
     * Возвращает Guzzle middleware функцию.
     *
     * @return callable(RequestInterface, array<string, mixed>): PromiseInterface
     */
    public function __invoke(callable $handler): callable
    {
        return function (RequestInterface $request, array $options) use ($handler): PromiseInterface {
            if (!$this->supports($request)) {
                return $handler($request, $options);
            }

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

    /**
     * Проверяет, относится ли запрос к настроенному API.
     */
    private function supports(RequestInterface $request): bool
    {
        if (!$this->baseUri instanceof Uri) {
            return true;
        }

        $requestUri = $request->getUri();

        if (
            $requestUri->getScheme()  !== $this->baseUri->getScheme()
            || $requestUri->getHost() !== $this->baseUri->getHost()
            || $requestUri->getPort() !== $this->baseUri->getPort()
        ) {
            return false;
        }

        $basePath = rtrim($this->baseUri->getPath(), '/');

        return $basePath              === ''
            || $requestUri->getPath() === $basePath
            || str_starts_with($requestUri->getPath(), $basePath . '/');
    }
}

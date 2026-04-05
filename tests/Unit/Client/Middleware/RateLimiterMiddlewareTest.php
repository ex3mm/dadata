<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\Tests\Unit\Client\Middleware;

use Ex3mm\Dadata\Client\DadataRateLimiter;
use Ex3mm\Dadata\Client\Middleware\RateLimiterMiddleware;
use GuzzleHttp\Promise\Create;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\SimpleCache\CacheInterface;

/**
 * @covers \Ex3mm\Dadata\Client\Middleware\RateLimiterMiddleware
 */
final class RateLimiterMiddlewareTest extends TestCase
{
    public function testAllowsRequestsWithinLimit(): void
    {
        $cache = $this->createMock(CacheInterface::class);
        $cache->method('get')->willReturn([]);
        $cache->method('set')->willReturn(true);

        $rateLimiter = new DadataRateLimiter($cache, 3, 1);
        $middleware  = new RateLimiterMiddleware($rateLimiter, 'test_key');
        $handler     = fn () => Create::promiseFor(null);
        $request     = $this->createMock(RequestInterface::class);

        $callable = $middleware($handler);

        // Execute 3 requests (within limit of 3/sec)
        $start = microtime(true);
        $callable($request, []);
        $callable($request, []);
        $callable($request, []);
        $elapsed = microtime(true) - $start;

        // Should complete without significant delay (< 100ms)
        $this->assertLessThan(0.1, $elapsed, 'Requests within limit should not be delayed');
    }

    public function testBlocksRequestsExceedingLimit(): void
    {
        $cache = $this->createMock(CacheInterface::class);

        // Simulate 3 requests already made in the current second
        $now        = microtime(true);
        $timestamps = [$now - 0.1, $now - 0.2, $now - 0.3];

        $cache->method('get')->willReturn($timestamps);
        $cache->method('set')->willReturn(true);

        $rateLimiter = new DadataRateLimiter($cache, 3, 1);
        $middleware  = new RateLimiterMiddleware($rateLimiter, 'test_key');
        $handler     = fn () => Create::promiseFor(null);
        $request     = $this->createMock(RequestInterface::class);

        $callable = $middleware($handler);

        // 4th request should throw RateLimitException
        $this->expectException(\Ex3mm\Dadata\Exceptions\RateLimitException::class);
        $callable($request, []);
    }

    public function testRemovesTimestampsOlderThanOneSecond(): void
    {
        $cache = $this->createMock(CacheInterface::class);

        $now = microtime(true);
        // Mix of old and recent timestamps
        $timestamps = [
            $now - 2.0,  // Old - should be removed
            $now - 1.5,  // Old - should be removed
            $now - 0.5,  // Recent - should be kept
        ];

        $cache->method('get')->willReturn($timestamps);
        $cache->method('set')->willReturn(true);

        $rateLimiter = new DadataRateLimiter($cache, 3, 1);
        $middleware  = new RateLimiterMiddleware($rateLimiter, 'test_key');
        $handler     = fn () => Create::promiseFor(null);
        $request     = $this->createMock(RequestInterface::class);

        $callable = $middleware($handler);
        $callable($request, []);

        // Test passes if no exception thrown
        $this->assertTrue(true);
    }

    public function testResetsCounterAfterTimeWindow(): void
    {
        $cache = $this->createMock(CacheInterface::class);

        // Simulate 3 requests made 1.5 seconds ago (all expired)
        $now        = microtime(true);
        $timestamps = [$now - 1.5, $now - 1.6, $now - 1.7];

        $cache->method('get')->willReturn($timestamps);
        $cache->method('set')->willReturn(true);

        $rateLimiter = new DadataRateLimiter($cache, 3, 1);
        $middleware  = new RateLimiterMiddleware($rateLimiter, 'test_key');
        $handler     = fn () => Create::promiseFor(null);
        $request     = $this->createMock(RequestInterface::class);

        $callable = $middleware($handler);

        // All old timestamps expired, so 3 new requests should pass without delay
        $start = microtime(true);
        $callable($request, []);
        $callable($request, []);
        $callable($request, []);
        $elapsed = microtime(true) - $start;

        $this->assertLessThan(0.1, $elapsed, 'Requests after window expiry should not be delayed');
    }

    public function testHandlesEmptyCache(): void
    {
        $cache = $this->createMock(CacheInterface::class);
        $cache->method('get')->willReturn([]);
        $cache->method('set')->willReturn(true);

        $rateLimiter = new DadataRateLimiter($cache, 5, 1);
        $middleware  = new RateLimiterMiddleware($rateLimiter, 'test_key');
        $handler     = fn () => Create::promiseFor(null);
        $request     = $this->createMock(RequestInterface::class);

        $callable = $middleware($handler);

        // First request with empty cache should work immediately
        $start = microtime(true);
        $callable($request, []);
        $elapsed = microtime(true) - $start;

        $this->assertLessThan(0.05, $elapsed, 'First request should not be delayed');
    }

    public function testHandlesNonArrayCacheValue(): void
    {
        $cache = $this->createMock(CacheInterface::class);
        $cache->method('get')->willReturn('invalid');
        $cache->method('set')->willReturn(true);

        $rateLimiter = new DadataRateLimiter($cache, 5, 1);
        $middleware  = new RateLimiterMiddleware($rateLimiter, 'test_key');
        $handler     = fn () => Create::promiseFor(null);
        $request     = $this->createMock(RequestInterface::class);

        $callable = $middleware($handler);

        // Should handle invalid cache value gracefully
        $callable($request, []);

        $this->assertTrue(true, 'Should handle non-array cache value without error');
    }

    public function testIsolatesLimitsBetweenDifferentKeys(): void
    {
        // Используем InMemoryCache для реального хранения состояния
        $cache = new \Ex3mm\Dadata\Cache\InMemoryCache();

        $handler = fn () => Create::promiseFor(null);
        $request = $this->createMock(RequestInterface::class);

        // Создаём два клиента с разными ключами и лимитом 3 запроса/сек
        $rateLimiterA = new DadataRateLimiter($cache, 3, 1);
        $middlewareA  = new RateLimiterMiddleware($rateLimiterA, 'client_a');
        $callableA    = $middlewareA($handler);

        $rateLimiterB = new DadataRateLimiter($cache, 3, 1);
        $middlewareB  = new RateLimiterMiddleware($rateLimiterB, 'client_b');
        $callableB    = $middlewareB($handler);

        // Клиент A делает 3 запроса (исчерпывает лимит)
        $callableA($request, []);
        $callableA($request, []);
        $callableA($request, []);

        // Клиент A должен получить исключение на 4-м запросе
        try {
            $callableA($request, []);
            $this->fail('Client A should throw RateLimitException after 3 requests');
        } catch (\Ex3mm\Dadata\Exceptions\RateLimitException $e) {
            $this->assertTrue(true, 'Client A correctly blocked after exceeding limit');
        }

        // Клиент B с другим ключом должен работать нормально (свой лимит)
        $callableB($request, []);
        $callableB($request, []);
        $callableB($request, []);

        $this->assertTrue(true, 'Client B has independent limit and is not affected by Client A');
    }
}

<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\Tests\Unit\Client\Middleware;

use Ex3mm\Dadata\Client\Middleware\RetryMiddleware;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Promise\Create;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * @covers \Ex3mm\Dadata\Client\Middleware\RetryMiddleware
 */
final class RetryMiddlewareTest extends TestCase
{
    public function testRetries429Response(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())->method('warning');

        $middleware = new RetryMiddleware(3, 10, $logger);

        $callCount = 0;
        $handler   = function () use (&$callCount) {
            $callCount++;
            if ($callCount === 1) {
                return Create::promiseFor(new Response(429));
            }
            return Create::promiseFor(new Response(200));
        };

        $request  = new Request('GET', 'https://api.example.com');
        $callable = $middleware($handler);

        $promise  = $callable($request, []);
        $response = $promise->wait();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(2, $callCount, 'Should retry once after 429');
    }

    public function testRetries500Response(): void
    {
        $logger     = $this->createMock(LoggerInterface::class);
        $middleware = new RetryMiddleware(3, 10, $logger);

        $callCount = 0;
        $handler   = function () use (&$callCount) {
            $callCount++;
            if ($callCount <= 2) {
                return Create::promiseFor(new Response(500));
            }
            return Create::promiseFor(new Response(200));
        };

        $request  = new Request('GET', 'https://api.example.com');
        $callable = $middleware($handler);

        $promise  = $callable($request, []);
        $response = $promise->wait();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(3, $callCount, 'Should retry twice after 500 errors');
    }

    public function testRetries502Response(): void
    {
        $logger     = $this->createMock(LoggerInterface::class);
        $middleware = new RetryMiddleware(2, 10, $logger);

        $callCount = 0;
        $handler   = function () use (&$callCount) {
            $callCount++;
            return Create::promiseFor($callCount === 1 ? new Response(502) : new Response(200));
        };

        $request  = new Request('GET', 'https://api.example.com');
        $callable = $middleware($handler);

        $response = $callable($request, [])->wait();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(2, $callCount);
    }

    public function testRetries503Response(): void
    {
        $logger     = $this->createMock(LoggerInterface::class);
        $middleware = new RetryMiddleware(2, 10, $logger);

        $callCount = 0;
        $handler   = function () use (&$callCount) {
            $callCount++;
            return Create::promiseFor($callCount === 1 ? new Response(503) : new Response(200));
        };

        $request  = new Request('GET', 'https://api.example.com');
        $callable = $middleware($handler);

        $response = $callable($request, [])->wait();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(2, $callCount);
    }

    public function testRetries504Response(): void
    {
        $logger     = $this->createMock(LoggerInterface::class);
        $middleware = new RetryMiddleware(2, 10, $logger);

        $callCount = 0;
        $handler   = function () use (&$callCount) {
            $callCount++;
            return Create::promiseFor($callCount === 1 ? new Response(504) : new Response(200));
        };

        $request  = new Request('GET', 'https://api.example.com');
        $callable = $middleware($handler);

        $response = $callable($request, [])->wait();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(2, $callCount);
    }

    public function testDoesNotRetry400Response(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->never())->method('warning');

        $middleware = new RetryMiddleware(3, 10, $logger);

        $callCount = 0;
        $handler   = function () use (&$callCount) {
            $callCount++;
            return Create::promiseFor(new Response(400));
        };

        $request  = new Request('GET', 'https://api.example.com');
        $callable = $middleware($handler);

        $response = $callable($request, [])->wait();

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals(1, $callCount, 'Should not retry 400 error');
    }

    public function testDoesNotRetry404Response(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->never())->method('warning');

        $middleware = new RetryMiddleware(3, 10, $logger);

        $callCount = 0;
        $handler   = function () use (&$callCount) {
            $callCount++;
            return Create::promiseFor(new Response(404));
        };

        $request  = new Request('GET', 'https://api.example.com');
        $callable = $middleware($handler);

        $response = $callable($request, [])->wait();

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals(1, $callCount, 'Should not retry 404 error');
    }

    public function testDoesNotRetry200Response(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->never())->method('warning');

        $middleware = new RetryMiddleware(3, 10, $logger);

        $callCount = 0;
        $handler   = function () use (&$callCount) {
            $callCount++;
            return Create::promiseFor(new Response(200));
        };

        $request  = new Request('GET', 'https://api.example.com');
        $callable = $middleware($handler);

        $response = $callable($request, [])->wait();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(1, $callCount, 'Should not retry successful response');
    }

    public function testReturns500AfterMaxAttempts(): void
    {
        $logger     = $this->createMock(LoggerInterface::class);
        $middleware = new RetryMiddleware(3, 10, $logger);

        $callCount = 0;
        $handler   = function () use (&$callCount) {
            $callCount++;
            return Create::promiseFor(new Response(500));
        };

        $request  = new Request('GET', 'https://api.example.com');
        $callable = $middleware($handler);

        $promise  = $callable($request, []);
        $response = $promise->wait();

        // After 3 attempts, should return the 500 response
        $this->assertEquals(500, $response->getStatusCode());
        $this->assertEquals(3, $callCount, 'Should attempt exactly maxAttempts times');
    }

    public function testExponentialBackoff(): void
    {
        $logger     = $this->createMock(LoggerInterface::class);
        $middleware = new RetryMiddleware(3, 100, $logger);

        $callCount = 0;
        $handler   = function () use (&$callCount) {
            $callCount++;
            if ($callCount <= 2) {
                return Create::promiseFor(new Response(500));
            }
            return Create::promiseFor(new Response(200));
        };

        $request  = new Request('GET', 'https://api.example.com');
        $callable = $middleware($handler);

        $start = microtime(true);
        $callable($request, [])->wait();
        $elapsed = microtime(true) - $start;

        // First retry: 100ms * 2^0 = 100ms
        // Second retry: 100ms * 2^1 = 200ms
        // Total: ~300ms
        $this->assertGreaterThan(0.25, $elapsed, 'Should apply exponential backoff');
        $this->assertLessThan(0.5, $elapsed, 'Backoff should not be too long');
    }

    public function testRetriesConnectException(): void
    {
        $logger     = $this->createMock(LoggerInterface::class);
        $middleware = new RetryMiddleware(2, 10, $logger);

        $callCount = 0;
        $request   = new Request('GET', 'https://api.example.com');

        $handler = function () use (&$callCount, $request) {
            $callCount++;
            if ($callCount === 1) {
                throw new ConnectException('Connection failed', $request);
            }
            return Create::promiseFor(new Response(200));
        };

        $callable = $middleware($handler);

        $response = $callable($request, [])->wait();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(2, $callCount, 'Should retry after ConnectException');
    }

    public function testThrowsConnectExceptionAfterMaxAttempts(): void
    {
        $this->expectException(ConnectException::class);

        $logger     = $this->createMock(LoggerInterface::class);
        $middleware = new RetryMiddleware(2, 10, $logger);

        $request = new Request('GET', 'https://api.example.com');

        $handler = function () use ($request): void {
            throw new ConnectException('Connection failed', $request);
        };

        $callable = $middleware($handler);
        $callable($request, [])->wait();
    }

    public function testRetriesRequestExceptionWith500(): void
    {
        $logger     = $this->createMock(LoggerInterface::class);
        $middleware = new RetryMiddleware(2, 10, $logger);

        $callCount = 0;
        $request   = new Request('GET', 'https://api.example.com');

        $handler = function () use (&$callCount, $request) {
            $callCount++;
            if ($callCount === 1) {
                throw new RequestException('Server error', $request, new Response(500));
            }
            return Create::promiseFor(new Response(200));
        };

        $callable = $middleware($handler);

        $response = $callable($request, [])->wait();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(2, $callCount, 'Should retry RequestException with 500 response');
    }

    public function testDoesNotRetryRequestExceptionWith400(): void
    {
        $this->expectException(RequestException::class);

        $logger     = $this->createMock(LoggerInterface::class);
        $middleware = new RetryMiddleware(3, 10, $logger);

        $request = new Request('GET', 'https://api.example.com');

        $handler = function () use ($request): void {
            throw new RequestException('Bad request', $request, new Response(400));
        };

        $callable = $middleware($handler);
        $callable($request, [])->wait();
    }
}

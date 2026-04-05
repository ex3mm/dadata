<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\Tests\Unit\Client\Middleware;

use Ex3mm\Dadata\Client\Middleware\LoggingMiddleware;
use GuzzleHttp\Promise\Create;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * @covers \Ex3mm\Dadata\Client\Middleware\LoggingMiddleware
 */
final class LoggingMiddlewareTest extends TestCase
{
    public function testLogsSuccessfulRequestAtInfoLevel(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->exactly(2))
            ->method('log')
            ->with(
                'info',
                $this->logicalOr(
                    $this->stringContains('GET https://api.example.com'),
                    $this->stringContains('Response 200')
                )
            );

        $middleware = new LoggingMiddleware($logger, 'info', false, false);

        $handler = fn () => Create::promiseFor(new Response(200));
        $request = new Request('GET', 'https://api.example.com');

        $callable = $middleware($handler);
        $callable($request, [])->wait();
    }

    public function testDoesNotLogRequestBodyWhenDisabled(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->never())->method('debug');

        $middleware = new LoggingMiddleware($logger, 'info', false, false);

        $handler = fn () => Create::promiseFor(new Response(200));
        $request = new Request('POST', 'https://api.example.com', [], '{"query":"test"}');

        $callable = $middleware($handler);
        $callable($request, [])->wait();
    }

    public function testLogsRequestBodyWhenEnabled(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())
            ->method('debug')
            ->with($this->stringContains('Request body:'));

        $middleware = new LoggingMiddleware($logger, 'info', true, false);

        $handler = fn () => Create::promiseFor(new Response(200));
        $request = new Request('POST', 'https://api.example.com', [], '{"query":"test"}');

        $callable = $middleware($handler);
        $callable($request, [])->wait();
    }

    public function testDoesNotLogResponseBodyWhenDisabled(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->never())
            ->method('debug')
            ->with($this->stringContains('Response body:'));

        $middleware = new LoggingMiddleware($logger, 'info', false, false);

        $handler = fn () => Create::promiseFor(new Response(200, [], '{"result":"ok"}'));
        $request = new Request('GET', 'https://api.example.com');

        $callable = $middleware($handler);
        $callable($request, [])->wait();
    }

    public function testLogsResponseBodyWhenEnabled(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())
            ->method('debug')
            ->with($this->stringContains('Response body:'));

        $middleware = new LoggingMiddleware($logger, 'info', false, true);

        $handler = fn () => Create::promiseFor(new Response(200, [], '{"result":"ok"}'));
        $request = new Request('GET', 'https://api.example.com');

        $callable = $middleware($handler);
        $callable($request, [])->wait();
    }

    public function testLogsErrorAtErrorLevel(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())
            ->method('log')
            ->with('info', $this->stringContains('GET'));
        $logger->expects($this->once())
            ->method('error')
            ->with($this->stringContains('Error for GET'));

        $middleware = new LoggingMiddleware($logger, 'info', false, false);

        $handler = (fn () => Create::rejectionFor(new \RuntimeException('Test error')));
        $request = new Request('GET', 'https://api.example.com');

        $callable = $middleware($handler);

        try {
            $callable($request, [])->wait();
            $this->fail('Expected RuntimeException to be thrown');
        } catch (\RuntimeException $e) {
            // Expected
            $this->assertEquals('Test error', $e->getMessage());
        }
    }

    public function testMeasuresRequestDuration(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->exactly(2))
            ->method('log');

        $middleware = new LoggingMiddleware($logger, 'info', false, false);

        $handler = function () {
            usleep(10000); // 10ms delay
            return Create::promiseFor(new Response(200));
        };
        $request = new Request('GET', 'https://api.example.com');

        $callable = $middleware($handler);
        $result   = $callable($request, [])->wait();

        $this->assertEquals(200, $result->getStatusCode());
    }

    public function testUsesConfiguredLogLevel(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->exactly(2))
            ->method('log')
            ->with(
                'debug',
                $this->logicalOr(
                    $this->stringContains('GET https://api.example.com'),
                    $this->stringContains('Response 200')
                )
            );

        $middleware = new LoggingMiddleware($logger, 'debug', false, false);

        $handler = fn () => Create::promiseFor(new Response(200));
        $request = new Request('GET', 'https://api.example.com');

        $callable = $middleware($handler);
        $callable($request, [])->wait();
    }
}

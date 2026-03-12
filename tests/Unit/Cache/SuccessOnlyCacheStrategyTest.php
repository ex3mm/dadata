<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\Tests\Unit\Cache;

use Ex3mm\Dadata\Cache\SuccessOnlyCacheStrategy;
use Ex3mm\Dadata\Tests\TestCase;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

final class SuccessOnlyCacheStrategyTest extends TestCase
{
    private SuccessOnlyCacheStrategy $strategy;

    protected function setUp(): void
    {
        parent::setUp();
        $cache          = new \Ex3mm\Dadata\Cache\InMemoryCache();
        $this->strategy = new SuccessOnlyCacheStrategy(
            new \Kevinrob\GuzzleCache\Storage\Psr16CacheStorage($cache),
            3600
        );
    }

    public function test_can_cache_2xx_responses(): void
    {
        $request  = new Request('GET', 'https://example.com');
        $response = new Response(200, [], 'OK');

        $reflection = new \ReflectionClass($this->strategy);
        $method     = $reflection->getMethod('getCacheObject');

        $cacheEntry = $method->invoke($this->strategy, $request, $response);
        $this->assertNotNull($cacheEntry, 'Expected 2xx response to be cacheable');
    }

    public function test_cannot_cache_4xx_responses(): void
    {
        $request  = new Request('GET', 'https://example.com/404');
        $response = new Response(404, [], 'Not Found');

        $reflection = new \ReflectionClass($this->strategy);
        $method     = $reflection->getMethod('getCacheObject');

        $cacheEntry = $method->invoke($this->strategy, $request, $response);
        $this->assertNull($cacheEntry, 'Expected 4xx response to NOT be cacheable');
    }

    public function test_cannot_cache_5xx_responses(): void
    {
        $request  = new Request('GET', 'https://example.com/error');
        $response = new Response(500, [], 'Error');

        $reflection = new \ReflectionClass($this->strategy);
        $method     = $reflection->getMethod('getCacheObject');

        $cacheEntry = $method->invoke($this->strategy, $request, $response);
        $this->assertNull($cacheEntry, 'Expected 5xx response to NOT be cacheable');
    }
}

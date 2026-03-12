<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\Tests\Unit\Cache;

use Ex3mm\Dadata\Cache\InMemoryCache;
use Ex3mm\Dadata\Tests\TestCase;

final class InMemoryCacheTest extends TestCase
{
    private InMemoryCache $cache;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cache = new InMemoryCache();
    }

    public function test_get_returns_default_for_missing_key(): void
    {
        $this->assertNull($this->cache->get('missing'));
        $this->assertSame('default', $this->cache->get('missing', 'default'));
    }

    public function test_set_and_get_work_correctly(): void
    {
        $this->cache->set('key', 'value');
        $this->assertSame('value', $this->cache->get('key'));
    }

    public function test_has_returns_true_for_existing_key(): void
    {
        $this->cache->set('key', 'value');
        $this->assertTrue($this->cache->has('key'));
    }

    public function test_has_returns_false_for_missing_key(): void
    {
        $this->assertFalse($this->cache->has('missing'));
    }

    public function test_delete_removes_key(): void
    {
        $this->cache->set('key', 'value');
        $this->cache->delete('key');
        $this->assertFalse($this->cache->has('key'));
    }

    public function test_clear_removes_all_keys(): void
    {
        $this->cache->set('key1', 'value1');
        $this->cache->set('key2', 'value2');
        $this->cache->clear();
        $this->assertFalse($this->cache->has('key1'));
        $this->assertFalse($this->cache->has('key2'));
    }

    public function test_ttl_expires_correctly(): void
    {
        $this->cache->set('key', 'value', 1);
        $this->assertTrue($this->cache->has('key'));

        sleep(2);

        $this->assertFalse($this->cache->has('key'));
        $this->assertNull($this->cache->get('key'));
    }

    public function test_null_ttl_means_no_expiration(): void
    {
        $this->cache->set('key', 'value');
        $this->assertTrue($this->cache->has('key'));

        sleep(1);

        $this->assertTrue($this->cache->has('key'));
    }

    public function test_get_multiple_works(): void
    {
        $this->cache->set('key1', 'value1');
        $this->cache->set('key2', 'value2');

        $result = $this->cache->getMultiple(['key1', 'key2', 'key3'], 'default');

        $this->assertSame([
            'key1' => 'value1',
            'key2' => 'value2',
            'key3' => 'default',
        ], $result);
    }

    public function test_set_multiple_works(): void
    {
        $this->cache->setMultiple([
            'key1' => 'value1',
            'key2' => 'value2',
        ]);

        $this->assertSame('value1', $this->cache->get('key1'));
        $this->assertSame('value2', $this->cache->get('key2'));
    }

    public function test_delete_multiple_works(): void
    {
        $this->cache->set('key1', 'value1');
        $this->cache->set('key2', 'value2');

        $this->cache->deleteMultiple(['key1', 'key2']);

        $this->assertFalse($this->cache->has('key1'));
        $this->assertFalse($this->cache->has('key2'));
    }
}

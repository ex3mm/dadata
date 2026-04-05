<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\Tests\Laravel;

use Ex3mm\Dadata\Contracts\DadataClientInterface;
use Ex3mm\Dadata\Laravel\Facades\Dadata;
use ReflectionClass;

final class DadataFacadeTest extends LaravelTestCase
{
    public function test_get_facade_accessor_returns_correct_service_name(): void
    {
        // Используем рефлексию для доступа к protected методу
        $reflection = new ReflectionClass(Dadata::class);
        $method     = $reflection->getMethod('getFacadeAccessor');

        $accessor = $method->invoke(null);

        $this->assertSame(DadataClientInterface::class, $accessor);
    }
}

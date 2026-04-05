<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\Tests\Unit\Requests;

use Ex3mm\Dadata\Endpoints\AbstractEndpoint;
use Ex3mm\Dadata\Exceptions\ValidationException;
use Ex3mm\Dadata\Requests\CleanAddressRequest;
use Ex3mm\Dadata\Tests\TestCase;
use ReflectionClass;

final class CleanAddressRequestTest extends TestCase
{
    private CleanAddressRequest $request;

    protected function setUp(): void
    {
        parent::setUp();

        $endpoint = $this->getMockBuilder(AbstractEndpoint::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->request = new CleanAddressRequest($endpoint);
    }

    public function test_query_sets_value(): void
    {
        $result = $this->request->query(' мск сухонска 11/-89 ');

        $this->assertSame($this->request, $result);
        $this->assertSame('мск сухонска 11/-89', $this->getPrivateProperty($this->request, 'query'));
    }

    public function test_to_array_returns_single_item_array(): void
    {
        $this->request->query('мск сухонска 11/-89');

        $array = $this->callPrivateMethod($this->request, 'toArray');

        $this->assertSame(['мск сухонска 11/-89'], $array);
    }

    public function test_validate_throws_exception_for_empty_query(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Адрес для стандартизации не может быть пустым');

        $this->callPrivateMethod($this->request, 'validate');
    }

    public function test_validate_passes_for_valid_query(): void
    {
        $this->request->query('мск сухонска 11/-89');

        $this->callPrivateMethod($this->request, 'validate');
        $this->assertTrue(true);
    }

    private function getPrivateProperty(object $object, string $propertyName): mixed
    {
        $reflection = new ReflectionClass($object);
        $property   = $reflection->getProperty($propertyName);

        return $property->getValue($object);
    }

    private function callPrivateMethod(object $object, string $methodName, array $args = []): mixed
    {
        $reflection = new ReflectionClass($object);
        $method     = $reflection->getMethod($methodName);

        return $method->invoke($object, ...$args);
    }
}

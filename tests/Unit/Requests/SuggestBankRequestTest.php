<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\Tests\Unit\Requests;

use Ex3mm\Dadata\Endpoints\AbstractEndpoint;
use Ex3mm\Dadata\Exceptions\ValidationException;
use Ex3mm\Dadata\Requests\SuggestBankRequest;
use Ex3mm\Dadata\Tests\TestCase;
use ReflectionClass;

final class SuggestBankRequestTest extends TestCase
{
    private SuggestBankRequest $request;

    protected function setUp(): void
    {
        parent::setUp();

        $endpoint = $this->getMockBuilder(AbstractEndpoint::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->request = new SuggestBankRequest($endpoint);
    }

    public function test_query_sets_value(): void
    {
        $result = $this->request->query('сбербанк');

        $this->assertSame($this->request, $result);
        $this->assertSame('сбербанк', $this->getPrivateProperty($this->request, 'query'));
    }

    public function test_to_array_includes_optional_fields(): void
    {
        $this->request
            ->query('сбербанк')
            ->count(5)
            ->status(['ACTIVE'])
            ->type(['BANK'])
            ->locations([['region' => 'Москва']])
            ->locationsBoost([['kladr_id' => '7700000000000']]);

        $array = $this->callPrivateMethod($this->request, 'toArray');

        $this->assertSame('сбербанк', $array['query']);
        $this->assertSame(5, $array['count']);
        $this->assertSame(['ACTIVE'], $array['status']);
        $this->assertSame(['BANK'], $array['type']);
        $this->assertArrayHasKey('locations', $array);
        $this->assertArrayHasKey('locations_boost', $array);
    }

    public function test_validate_throws_for_empty_query(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Поисковый запрос не может быть пустым');

        $this->callPrivateMethod($this->request, 'validate');
    }

    public function test_validate_passes_for_valid_query(): void
    {
        $this->request->query('сбербанк');
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

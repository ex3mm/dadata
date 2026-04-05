<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\Tests\Unit\Requests;

use Ex3mm\Dadata\Endpoints\AbstractEndpoint;
use Ex3mm\Dadata\Enums\PartyBranchType;
use Ex3mm\Dadata\Enums\PartyStateStatus;
use Ex3mm\Dadata\Enums\PartyType;
use Ex3mm\Dadata\Exceptions\ValidationException;
use Ex3mm\Dadata\Requests\FindPartyRequest;
use Ex3mm\Dadata\Tests\TestCase;
use ReflectionClass;

final class FindPartyRequestTest extends TestCase
{
    private FindPartyRequest $request;

    protected function setUp(): void
    {
        parent::setUp();

        $endpoint = $this->getMockBuilder(AbstractEndpoint::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->request = new FindPartyRequest($endpoint);
    }

    public function test_query_sets_value(): void
    {
        $result = $this->request->query('7707083893');

        $this->assertSame($this->request, $result);
        $this->assertSame('7707083893', $this->getPrivateProperty($this->request, 'query'));
    }

    public function test_to_array_contains_query_and_count(): void
    {
        $this->request
            ->query('1027700132195')
            ->count(1);

        $array = $this->callPrivateMethod($this->request, 'toArray');

        $this->assertSame('1027700132195', $array['query']);
        $this->assertSame(1, $array['count']);
    }

    public function test_to_array_includes_optional_kpp(): void
    {
        $this->request
            ->query('7701234567')
            ->count(1)
            ->kpp('770101001');

        $array = $this->callPrivateMethod($this->request, 'toArray');

        $this->assertSame('770101001', $array['kpp']);
    }

    public function test_to_array_includes_optional_filters(): void
    {
        $this->request
            ->query('7701234567')
            ->branchType(PartyBranchType::BRANCH)
            ->type(PartyType::LEGAL)
            ->status([PartyStateStatus::ACTIVE, PartyStateStatus::LIQUIDATING]);

        $array = $this->callPrivateMethod($this->request, 'toArray');

        $this->assertSame('BRANCH', $array['branch_type']);
        $this->assertSame('LEGAL', $array['type']);
        $this->assertSame(['ACTIVE', 'LIQUIDATING'], $array['status']);
    }

    public function test_validate_throws_for_empty_query(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Поисковый запрос не может быть пустым');

        $this->callPrivateMethod($this->request, 'validate');
    }

    public function test_validate_passes_for_valid_query(): void
    {
        $this->request->query('7707083893');
        $this->callPrivateMethod($this->request, 'validate');

        $this->assertTrue(true);
    }

    public function test_validate_throws_for_count_more_than_300(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Количество результатов не может быть больше 300');

        $this->request
            ->query('7707083893')
            ->count(301);

        $this->callPrivateMethod($this->request, 'validate');
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

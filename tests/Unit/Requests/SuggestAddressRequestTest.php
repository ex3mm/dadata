<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\Tests\Unit\Requests;

use Ex3mm\Dadata\Endpoints\AbstractEndpoint;
use Ex3mm\Dadata\Enums\AddressBound;
use Ex3mm\Dadata\Enums\Language;
use Ex3mm\Dadata\Exceptions\ValidationException;
use Ex3mm\Dadata\Requests\SuggestAddressRequest;
use Ex3mm\Dadata\Tests\TestCase;
use ReflectionClass;

final class SuggestAddressRequestTest extends TestCase
{
    private SuggestAddressRequest $request;

    protected function setUp(): void
    {
        parent::setUp();

        $endpoint = $this->getMockBuilder(AbstractEndpoint::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->request = new SuggestAddressRequest($endpoint);
    }

    public function test_query_sets_query_string(): void
    {
        $result = $this->request->query('Москва');

        $this->assertSame($this->request, $result);
        $this->assertSame('Москва', $this->getPrivateProperty($this->request, 'query'));
    }

    public function test_count_sets_count_value(): void
    {
        $result = $this->request->count(5);

        $this->assertSame($this->request, $result);
        $this->assertSame(5, $this->getPrivateProperty($this->request, 'count'));
    }

    public function test_from_bound_sets_lower_bound(): void
    {
        $result = $this->request->fromBound(AddressBound::CITY);

        $this->assertSame($this->request, $result);
        $this->assertSame(AddressBound::CITY, $this->getPrivateProperty($this->request, 'fromBound'));
    }

    public function test_to_bound_sets_upper_bound(): void
    {
        $result = $this->request->toBound(AddressBound::STREET);

        $this->assertSame($this->request, $result);
        $this->assertSame(AddressBound::STREET, $this->getPrivateProperty($this->request, 'toBound'));
    }

    public function test_language_sets_language(): void
    {
        $result = $this->request->language(Language::EN);

        $this->assertSame($this->request, $result);
        $this->assertSame(Language::EN, $this->getPrivateProperty($this->request, 'language'));
    }

    public function test_division_sets_division_type(): void
    {
        $result = $this->request->division('municipal');

        $this->assertSame($this->request, $result);
        $this->assertSame('municipal', $this->getPrivateProperty($this->request, 'division'));
    }

    public function test_locations_sets_location_restrictions(): void
    {
        $locations = [['city' => 'Москва']];
        $result    = $this->request->locations($locations);

        $this->assertSame($this->request, $result);
        $this->assertSame($locations, $this->getPrivateProperty($this->request, 'locations'));
    }

    public function test_locations_geo_sets_geo_restrictions(): void
    {
        $locationsGeo = [['lat' => 55.7558, 'lon' => 37.6173, 'radius_km' => 10]];
        $result       = $this->request->locationsGeo($locationsGeo);

        $this->assertSame($this->request, $result);
        $this->assertSame($locationsGeo, $this->getPrivateProperty($this->request, 'locationsGeo'));
    }

    public function test_locations_boost_sets_city_priority(): void
    {
        $locationsBoost = [['kladr_id' => '7700000000000']];
        $result         = $this->request->locationsBoost($locationsBoost);

        $this->assertSame($this->request, $result);
        $this->assertSame($locationsBoost, $this->getPrivateProperty($this->request, 'locationsBoost'));
    }

    public function test_to_array_returns_minimal_data(): void
    {
        $this->request->query('Москва');

        $array = $this->callPrivateMethod($this->request, 'toArray');

        $this->assertIsArray($array);
        $this->assertArrayHasKey('query', $array);
        $this->assertArrayHasKey('count', $array);
        $this->assertArrayHasKey('language', $array);
        $this->assertSame('Москва', $array['query']);
        $this->assertSame(10, $array['count']);
        $this->assertSame('ru', $array['language']);
    }

    public function test_to_array_includes_optional_parameters(): void
    {
        $this->request
            ->query('Москва')
            ->count(5)
            ->language(Language::EN)
            ->division('municipal')
            ->fromBound(AddressBound::CITY)
            ->toBound(AddressBound::STREET)
            ->locations([['city' => 'Москва']])
            ->locationsGeo([['lat' => 55.7558, 'lon' => 37.6173, 'radius_km' => 10]])
            ->locationsBoost([['kladr_id' => '7700000000000']]);

        $array = $this->callPrivateMethod($this->request, 'toArray');

        $this->assertArrayHasKey('query', $array);
        $this->assertArrayHasKey('count', $array);
        $this->assertArrayHasKey('language', $array);
        $this->assertArrayHasKey('division', $array);
        $this->assertArrayHasKey('from_bound', $array);
        $this->assertArrayHasKey('to_bound', $array);
        $this->assertArrayHasKey('locations', $array);
        $this->assertArrayHasKey('locations_geo', $array);
        $this->assertArrayHasKey('locations_boost', $array);

        $this->assertSame('Москва', $array['query']);
        $this->assertSame(5, $array['count']);
        $this->assertSame('en', $array['language']);
        $this->assertSame('municipal', $array['division']);
        $this->assertSame(['value' => 'city'], $array['from_bound']);
        $this->assertSame(['value' => 'street'], $array['to_bound']);
    }

    public function test_validate_throws_exception_for_empty_query(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Поисковый запрос не может быть пустым');

        $this->callPrivateMethod($this->request, 'validate');
    }

    public function test_validate_passes_for_valid_query(): void
    {
        $this->request->query('Москва');

        // Не должно выбросить исключение
        $this->callPrivateMethod($this->request, 'validate');

        $this->assertTrue(true);
    }

    public function test_fluent_interface_allows_method_chaining(): void
    {
        $result = $this->request
            ->query('Москва')
            ->count(5)
            ->language(Language::RU)
            ->fromBound(AddressBound::CITY)
            ->toBound(AddressBound::STREET);

        $this->assertSame($this->request, $result);
    }

    /**
     * Получает значение приватного свойства через рефлексию.
     */
    private function getPrivateProperty(object $object, string $propertyName): mixed
    {
        $reflection = new ReflectionClass($object);
        $property   = $reflection->getProperty($propertyName);

        return $property->getValue($object);
    }

    /**
     * Вызывает приватный метод через рефлексию.
     */
    private function callPrivateMethod(object $object, string $methodName, array $args = []): mixed
    {
        $reflection = new ReflectionClass($object);
        $method     = $reflection->getMethod($methodName);

        return $method->invoke($object, ...$args);
    }
}

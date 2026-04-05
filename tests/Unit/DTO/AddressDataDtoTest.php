<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\Tests\Unit\DTO;

use Ex3mm\Dadata\DTO\Response\Shared\AddressDataDto;
use Ex3mm\Dadata\Tests\TestCase;

final class AddressDataDtoTest extends TestCase
{
    public function test_extract_string_handles_valid_string(): void
    {
        $data = [
            'postal_code' => '394036',
            'country'     => 'Россия',
        ];

        $dto = AddressDataDto::fromArray($data);

        $this->assertSame('394036', $dto->postalCode);
        $this->assertSame('Россия', $dto->country);
    }

    public function test_extract_string_returns_null_for_missing_key(): void
    {
        $data = [];

        $dto = AddressDataDto::fromArray($data);

        $this->assertNull($dto->postalCode);
        $this->assertNull($dto->country);
    }

    public function test_extract_string_returns_null_for_non_string_value(): void
    {
        $data = [
            'postal_code' => 123, // Не строка
            'country'     => ['array'], // Не строка
        ];

        $dto = AddressDataDto::fromArray($data);

        $this->assertNull($dto->postalCode);
        $this->assertNull($dto->country);
    }

    public function test_extract_int_handles_valid_integer(): void
    {
        $data = [
            'house_flat_count' => 50,
        ];

        $dto = AddressDataDto::fromArray($data);

        $this->assertSame(50, $dto->houseFlatCount);
    }

    public function test_extract_int_handles_numeric_string(): void
    {
        $data = [
            'house_flat_count' => '42',
        ];

        $dto = AddressDataDto::fromArray($data);

        $this->assertSame(42, $dto->houseFlatCount);
    }

    public function test_extract_int_returns_null_for_missing_key(): void
    {
        $data = [];

        $dto = AddressDataDto::fromArray($data);

        $this->assertNull($dto->houseFlatCount);
    }

    public function test_extract_int_returns_null_for_non_numeric_value(): void
    {
        $data = [
            'house_flat_count' => 'not a number',
        ];

        $dto = AddressDataDto::fromArray($data);

        $this->assertNull($dto->houseFlatCount);
    }

    public function test_extract_float_handles_valid_float(): void
    {
        $data = [
            'geo_lat' => 51.677065,
            'geo_lon' => 39.207924,
        ];

        $dto = AddressDataDto::fromArray($data);

        $this->assertSame(51.677065, $dto->geoLat);
        $this->assertSame(39.207924, $dto->geoLon);
    }

    public function test_extract_float_handles_numeric_string(): void
    {
        $data = [
            'geo_lat' => '51.677065',
            'geo_lon' => '39.207924',
        ];

        $dto = AddressDataDto::fromArray($data);

        $this->assertSame(51.677065, $dto->geoLat);
        $this->assertSame(39.207924, $dto->geoLon);
    }

    public function test_extract_float_returns_null_for_missing_key(): void
    {
        $data = [];

        $dto = AddressDataDto::fromArray($data);

        $this->assertNull($dto->geoLat);
        $this->assertNull($dto->geoLon);
    }

    public function test_extract_float_returns_null_for_non_numeric_value(): void
    {
        $data = [
            'geo_lat' => 'not a number',
        ];

        $dto = AddressDataDto::fromArray($data);

        // PHP преобразует нечисловую строку в 0.0 через floatval
        $this->assertSame(0.0, $dto->geoLat);
    }

    public function test_extract_array_handles_valid_array(): void
    {
        $data = [
            'history_values' => ['ул Мира', 'ул Ленина'],
            'metro'          => [['name' => 'Площадь Ленина']],
        ];

        $dto = AddressDataDto::fromArray($data);

        $this->assertSame(['ул Мира', 'ул Ленина'], $dto->historyValues);
        $this->assertSame([['name' => 'Площадь Ленина']], $dto->metro);
    }

    public function test_extract_array_returns_null_for_missing_key(): void
    {
        $data = [];

        $dto = AddressDataDto::fromArray($data);

        $this->assertNull($dto->historyValues);
        $this->assertNull($dto->metro);
    }

    public function test_extract_array_returns_null_for_non_array_value(): void
    {
        $data = [
            'history_values' => 'not an array',
            'metro'          => 123,
        ];

        $dto = AddressDataDto::fromArray($data);

        $this->assertNull($dto->historyValues);
        $this->assertNull($dto->metro);
    }

    public function test_convert_to_array_handles_nested_objects(): void
    {
        $data = [
            'postal_code'    => '394036',
            'country'        => 'Россия',
            'history_values' => ['ул Мира'],
        ];

        $dto   = AddressDataDto::fromArray($data);
        $array = $dto->toArray();

        $this->assertIsArray($array);
        $this->assertSame('394036', $array['postal_code']);
        $this->assertSame('Россия', $array['country']);
        $this->assertSame(['ул Мира'], $array['history_values']);
    }

    public function test_convert_to_array_handles_null_values(): void
    {
        $data = [];

        $dto   = AddressDataDto::fromArray($data);
        $array = $dto->toArray();

        $this->assertIsArray($array);
        $this->assertNull($array['postal_code']);
        $this->assertNull($array['country']);
        $this->assertNull($array['history_values']);
    }
}

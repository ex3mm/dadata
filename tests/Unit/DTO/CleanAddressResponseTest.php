<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\Tests\Unit\DTO;

use Ex3mm\Dadata\DTO\Response\CleanAddress\CleanAddressResponseDto;
use Ex3mm\Dadata\Tests\TestCase;

final class CleanAddressResponseTest extends TestCase
{
    /**
     * Проверяем, что все поля из clean_address_response.json присутствуют в DTO.
     */
    public function test_from_array_maps_all_fixture_fields(): void
    {
        $fixtureJson = file_get_contents(__DIR__ . '/../../fixtures/clean_address_response.json');
        $this->assertNotFalse($fixtureJson, 'Фикстура clean_address_response.json не найдена');

        $fixture = json_decode($fixtureJson, true);
        $this->assertIsArray($fixture);

        $dto = CleanAddressResponseDto::fromArray($fixture);
        $arr = $dto->toArray();

        // source/result на верхнем уровне
        $this->assertSame($fixture['source'], $dto->source);
        $this->assertSame($fixture['result'], $dto->result);

        // Все ключи из fixture должны присутствовать после преобразования DTO
        foreach (array_keys($fixture) as $key) {
            $this->assertArrayHasKey($key, $arr, "Ключ {$key} отсутствует в DTO");
        }

        // Точечная проверка реальных значений
        $this->assertSame('393250', $arr['postal_code']);
        $this->assertSame('Тамбовская обл', $arr['region_with_type']);
        $this->assertSame('ул Цыплухина', $arr['street_with_type']);
        $this->assertSame('д 3', 'д ' . $arr['house']);
        $this->assertSame('UTC+3', $arr['timezone']);

        // Вложенные поля
        $this->assertIsArray($arr['divisions']);
        $this->assertIsArray($arr['divisions']['administrative']);
        $this->assertSame('Рассказово', $arr['divisions']['administrative']['city']['name']);
    }
}

<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\Tests\Unit\DTO;

use Ex3mm\Dadata\DTO\Response\CollectionResponse;
use Ex3mm\Dadata\DTO\Response\Shared\AddressDataDto;
use Ex3mm\Dadata\DTO\Response\Shared\AddressValueDto;
use Ex3mm\Dadata\Tests\TestCase;

final class SuggestAddressResponseTest extends TestCase
{
    public function test_parses_suggest_address_response_from_fixture(): void
    {
        $fixtureJson = file_get_contents(__DIR__ . '/../../fixtures/suggest_address_response.json');
        $this->assertNotFalse($fixtureJson, 'Фикстура suggest_address_response.json не найдена');

        $fixtureData = json_decode($fixtureJson, true);
        $this->assertIsArray($fixtureData, 'Фикстура должна быть валидным JSON');
        $this->assertArrayHasKey('suggestions', $fixtureData);

        // Парсим ответ через эндпоинт
        $response = $this->parseResponse($fixtureData, $fixtureJson);

        // Проверяем структуру ответа
        $this->assertInstanceOf(CollectionResponse::class, $response);
        $this->assertCount(3, $response->items);
        $this->assertSame(3, $response->total);
        $this->assertSame($fixtureJson, $response->raw);
    }

    public function test_first_suggestion_has_correct_data(): void
    {
        $fixtureJson = file_get_contents(__DIR__ . '/../../fixtures/suggest_address_response.json');
        $fixtureData = json_decode($fixtureJson, true);
        $response    = $this->parseResponse($fixtureData, $fixtureJson);

        $firstSuggestion = $response->items[0];

        $this->assertInstanceOf(AddressValueDto::class, $firstSuggestion);
        $this->assertSame('г Воронеж, ул Мира, д 125', $firstSuggestion->value);
        $this->assertSame('394036, Воронежская обл, г Воронеж, ул Мира, д 125', $firstSuggestion->unrestrictedValue);
        $this->assertInstanceOf(AddressDataDto::class, $firstSuggestion->data);
    }

    public function test_address_data_has_all_required_fields(): void
    {
        $fixtureJson = file_get_contents(__DIR__ . '/../../fixtures/suggest_address_response.json');
        $fixtureData = json_decode($fixtureJson, true);
        $response    = $this->parseResponse($fixtureData, $fixtureJson);

        $addressData = $response->items[0]->data;

        // Базовые поля
        $this->assertSame('394036', $addressData->postalCode);
        $this->assertSame('Россия', $addressData->country);
        $this->assertSame('RU', $addressData->countryIsoCode);
        $this->assertSame('Центральный', $addressData->federalDistrict);

        // Регион
        $this->assertSame('b756fe6b-bbd3-44d5-9302-5bfcc740f46e', $addressData->regionFiasId);
        $this->assertSame('3600000000000', $addressData->regionKladrId);
        $this->assertSame('RU-VOR', $addressData->regionIsoCode);
        $this->assertSame('Воронежская обл', $addressData->regionWithType);
        $this->assertSame('обл', $addressData->regionType);
        $this->assertSame('область', $addressData->regionTypeFull);
        $this->assertSame('Воронежская', $addressData->region);

        // Город
        $this->assertSame('5bf5ddff-6353-4a3d-80c4-6fb27f00c6c1', $addressData->cityFiasId);
        $this->assertSame('3600000100000', $addressData->cityKladrId);
        $this->assertSame('г Воронеж', $addressData->cityWithType);
        $this->assertSame('г', $addressData->cityType);
        $this->assertSame('город', $addressData->cityTypeFull);
        $this->assertSame('Воронеж', $addressData->city);

        // Улица
        $this->assertSame('87b4e2f0-3f50-47ee-96ee-5691d1d35322', $addressData->streetFiasId);
        $this->assertSame('36000001000054600', $addressData->streetKladrId);
        $this->assertSame('ул Мира', $addressData->streetWithType);
        $this->assertSame('ул', $addressData->streetType);
        $this->assertSame('улица', $addressData->streetTypeFull);
        $this->assertSame('Мира', $addressData->street);

        // Дом
        $this->assertSame('д', $addressData->houseType);
        $this->assertSame('дом', $addressData->houseTypeFull);
        $this->assertSame('125', $addressData->house);
        $this->assertSame(0, $addressData->houseFlatCount);

        // Координаты
        $this->assertSame(51.677065, $addressData->geoLat);
        $this->assertSame(39.207924, $addressData->geoLon);
        $this->assertSame('2', $addressData->qcGeo);

        // Коды
        $this->assertSame('87b4e2f0-3f50-47ee-96ee-5691d1d35322', $addressData->fiasId);
        $this->assertSame('7', $addressData->fiasLevel);
        $this->assertSame('0', $addressData->fiasActualityState);
        $this->assertSame('36000001000054600', $addressData->kladrId);
        $this->assertSame('472045', $addressData->geonameId);
        $this->assertSame('2', $addressData->capitalMarker);
        $this->assertSame('20401000000', $addressData->okato);
        $this->assertSame('20701000001', $addressData->oktmo);
        $this->assertSame('3666', $addressData->taxOffice);
        $this->assertSame('3666', $addressData->taxOfficeLegal);
        $this->assertSame('UTC+3', $addressData->timezone);
    }

    public function test_second_suggestion_has_stead_data(): void
    {
        $fixtureJson = file_get_contents(__DIR__ . '/../../fixtures/suggest_address_response.json');
        $fixtureData = json_decode($fixtureJson, true);
        $response    = $this->parseResponse($fixtureData, $fixtureJson);

        // Третья подсказка содержит земельный участок
        $steadSuggestion = $response->items[2];
        $addressData     = $steadSuggestion->data;

        $this->assertSame('г Воронеж, ул Солдатское поле, уч 125', $steadSuggestion->value);
        $this->assertSame('5e08e9dc-a017-4aaa-aeec-2efcb19a1eb8', $addressData->steadFiasId);
        $this->assertSame('36:34:0349005:26', $addressData->steadCadnum);
        $this->assertSame('уч', $addressData->steadType);
        $this->assertSame('участок', $addressData->steadTypeFull);
        $this->assertSame('125', $addressData->stead);
        $this->assertSame('75', $addressData->fiasLevel); // Уровень земельного участка
    }

    public function test_history_values_are_parsed_correctly(): void
    {
        $fixtureJson = file_get_contents(__DIR__ . '/../../fixtures/suggest_address_response.json');
        $fixtureData = json_decode($fixtureJson, true);
        $response    = $this->parseResponse($fixtureData, $fixtureJson);

        // Вторая и третья подсказки имеют history_values
        $secondSuggestion = $response->items[1];
        $this->assertIsArray($secondSuggestion->data->historyValues);
        $this->assertContains('ул Мира', $secondSuggestion->data->historyValues);

        $thirdSuggestion = $response->items[2];
        $this->assertIsArray($thirdSuggestion->data->historyValues);
        $this->assertContains('ул Мира', $thirdSuggestion->data->historyValues);
    }

    public function test_to_array_converts_response_to_array(): void
    {
        $fixtureJson = file_get_contents(__DIR__ . '/../../fixtures/suggest_address_response.json');
        $fixtureData = json_decode($fixtureJson, true);
        $response    = $this->parseResponse($fixtureData, $fixtureJson);

        $array = $response->toArray();

        $this->assertIsArray($array);
        $this->assertArrayHasKey('items', $array);
        $this->assertArrayHasKey('total', $array);
        $this->assertCount(3, $array['items']);
        $this->assertSame(3, $array['total']);

        // Проверяем, что вложенные объекты тоже преобразованы в массивы
        $firstItem = $array['items'][0];
        $this->assertIsArray($firstItem);
        $this->assertArrayHasKey('value', $firstItem);
        $this->assertArrayHasKey('unrestricted_value', $firstItem);
        $this->assertArrayHasKey('data', $firstItem);
        $this->assertIsArray($firstItem['data']);
        $this->assertArrayHasKey('postal_code', $firstItem['data']);
    }

    /**
     * Вспомогательный метод для парсинга ответа через эндпоинт.
     *
     * @param array<string, mixed> $fixtureData
     *
     * @return CollectionResponse<AddressValueDto>
     */
    private function parseResponse(array $fixtureData, string $rawJson): CollectionResponse
    {
        $suggestions = $fixtureData['suggestions'];
        $items       = [];

        foreach ($suggestions as $suggestion) {
            $items[] = AddressValueDto::fromSuggestArray($suggestion);
        }

        return new CollectionResponse(
            items: $items,
            raw: $rawJson,
            total: count($items)
        );
    }

    public function test_constructor_creates_suggestion_with_all_fields(): void
    {
        $fixtureJson = file_get_contents(__DIR__ . '/../../fixtures/suggest_address_response.json');
        $fixtureData = json_decode($fixtureJson, true);

        $suggestionData = $fixtureData['suggestions'][0];
        $addressData    = AddressDataDto::fromArray($suggestionData['data']);

        $suggestion = new AddressValueDto(
            value: $suggestionData['value'],
            unrestrictedValue: $suggestionData['unrestricted_value'],
            data: $addressData
        );

        $this->assertSame($suggestionData['value'], $suggestion->value);
        $this->assertSame($suggestionData['unrestricted_value'], $suggestion->unrestrictedValue);
        $this->assertSame($addressData, $suggestion->data);
    }

    public function test_from_array_throws_exception_for_invalid_value_type(): void
    {
        $this->expectException(\Ex3mm\Dadata\Exceptions\ValidationException::class);
        $this->expectExceptionMessage('Invalid value field: expected string, got integer');

        AddressValueDto::fromSuggestArray([
            'value'              => 123, // Неверный тип
            'unrestricted_value' => 'test',
            'data'               => [],
        ]);
    }

    public function test_from_array_throws_exception_for_invalid_unrestricted_value_type(): void
    {
        $this->expectException(\Ex3mm\Dadata\Exceptions\ValidationException::class);
        $this->expectExceptionMessage('Invalid unrestricted_value field: expected string, got array');

        AddressValueDto::fromSuggestArray([
            'value'              => 'test',
            'unrestricted_value' => [], // Неверный тип
            'data'               => [],
        ]);
    }

    public function test_from_array_throws_exception_for_invalid_data_type(): void
    {
        $this->expectException(\Ex3mm\Dadata\Exceptions\ValidationException::class);
        $this->expectExceptionMessage('Invalid data field: expected array, got string');

        AddressValueDto::fromSuggestArray([
            'value'              => 'test',
            'unrestricted_value' => 'test',
            'data'               => 'invalid', // Неверный тип
        ]);
    }
}

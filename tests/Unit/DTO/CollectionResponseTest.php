<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\Tests\Unit\DTO;

use Ex3mm\Dadata\DTO\Response\CollectionResponse;
use Ex3mm\Dadata\DTO\Response\Shared\AddressValueDto;
use Ex3mm\Dadata\Tests\TestCase;

final class CollectionResponseTest extends TestCase
{
    public function test_constructor_creates_collection_with_items(): void
    {
        $items = ['item1', 'item2', 'item3'];
        $raw   = '{"test": "data"}';
        $total = 3;

        $collection = new CollectionResponse($items, $raw, $total);

        $this->assertSame($items, $collection->items);
        $this->assertSame($raw, $collection->raw);
        $this->assertSame($total, $collection->total);
    }

    public function test_constructor_creates_empty_collection(): void
    {
        $collection = new CollectionResponse([], '{}', 0);

        $this->assertSame([], $collection->items);
        $this->assertSame('{}', $collection->raw);
        $this->assertSame(0, $collection->total);
    }

    public function test_from_array_creates_empty_collection(): void
    {
        $data        = ['suggestions' => []];
        $rawResponse = '{"suggestions": []}';

        $collection = CollectionResponse::fromArray($data, $rawResponse);

        $this->assertInstanceOf(CollectionResponse::class, $collection);
        $this->assertSame([], $collection->items);
        $this->assertSame($rawResponse, $collection->raw);
        $this->assertSame(0, $collection->total);
    }

    public function test_to_array_converts_items_with_to_array_method(): void
    {
        $fixture = json_decode(
            file_get_contents(__DIR__ . '/../../fixtures/suggest_address_response.json'),
            true
        );

        $items = [];
        foreach ($fixture['suggestions'] as $suggestion) {
            $items[] = AddressValueDto::fromArray($suggestion);
        }

        $collection = new CollectionResponse($items, json_encode($fixture), count($items));

        $result = $collection->toArray();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('items', $result);
        $this->assertArrayHasKey('total', $result);
        $this->assertCount(count($items), $result['items']);
        $this->assertSame(count($items), $result['total']);

        // Проверяем что items были преобразованы через toArray()
        foreach ($result['items'] as $item) {
            $this->assertIsArray($item);
            $this->assertArrayHasKey('value', $item);
            $this->assertArrayHasKey('data', $item);
        }
    }

    public function test_to_array_handles_scalar_items(): void
    {
        $items      = ['string1', 'string2', 'string3'];
        $collection = new CollectionResponse($items, '{}', 3);

        $result = $collection->toArray();

        $this->assertSame([
            'items' => $items,
            'total' => 3,
        ], $result);
    }

    public function test_to_array_handles_mixed_items(): void
    {
        $fixture = json_decode(
            file_get_contents(__DIR__ . '/../../fixtures/suggest_address_response.json'),
            true
        );

        $dto = AddressValueDto::fromArray($fixture['suggestions'][0]);

        $items      = [$dto, 'scalar_value', ['array_value']];
        $collection = new CollectionResponse($items, '{}', 3);

        $result = $collection->toArray();

        $this->assertCount(3, $result['items']);
        $this->assertIsArray($result['items'][0]); // DTO преобразован в массив
        $this->assertSame('scalar_value', $result['items'][1]);
        $this->assertSame(['array_value'], $result['items'][2]);
    }
}

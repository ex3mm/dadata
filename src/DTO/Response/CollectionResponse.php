<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\DTO\Response;

use Ex3mm\Dadata\Contracts\DtoInterface;

/**
 * Универсальный DTO для коллекции ответов от DaData API.
 *
 * @template T
 */
final readonly class CollectionResponse implements DtoInterface
{
    /**
     * @param list<T> $items Массив элементов ответа
     * @param string $raw Оригинальный JSON-ответ от API
     * @param int $total Количество элементов в ответе
     */
    public function __construct(
        public array $items,
        public string $raw,
        public int $total,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data, string $rawResponse): static
    {
        return new self(
            items: [],
            raw: $rawResponse,
            total: 0
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $items = [];
        foreach ($this->items as $item) {
            if (is_object($item) && method_exists($item, 'toArray')) {
                /** @phpstan-ignore-next-line */
                $items[] = $item->toArray();
            } else {
                $items[] = $item;
            }
        }

        return [
            'items' => $items,
            'total' => $this->total,
        ];
    }
}

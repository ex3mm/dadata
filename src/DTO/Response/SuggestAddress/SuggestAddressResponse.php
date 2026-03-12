<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\DTO\Response\SuggestAddress;

use Ex3mm\Dadata\DTO\Response\AbstractResponse;

/**
 * DTO для ответа Suggest Address API.
 */
final readonly class SuggestAddressResponse extends AbstractResponse
{
    /**
     * @param list<AddressSuggestionDTO> $suggestions Список подсказок
     * @param string $rawResponse Оригинальный JSON-ответ
     */
    public function __construct(
        public array $suggestions,
        string $rawResponse,
    ) {
        parent::__construct($rawResponse);
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data, string $rawResponse): static
    {
        $suggestions     = [];
        $suggestionsData = $data['suggestions'] ?? [];

        if (is_array($suggestionsData)) {
            foreach ($suggestionsData as $item) {
                if (is_array($item)) {
                    /** @var array<string, mixed> $item */
                    $suggestions[] = AddressSuggestionDTO::fromArray($item);
                }
            }
        }

        return new self($suggestions, $rawResponse);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'suggestions' => array_map(
                fn (AddressSuggestionDTO $s): array => [
                    'value'              => $s->value,
                    'unrestricted_value' => $s->unrestrictedValue,
                    'data'               => (array) $s->data,
                ],
                $this->suggestions
            ),
        ];
    }
}

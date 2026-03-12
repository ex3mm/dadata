<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\DTO\Response\SuggestParty;

use Ex3mm\Dadata\DTO\Response\AbstractResponse;

/**
 * DTO для ответа Suggest Party API.
 */
final readonly class SuggestPartyResponse extends AbstractResponse
{
    /**
     * @param list<PartySuggestionDTO> $suggestions Список подсказок организаций
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
                    $suggestions[] = PartySuggestionDTO::fromArray($item);
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
                fn (PartySuggestionDTO $s): array => [
                    'value'              => $s->value,
                    'unrestricted_value' => $s->unrestrictedValue,
                ],
                $this->suggestions
            ),
        ];
    }
}

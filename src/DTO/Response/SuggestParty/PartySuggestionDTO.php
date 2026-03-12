<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\DTO\Response\SuggestParty;

use Ex3mm\Dadata\Exceptions\ValidationException;

/**
 * Одна подсказка организации.
 */
final readonly class PartySuggestionDTO
{
    /**
     * @param string $value Краткое представление организации
     * @param string $unrestrictedValue Полное представление организации
     * @param PartyDataDTO $data Детальные данные организации
     */
    public function __construct(
        public string $value,
        public string $unrestrictedValue,
        public PartyDataDTO $data,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): static
    {
        $value             = $data['value']              ?? '';
        $unrestrictedValue = $data['unrestricted_value'] ?? '';
        $dataArray         = $data['data']               ?? [];

        if (!is_string($value)) {
            throw new ValidationException(
                message: 'Invalid value field: expected string, got ' . gettype($value),
                statusCode: 0,
                responseBody: '',
                errors: ['invalid_field_type'],
            );
        }

        if (!is_string($unrestrictedValue)) {
            throw new ValidationException(
                message: 'Invalid unrestricted_value field: expected string, got ' . gettype($unrestrictedValue),
                statusCode: 0,
                responseBody: '',
                errors: ['invalid_field_type'],
            );
        }

        if (!is_array($dataArray)) {
            throw new ValidationException(
                message: 'Invalid data field: expected array, got ' . gettype($dataArray),
                statusCode: 0,
                responseBody: '',
                errors: ['invalid_field_type'],
            );
        }

        /** @var array<string, mixed> $dataArray */
        return new self(
            value: $value,
            unrestrictedValue: $unrestrictedValue,
            data: PartyDataDTO::fromArray($dataArray),
        );
    }
}

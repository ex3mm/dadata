<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\DTO\Response\Shared;

use Ex3mm\Dadata\Exceptions\ValidationException;

/**
 * Универсальный DTO для адресного блока вида:
 * value + unrestricted_value + data.
 */
final readonly class AddressValueDto
{
    public function __construct(
        public ?string $value,
        public ?string $unrestrictedValue,
        public ?AddressDataDto $data,
    ) {
    }

    /**
     * Мягкий парсинг: неверные типы приводятся к null.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): static
    {
        $addressData = null;
        if (isset($data['data']) && is_array($data['data'])) {
            /** @var array<string, mixed> $nested */
            $nested      = $data['data'];
            $addressData = AddressDataDto::fromArray($nested);
        }

        return new self(
            value: isset($data['value'])                          && is_string($data['value']) ? $data['value'] : null,
            unrestrictedValue: isset($data['unrestricted_value']) && is_string($data['unrestricted_value']) ? $data['unrestricted_value'] : null,
            data: $addressData
        );
    }

    /**
     * Строгий парсинг для suggestAddress: поле value/unrestricted_value/data обязательно.
     *
     * @param array<string, mixed> $data
     */
    public static function fromSuggestArray(array $data): static
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
            data: AddressDataDto::fromArray($dataArray),
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'value'              => $this->value,
            'unrestricted_value' => $this->unrestrictedValue,
            'data'               => $this->data?->toArray(),
        ];
    }
}

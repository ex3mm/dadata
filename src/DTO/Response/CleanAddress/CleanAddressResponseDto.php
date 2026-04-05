<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\DTO\Response\CleanAddress;

use Ex3mm\Dadata\DTO\Response\Shared\AddressDataDto;
use Ex3mm\Dadata\Exceptions\ValidationException;

/**
 * DTO стандартизованного адреса.
 */
final readonly class CleanAddressResponseDto
{
    public function __construct(
        public string $source,
        public string $result,
        public AddressDataDto $data,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): static
    {
        $source = $data['source'] ?? '';
        $result = $data['result'] ?? '';

        if (!is_string($source)) {
            throw new ValidationException(
                message: 'Invalid source field: expected string, got ' . gettype($source),
                statusCode: 0,
                responseBody: '',
                errors: ['invalid_field_type'],
            );
        }

        if (!is_string($result)) {
            throw new ValidationException(
                message: 'Invalid result field: expected string, got ' . gettype($result),
                statusCode: 0,
                responseBody: '',
                errors: ['invalid_field_type'],
            );
        }

        return new self(
            source: $source,
            result: $result,
            data: AddressDataDto::fromArray($data)
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'source' => $this->source,
            'result' => $this->result,
            ...$this->data->toArray(),
        ];
    }
}

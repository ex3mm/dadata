<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\DTO\Response\Shared\Address;

/**
 * DTO элемента административного деления.
 */
final readonly class DivisionDto
{
    public function __construct(
        public ?string $fiasId,
        public ?string $kladrId,
        public ?string $type,
        public ?string $typeFull,
        public ?string $name,
        public ?string $nameWithType,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            fiasId: self::extractString($data, 'fias_id'),
            kladrId: self::extractString($data, 'kladr_id'),
            type: self::extractString($data, 'type'),
            typeFull: self::extractString($data, 'type_full'),
            name: self::extractString($data, 'name'),
            nameWithType: self::extractString($data, 'name_with_type'),
        );
    }

    /**
     * @param array<string, mixed> $data
     */
    private static function extractString(array $data, string $key): ?string
    {
        return isset($data[$key]) && is_string($data[$key]) ? $data[$key] : null;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'fias_id'        => $this->fiasId,
            'kladr_id'       => $this->kladrId,
            'type'           => $this->type,
            'type_full'      => $this->typeFull,
            'name'           => $this->name,
            'name_with_type' => $this->nameWithType,
        ];
    }
}

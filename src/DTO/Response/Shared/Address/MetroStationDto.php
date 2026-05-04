<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\DTO\Response\Shared\Address;

/**
 * DTO станции метро.
 */
final readonly class MetroStationDto
{
    public function __construct(
        public ?string $name,
        public ?string $line,
        public ?float $distance,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            name: isset($data['name']) && is_string($data['name']) ? $data['name'] : null,
            line: isset($data['line']) && is_string($data['line']) ? $data['line'] : null,
            distance: self::extractFloat($data, 'distance'),
        );
    }

    /**
     * @param array<string, mixed> $data
     */
    private static function extractFloat(array $data, string $key): ?float
    {
        if (!isset($data[$key])) {
            return null;
        }

        return is_float($data[$key]) || is_int($data[$key]) || is_string($data[$key])
            ? (float) $data[$key]
            : null;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'name'     => $this->name,
            'line'     => $this->line,
            'distance' => $this->distance,
        ];
    }
}

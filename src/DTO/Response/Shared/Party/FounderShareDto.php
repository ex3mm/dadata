<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\DTO\Response\Shared\Party;

/**
 * DTO доли учредителя компании.
 */
final readonly class FounderShareDto
{
    public function __construct(
        public ?string $type,
        public ?float $value,
        public ?int $numerator,
        public ?int $denominator,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            type: isset($data['type']) && is_string($data['type']) ? $data['type'] : null,
            value: self::extractFloat($data, 'value'),
            numerator: self::extractInt($data, 'numerator'),
            denominator: self::extractInt($data, 'denominator'),
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
     * @param array<string, mixed> $data
     */
    private static function extractInt(array $data, string $key): ?int
    {
        if (!isset($data[$key])) {
            return null;
        }

        return is_int($data[$key]) ? $data[$key] : (is_numeric($data[$key]) ? (int) $data[$key] : null);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'type'        => $this->type,
            'value'       => $this->value,
            'numerator'   => $this->numerator,
            'denominator' => $this->denominator,
        ];
    }
}

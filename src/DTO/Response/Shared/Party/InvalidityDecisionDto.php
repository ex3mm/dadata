<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\DTO\Response\Shared\Party;

/**
 * DTO решения суда о недостоверности сведений.
 */
final readonly class InvalidityDecisionDto
{
    public function __construct(
        public ?string $courtName,
        public ?string $number,
        public ?int $date,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            courtName: isset($data['court_name']) && is_string($data['court_name']) ? $data['court_name'] : null,
            number: isset($data['number'])        && is_string($data['number']) ? $data['number'] : null,
            date: self::extractInt($data, 'date'),
        );
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
            'court_name' => $this->courtName,
            'number'     => $this->number,
            'date'       => $this->date,
        ];
    }
}

<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\DTO\Response\Shared\Party;

/**
 * DTO финансовых показателей компании за год.
 */
final readonly class FinanceDto
{
    public function __construct(
        public ?string $taxSystem,
        public ?float $income,
        public ?float $expense,
        public ?float $revenue,
        public ?float $debt,
        public ?float $penalty,
        public ?int $year,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            taxSystem: isset($data['tax_system']) && is_string($data['tax_system']) ? $data['tax_system'] : null,
            income: self::extractFloat($data, 'income'),
            expense: self::extractFloat($data, 'expense'),
            revenue: self::extractFloat($data, 'revenue'),
            debt: self::extractFloat($data, 'debt'),
            penalty: self::extractFloat($data, 'penalty'),
            year: self::extractInt($data, 'year'),
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
            'tax_system' => $this->taxSystem,
            'income'     => $this->income,
            'expense'    => $this->expense,
            'revenue'    => $this->revenue,
            'debt'       => $this->debt,
            'penalty'    => $this->penalty,
            'year'       => $this->year,
        ];
    }
}

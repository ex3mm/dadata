<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\DTO\Response\Shared\Party;

/**
 * DTO документа компании.
 */
final readonly class DocumentDto
{
    public function __construct(
        public ?string $type,
        public ?string $series,
        public ?string $number,
        public ?int $issueDate,
        public ?string $issueAuthority,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            type: isset($data['type'])     && is_string($data['type']) ? $data['type'] : null,
            series: isset($data['series']) && is_string($data['series']) ? $data['series'] : null,
            number: isset($data['number']) && is_string($data['number']) ? $data['number'] : null,
            issueDate: self::extractInt($data, 'issue_date'),
            issueAuthority: isset($data['issue_authority']) && is_string($data['issue_authority']) ? $data['issue_authority'] : null,
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
            'type'            => $this->type,
            'series'          => $this->series,
            'number'          => $this->number,
            'issue_date'      => $this->issueDate,
            'issue_authority' => $this->issueAuthority,
        ];
    }
}

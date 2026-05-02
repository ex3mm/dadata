<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\DTO\Response\Shared\Party;

/** DTO ФИО индивидуального предпринимателя */
final readonly class FioDto
{
    public function __construct(
        public ?string $surname,
        public ?string $name,
        public ?string $patronymic,
        public ?string $gender,
        public ?string $source,
        public ?string $qc,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): static
    {
        return new self(
            surname: self::extractString($data, 'surname'),
            name: self::extractString($data, 'name'),
            patronymic: self::extractString($data, 'patronymic'),
            gender: self::extractString($data, 'gender'),
            source: self::extractString($data, 'source'),
            qc: self::extractString($data, 'qc'),
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
            'surname'    => $this->surname,
            'name'       => $this->name,
            'patronymic' => $this->patronymic,
            'gender'     => $this->gender,
            'source'     => $this->source,
            'qc'         => $this->qc,
        ];
    }
}

<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\DTO\Response\SuggestFio;

use Ex3mm\Dadata\Contracts\DtoInterface;
use Ex3mm\Dadata\Enums\Gender;

/** DTO данных ФИО из подсказок */
final readonly class FioDataDto implements DtoInterface
{
    public function __construct(
        public ?string $surname,
        public ?string $name,
        public ?string $patronymic,
        public ?Gender $gender,
        public ?string $source,
        public ?string $qc,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data, string $rawResponse = ''): static
    {
        $gender = null;
        if (isset($data['gender']) && is_string($data['gender'])) {
            $gender = Gender::tryFrom($data['gender']);
        }

        return new self(
            surname: self::extractString($data, 'surname'),
            name: self::extractString($data, 'name'),
            patronymic: self::extractString($data, 'patronymic'),
            gender: $gender,
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
            'gender'     => $this->gender?->value,
            'source'     => $this->source,
            'qc'         => $this->qc,
        ];
    }
}

<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\DTO\Response\Shared\Party;

/**
 * DTO правопредшественника компании.
 */
final readonly class PredecessorDto
{
    public function __construct(
        public ?string $ogrn,
        public ?string $inn,
        public ?string $name,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            ogrn: isset($data['ogrn']) && is_string($data['ogrn']) ? $data['ogrn'] : null,
            inn: isset($data['inn'])   && is_string($data['inn']) ? $data['inn'] : null,
            name: isset($data['name']) && is_string($data['name']) ? $data['name'] : null,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'ogrn' => $this->ogrn,
            'inn'  => $this->inn,
            'name' => $this->name,
        ];
    }
}

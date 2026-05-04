<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\DTO\Response\Shared\Party;

/** DTO детальных данных телефона компании */
final readonly class PartyPhoneDataDto
{
    public function __construct(
        public ?string $type,
        public ?string $number,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            type: isset($data['type'])     && is_string($data['type']) ? $data['type'] : null,
            number: isset($data['number']) && is_string($data['number']) ? $data['number'] : null,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'type'   => $this->type,
            'number' => $this->number,
        ];
    }
}

<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\DTO\Response\Shared\Party;

/** DTO уставного капитала компании */
final readonly class CapitalDto
{
    public function __construct(
        public ?string $type,
        public ?int $value,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            type: isset($data['type'])   && is_string($data['type']) ? $data['type'] : null,
            value: isset($data['value']) && is_int($data['value']) ? $data['value'] : null,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'type'  => $this->type,
            'value' => $this->value,
        ];
    }
}

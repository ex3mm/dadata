<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\DTO\Response\Shared\Party;

final readonly class PartyOpfDto
{
    public function __construct(
        public ?string $type,
        public ?string $code,
        public ?string $full,
        public ?string $short,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): static
    {
        return new self(
            type: isset($data['type'])   && is_string($data['type']) ? $data['type'] : null,
            code: isset($data['code'])   && is_string($data['code']) ? $data['code'] : null,
            full: isset($data['full'])   && is_string($data['full']) ? $data['full'] : null,
            short: isset($data['short']) && is_string($data['short']) ? $data['short'] : null,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'type'  => $this->type,
            'code'  => $this->code,
            'full'  => $this->full,
            'short' => $this->short,
        ];
    }
}

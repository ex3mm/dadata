<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\DTO\Response\Shared\Bank;

use Ex3mm\Dadata\Enums\BankOpfType;

final readonly class BankOpfDto
{
    public function __construct(
        public ?BankOpfType $type,
        public ?string $full,
        public ?string $short,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): static
    {
        $type = null;
        if (isset($data['type']) && is_string($data['type'])) {
            $type = BankOpfType::tryFrom($data['type']);
        }

        return new self(
            type: $type,
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
            'type'  => $this->type?->value,
            'full'  => $this->full,
            'short' => $this->short,
        ];
    }
}

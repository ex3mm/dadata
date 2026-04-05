<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\DTO\Response\Shared\Bank;

final readonly class BankNameDto
{
    public function __construct(
        public ?string $payment,
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
            payment: isset($data['payment']) && is_string($data['payment']) ? $data['payment'] : null,
            full: isset($data['full'])       && is_string($data['full']) ? $data['full'] : null,
            short: isset($data['short'])     && is_string($data['short']) ? $data['short'] : null,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'payment' => $this->payment,
            'full'    => $this->full,
            'short'   => $this->short,
        ];
    }
}

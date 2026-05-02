<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\DTO\Response\Shared\Party;

/** DTO названия страны гражданства */
final readonly class CitizenshipNameDto
{
    public function __construct(
        public ?string $full,
        public ?string $short,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): static
    {
        $full = null;
        if (isset($data['full']) && is_string($data['full'])) {
            $full = $data['full'];
        }

        $short = null;
        if (isset($data['short']) && is_string($data['short'])) {
            $short = $data['short'];
        }

        return new self(
            full: $full,
            short: $short,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'full'  => $this->full,
            'short' => $this->short,
        ];
    }
}

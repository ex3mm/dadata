<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\DTO\Response\Shared\Party;

final readonly class PartyNameDto
{
    public function __construct(
        public ?string $fullWithOpf,
        public ?string $shortWithOpf,
        public ?string $latin,
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
            fullWithOpf: isset($data['full_with_opf'])   && is_string($data['full_with_opf']) ? $data['full_with_opf'] : null,
            shortWithOpf: isset($data['short_with_opf']) && is_string($data['short_with_opf']) ? $data['short_with_opf'] : null,
            latin: isset($data['latin'])                 && is_string($data['latin']) ? $data['latin'] : null,
            full: isset($data['full'])                   && is_string($data['full']) ? $data['full'] : null,
            short: isset($data['short'])                 && is_string($data['short']) ? $data['short'] : null,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'full_with_opf'  => $this->fullWithOpf,
            'short_with_opf' => $this->shortWithOpf,
            'latin'          => $this->latin,
            'full'           => $this->full,
            'short'          => $this->short,
        ];
    }
}

<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\DTO\Response\Shared\Party;

/** DTO кода страны гражданства */
final readonly class CitizenshipCodeDto
{
    public function __construct(
        public ?int $numeric,
        public ?string $alpha3,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): static
    {
        $numeric = null;
        if (isset($data['numeric']) && is_int($data['numeric'])) {
            $numeric = $data['numeric'];
        }

        $alpha3 = null;
        if (isset($data['alpha_3']) && is_string($data['alpha_3'])) {
            $alpha3 = $data['alpha_3'];
        }

        return new self(
            numeric: $numeric,
            alpha3: $alpha3,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'numeric' => $this->numeric,
            'alpha_3' => $this->alpha3,
        ];
    }
}

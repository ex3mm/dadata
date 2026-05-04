<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\DTO\Response\Shared\Party;

/**
 * DTO органа власти (ФНС, ПФР и т.д.).
 */
final readonly class AuthorityDto
{
    public function __construct(
        public ?string $type,
        public ?string $code,
        public ?string $name,
        public ?string $address,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            type: isset($data['type'])       && is_string($data['type']) ? $data['type'] : null,
            code: isset($data['code'])       && is_string($data['code']) ? $data['code'] : null,
            name: isset($data['name'])       && is_string($data['name']) ? $data['name'] : null,
            address: isset($data['address']) && is_string($data['address']) ? $data['address'] : null,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'type'    => $this->type,
            'code'    => $this->code,
            'name'    => $this->name,
            'address' => $this->address,
        ];
    }
}

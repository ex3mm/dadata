<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\DTO\Response\Shared\Party;

/**
 * DTO кода ОКВЭД компании.
 */
final readonly class OkvedDto
{
    public function __construct(
        public ?bool $main,
        public ?string $type,
        public ?string $code,
        public ?string $name,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            main: isset($data['main']) && is_bool($data['main']) ? $data['main'] : null,
            type: isset($data['type']) && is_string($data['type']) ? $data['type'] : null,
            code: isset($data['code']) && is_string($data['code']) ? $data['code'] : null,
            name: isset($data['name']) && is_string($data['name']) ? $data['name'] : null,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'main' => $this->main,
            'type' => $this->type,
            'code' => $this->code,
            'name' => $this->name,
        ];
    }
}

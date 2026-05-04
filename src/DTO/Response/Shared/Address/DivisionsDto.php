<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\DTO\Response\Shared\Address;

/**
 * DTO всех делений адреса (административные и т.д.).
 */
final readonly class DivisionsDto
{
    public function __construct(
        public ?AdministrativeDivisionDto $administrative,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $administrative = null;
        if (isset($data['administrative']) && is_array($data['administrative'])) {
            /** @var array<string, mixed> $adminData */
            $adminData      = $data['administrative'];
            $administrative = AdministrativeDivisionDto::fromArray($adminData);
        }

        return new self(
            administrative: $administrative,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'administrative' => $this->administrative?->toArray(),
        ];
    }
}

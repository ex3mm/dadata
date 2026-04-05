<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\DTO\Response\Shared\Bank;

use Ex3mm\Dadata\Enums\BankStateStatus;

final readonly class BankStateDto
{
    public function __construct(
        public ?BankStateStatus $status,
        public ?string $code,
        public ?int $actualityDate,
        public ?int $registrationDate,
        public ?int $liquidationDate,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): static
    {
        $status = null;
        if (isset($data['status']) && is_string($data['status'])) {
            $status = BankStateStatus::tryFrom($data['status']);
        }

        return new self(
            status: $status,
            code: isset($data['code']) && is_string($data['code']) ? $data['code'] : null,
            actualityDate: self::extractInt($data, 'actuality_date'),
            registrationDate: self::extractInt($data, 'registration_date'),
            liquidationDate: self::extractInt($data, 'liquidation_date'),
        );
    }

    /**
     * @param array<string, mixed> $data
     */
    private static function extractInt(array $data, string $key): ?int
    {
        if (!isset($data[$key])) {
            return null;
        }

        return is_int($data[$key]) ? $data[$key] : (is_numeric($data[$key]) ? (int) $data[$key] : null);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'status'            => $this->status?->value,
            'code'              => $this->code,
            'actuality_date'    => $this->actualityDate,
            'registration_date' => $this->registrationDate,
            'liquidation_date'  => $this->liquidationDate,
        ];
    }
}

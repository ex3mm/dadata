<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\DTO\Response\Shared\Bank;

use Ex3mm\Dadata\DTO\Response\Shared\AddressValueDto;

/**
 * Детальные данные банка из DaData API.
 */
final readonly class BankDataDto
{
    public function __construct(
        public ?BankOpfDto $opf,
        public ?BankNameDto $name,
        public ?string $bic,
        public ?string $swift,
        public ?string $inn,
        public ?string $kpp,
        public ?string $okpo,
        public ?string $correspondentAccount,
        public mixed $treasuryAccounts,
        public ?string $registrationNumber,
        public ?string $paymentCity,
        public ?BankStateDto $state,
        public mixed $rkc,
        public mixed $cbr,
        public ?AddressValueDto $address,
        public mixed $phones,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): static
    {
        return new self(
            opf: self::extractOpf($data),
            name: self::extractName($data),
            bic: self::extractString($data, 'bic'),
            swift: self::extractString($data, 'swift'),
            inn: self::extractString($data, 'inn'),
            kpp: self::extractString($data, 'kpp'),
            okpo: self::extractString($data, 'okpo'),
            correspondentAccount: self::extractString($data, 'correspondent_account'),
            treasuryAccounts: $data['treasury_accounts'] ?? null,
            registrationNumber: self::extractString($data, 'registration_number'),
            paymentCity: self::extractString($data, 'payment_city'),
            state: self::extractState($data),
            rkc: $data['rkc'] ?? null,
            cbr: $data['cbr'] ?? null,
            address: self::extractAddress($data),
            phones: $data['phones'] ?? null,
        );
    }

    /**
     * @param array<string, mixed> $data
     */
    private static function extractString(array $data, string $key): ?string
    {
        return isset($data[$key]) && is_string($data[$key]) ? $data[$key] : null;
    }

    /**
     * @param array<string, mixed> $data
     */
    private static function extractOpf(array $data): ?BankOpfDto
    {
        if (!isset($data['opf']) || !is_array($data['opf'])) {
            return null;
        }

        /** @var array<string, mixed> $opf */
        $opf = $data['opf'];

        return BankOpfDto::fromArray($opf);
    }

    /**
     * @param array<string, mixed> $data
     */
    private static function extractName(array $data): ?BankNameDto
    {
        if (!isset($data['name']) || !is_array($data['name'])) {
            return null;
        }

        /** @var array<string, mixed> $name */
        $name = $data['name'];

        return BankNameDto::fromArray($name);
    }

    /**
     * @param array<string, mixed> $data
     */
    private static function extractState(array $data): ?BankStateDto
    {
        if (!isset($data['state']) || !is_array($data['state'])) {
            return null;
        }

        /** @var array<string, mixed> $state */
        $state = $data['state'];

        return BankStateDto::fromArray($state);
    }

    /**
     * @param array<string, mixed> $data
     */
    private static function extractAddress(array $data): ?AddressValueDto
    {
        if (!isset($data['address']) || !is_array($data['address'])) {
            return null;
        }

        /** @var array<string, mixed> $address */
        $address = $data['address'];

        return AddressValueDto::fromArray($address);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'opf'                   => $this->opf?->toArray(),
            'name'                  => $this->name?->toArray(),
            'bic'                   => $this->bic,
            'swift'                 => $this->swift,
            'inn'                   => $this->inn,
            'kpp'                   => $this->kpp,
            'okpo'                  => $this->okpo,
            'correspondent_account' => $this->correspondentAccount,
            'treasury_accounts'     => $this->treasuryAccounts,
            'registration_number'   => $this->registrationNumber,
            'payment_city'          => $this->paymentCity,
            'state'                 => $this->state?->toArray(),
            'rkc'                   => $this->rkc,
            'cbr'                   => $this->cbr,
            'address'               => $this->address?->toArray(),
            'phones'                => $this->phones,
        ];
    }
}

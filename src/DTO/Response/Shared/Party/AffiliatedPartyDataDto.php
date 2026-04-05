<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\DTO\Response\Shared\Party;

use Ex3mm\Dadata\DTO\Response\Shared\AddressValueDto;
use Ex3mm\Dadata\Enums\PartyBranchType;
use Ex3mm\Dadata\Enums\PartyType;

final readonly class AffiliatedPartyDataDto
{
    public function __construct(
        public ?string $inn,
        public ?string $kpp,
        public ?string $ogrn,
        public ?string $hid,
        public ?PartyType $type,
        public ?string $okato,
        public ?string $oktmo,
        public ?string $okpo,
        public ?string $okogu,
        public ?string $okfs,
        public ?string $okved,
        public ?string $okvedType,
        public ?int $branchCount,
        public ?PartyBranchType $branchType,
        public ?AddressValueDto $address,
        public ?PartyStateDto $state,
        public ?bool $invalid,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): static
    {
        $type = null;
        if (isset($data['type']) && is_string($data['type'])) {
            $type = PartyType::tryFrom($data['type']);
        }

        $branchType = null;
        if (isset($data['branch_type']) && is_string($data['branch_type'])) {
            $branchType = PartyBranchType::tryFrom($data['branch_type']);
        }

        $address = null;
        if (isset($data['address']) && is_array($data['address'])) {
            /** @var array<string, mixed> $addressData */
            $addressData = $data['address'];
            $address     = AddressValueDto::fromArray($addressData);
        }

        $state = null;
        if (isset($data['state']) && is_array($data['state'])) {
            /** @var array<string, mixed> $stateData */
            $stateData = $data['state'];
            $state     = PartyStateDto::fromArray($stateData);
        }

        return new self(
            inn: self::extractString($data, 'inn'),
            kpp: self::extractString($data, 'kpp'),
            ogrn: self::extractString($data, 'ogrn'),
            hid: self::extractString($data, 'hid'),
            type: $type,
            okato: self::extractString($data, 'okato'),
            oktmo: self::extractString($data, 'oktmo'),
            okpo: self::extractString($data, 'okpo'),
            okogu: self::extractString($data, 'okogu'),
            okfs: self::extractString($data, 'okfs'),
            okved: self::extractString($data, 'okved'),
            okvedType: self::extractString($data, 'okved_type'),
            branchCount: self::extractInt($data, 'branch_count'),
            branchType: $branchType,
            address: $address,
            state: $state,
            invalid: self::extractBool($data, 'invalid'),
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
    private static function extractInt(array $data, string $key): ?int
    {
        if (!isset($data[$key])) {
            return null;
        }

        return is_int($data[$key]) ? $data[$key] : (is_numeric($data[$key]) ? (int) $data[$key] : null);
    }

    /**
     * @param array<string, mixed> $data
     */
    private static function extractBool(array $data, string $key): ?bool
    {
        return isset($data[$key]) && is_bool($data[$key]) ? $data[$key] : null;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'inn'          => $this->inn,
            'kpp'          => $this->kpp,
            'ogrn'         => $this->ogrn,
            'hid'          => $this->hid,
            'type'         => $this->type?->value,
            'okato'        => $this->okato,
            'oktmo'        => $this->oktmo,
            'okpo'         => $this->okpo,
            'okogu'        => $this->okogu,
            'okfs'         => $this->okfs,
            'okved'        => $this->okved,
            'okved_type'   => $this->okvedType,
            'branch_count' => $this->branchCount,
            'branch_type'  => $this->branchType?->value,
            'address'      => $this->address?->toArray(),
            'state'        => $this->state?->toArray(),
            'invalid'      => $this->invalid,
        ];
    }
}

<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\DTO\Response\Shared\Party;

use Ex3mm\Dadata\DTO\Response\Shared\AddressValueDto;
use Ex3mm\Dadata\Enums\PartyBranchType;
use Ex3mm\Dadata\Enums\PartyType;

final readonly class PartyDataDto
{
    public function __construct(
        // Поля для ИП
        public ?CitizenshipDto $citizenship,
        public ?FioDto $fio,
        // Общие поля (порядок как в API)
        public ?string $source,
        public ?string $qc,
        public ?string $hid,
        public ?PartyType $type,
        public ?PartyStateDto $state,
        public ?PartyOpfDto $opf,
        public ?PartyNameDto $name,
        public ?string $inn,
        public ?string $ogrn,
        public ?string $okpo,
        public ?string $okato,
        public ?string $oktmo,
        public ?string $okogu,
        public ?string $okfs,
        public ?string $okved,
        public mixed $okveds,
        public mixed $authorities,
        public mixed $documents,
        public mixed $licenses,
        public mixed $finance,
        public ?AddressValueDto $address,
        public mixed $phones,
        public mixed $emails,
        public mixed $sites,
        public ?int $ogrnDate,
        public ?string $okvedType,
        public mixed $financeHistory,
        public ?int $employeeCount,
        // Поля для юрлиц
        public ?string $kpp,
        public ?string $kppLargest,
        public mixed $capital,
        public mixed $invalid,
        public ?PartyManagementDto $management,
        public mixed $founders,
        public mixed $managers,
        public mixed $predecessors,
        public mixed $successors,
        public ?PartyBranchType $branchType,
        public ?int $branchCount,
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

        return new self(
            citizenship: self::extractCitizenship($data),
            fio: self::extractFio($data),
            source: self::extractString($data, 'source'),
            qc: self::extractString($data, 'qc'),
            hid: self::extractString($data, 'hid'),
            type: $type,
            state: self::extractState($data),
            opf: self::extractOpf($data),
            name: self::extractName($data),
            inn: self::extractString($data, 'inn'),
            ogrn: self::extractString($data, 'ogrn'),
            okpo: self::extractString($data, 'okpo'),
            okato: self::extractString($data, 'okato'),
            oktmo: self::extractString($data, 'oktmo'),
            okogu: self::extractString($data, 'okogu'),
            okfs: self::extractString($data, 'okfs'),
            okved: self::extractString($data, 'okved'),
            okveds: $data['okveds']           ?? null,
            authorities: $data['authorities'] ?? null,
            documents: $data['documents']     ?? null,
            licenses: $data['licenses']       ?? null,
            finance: $data['finance']         ?? null,
            address: self::extractAddress($data),
            phones: $data['phones'] ?? null,
            emails: $data['emails'] ?? null,
            sites: $data['sites']   ?? null,
            ogrnDate: self::extractInt($data, 'ogrn_date'),
            okvedType: self::extractString($data, 'okved_type'),
            financeHistory: $data['finance_history'] ?? null,
            employeeCount: self::extractInt($data, 'employee_count'),
            kpp: self::extractString($data, 'kpp'),
            kppLargest: self::extractString($data, 'kpp_largest'),
            capital: $data['capital'] ?? null,
            invalid: $data['invalid'] ?? null,
            management: self::extractManagement($data),
            founders: $data['founders']         ?? null,
            managers: $data['managers']         ?? null,
            predecessors: $data['predecessors'] ?? null,
            successors: $data['successors']     ?? null,
            branchType: $branchType,
            branchCount: self::extractInt($data, 'branch_count'),
        );
    }

    /**
     * @param array<string, mixed> $data
     */
    private static function extractCitizenship(array $data): ?CitizenshipDto
    {
        if (!isset($data['citizenship']) || !is_array($data['citizenship'])) {
            return null;
        }

        /** @var array<string, mixed> $citizenship */
        $citizenship = $data['citizenship'];

        return CitizenshipDto::fromArray($citizenship);
    }

    /**
     * @param array<string, mixed> $data
     */
    private static function extractFio(array $data): ?FioDto
    {
        if (!isset($data['fio']) || !is_array($data['fio'])) {
            return null;
        }

        /** @var array<string, mixed> $fio */
        $fio = $data['fio'];

        return FioDto::fromArray($fio);
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
    private static function extractManagement(array $data): ?PartyManagementDto
    {
        if (!isset($data['management']) || !is_array($data['management'])) {
            return null;
        }

        /** @var array<string, mixed> $management */
        $management = $data['management'];

        return PartyManagementDto::fromArray($management);
    }

    /**
     * @param array<string, mixed> $data
     */
    private static function extractState(array $data): ?PartyStateDto
    {
        if (!isset($data['state']) || !is_array($data['state'])) {
            return null;
        }

        /** @var array<string, mixed> $state */
        $state = $data['state'];

        return PartyStateDto::fromArray($state);
    }

    /**
     * @param array<string, mixed> $data
     */
    private static function extractOpf(array $data): ?PartyOpfDto
    {
        if (!isset($data['opf']) || !is_array($data['opf'])) {
            return null;
        }

        /** @var array<string, mixed> $opf */
        $opf = $data['opf'];

        return PartyOpfDto::fromArray($opf);
    }

    /**
     * @param array<string, mixed> $data
     */
    private static function extractName(array $data): ?PartyNameDto
    {
        if (!isset($data['name']) || !is_array($data['name'])) {
            return null;
        }

        /** @var array<string, mixed> $name */
        $name = $data['name'];

        return PartyNameDto::fromArray($name);
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
            'citizenship'     => $this->citizenship?->toArray(),
            'fio'             => $this->fio?->toArray(),
            'source'          => $this->source,
            'qc'              => $this->qc,
            'hid'             => $this->hid,
            'type'            => $this->type?->value,
            'state'           => $this->state?->toArray(),
            'opf'             => $this->opf?->toArray(),
            'name'            => $this->name?->toArray(),
            'inn'             => $this->inn,
            'ogrn'            => $this->ogrn,
            'okpo'            => $this->okpo,
            'okato'           => $this->okato,
            'oktmo'           => $this->oktmo,
            'okogu'           => $this->okogu,
            'okfs'            => $this->okfs,
            'okved'           => $this->okved,
            'okveds'          => $this->okveds,
            'authorities'     => $this->authorities,
            'documents'       => $this->documents,
            'licenses'        => $this->licenses,
            'finance'         => $this->finance,
            'address'         => $this->address?->toArray(),
            'phones'          => $this->phones,
            'emails'          => $this->emails,
            'sites'           => $this->sites,
            'ogrn_date'       => $this->ogrnDate,
            'okved_type'      => $this->okvedType,
            'finance_history' => $this->financeHistory,
            'employee_count'  => $this->employeeCount,
            'kpp'             => $this->kpp,
            'kpp_largest'     => $this->kppLargest,
            'capital'         => $this->capital,
            'invalid'         => $this->invalid,
            'management'      => $this->management?->toArray(),
            'founders'        => $this->founders,
            'managers'        => $this->managers,
            'predecessors'    => $this->predecessors,
            'successors'      => $this->successors,
            'branch_type'     => $this->branchType?->value,
            'branch_count'    => $this->branchCount,
        ];
    }
}

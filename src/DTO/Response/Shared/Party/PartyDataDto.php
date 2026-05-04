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
        /** @var list<OkvedDto>|null */
        public ?array $okveds,
        public ?AuthoritiesDto $authorities,
        public ?DocumentsDto $documents,
        /** @var list<LicenseDto>|null */
        public ?array $licenses,
        public ?FinanceDto $finance,
        public ?AddressValueDto $address,
        /** @var list<PartyPhoneDto>|null */
        public ?array $phones,
        /** @var list<PartyEmailDto>|null */
        public ?array $emails,
        /** @var mixed Сайты компании. Структура не документирована в API DaData. */
        public mixed $sites,
        public ?int $ogrnDate,
        public ?string $okvedType,
        /** @var mixed История финансовых показателей. Структура не документирована в API DaData. */
        public mixed $financeHistory,
        public ?int $employeeCount,
        // Поля для юрлиц
        public ?string $kpp,
        public ?string $kppLargest,
        public ?CapitalDto $capital,
        public ?InvalidityDto $invalid,
        public ?PartyManagementDto $management,
        /** @var list<FounderDto>|null */
        public ?array $founders,
        /** @var list<PartyManagerDto>|null */
        public ?array $managers,
        /** @var list<PredecessorDto>|null */
        public ?array $predecessors,
        /** @var list<SuccessorDto>|null */
        public ?array $successors,
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
            okveds: self::extractOkveds($data),
            authorities: self::extractAuthorities($data),
            documents: self::extractDocuments($data),
            licenses: self::extractLicenses($data),
            finance: self::extractFinance($data),
            address: self::extractAddress($data),
            phones: self::extractPhones($data),
            emails: self::extractEmails($data),
            sites: $data['sites'] ?? null,
            ogrnDate: self::extractInt($data, 'ogrn_date'),
            okvedType: self::extractString($data, 'okved_type'),
            financeHistory: $data['finance_history'] ?? null,
            employeeCount: self::extractInt($data, 'employee_count'),
            kpp: self::extractString($data, 'kpp'),
            kppLargest: self::extractString($data, 'kpp_largest'),
            capital: self::extractCapital($data),
            invalid: self::extractInvalid($data),
            management: self::extractManagement($data),
            founders: self::extractFounders($data),
            managers: self::extractManagers($data),
            predecessors: self::extractPredecessors($data),
            successors: self::extractSuccessors($data),
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
     * @param array<string, mixed> $data
     *
     * @return list<PartyPhoneDto>|null
     */
    private static function extractPhones(array $data): ?array
    {
        if (!isset($data['phones']) || !is_array($data['phones'])) {
            return null;
        }

        $phones = [];
        foreach ($data['phones'] as $phone) {
            if (is_array($phone)) {
                /** @var array<string, mixed> $phone */
                $phones[] = PartyPhoneDto::fromArray($phone);
            }
        }

        return $phones !== [] ? $phones : null;
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return list<PartyEmailDto>|null
     */
    private static function extractEmails(array $data): ?array
    {
        if (!isset($data['emails']) || !is_array($data['emails'])) {
            return null;
        }

        $emails = [];
        foreach ($data['emails'] as $email) {
            if (is_array($email)) {
                /** @var array<string, mixed> $email */
                $emails[] = PartyEmailDto::fromArray($email);
            }
        }

        return $emails !== [] ? $emails : null;
    }

    /**
     * @param array<string, mixed> $data
     */
    private static function extractCapital(array $data): ?CapitalDto
    {
        if (!isset($data['capital']) || !is_array($data['capital'])) {
            return null;
        }

        /** @var array<string, mixed> $capital */
        $capital = $data['capital'];

        return CapitalDto::fromArray($capital);
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return list<PartyManagerDto>|null
     */
    private static function extractManagers(array $data): ?array
    {
        if (!isset($data['managers']) || !is_array($data['managers'])) {
            return null;
        }

        $managers = [];
        foreach ($data['managers'] as $manager) {
            if (is_array($manager)) {
                /** @var array<string, mixed> $manager */
                $managers[] = PartyManagerDto::fromArray($manager);
            }
        }

        return $managers !== [] ? $managers : null;
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return list<OkvedDto>|null
     */
    private static function extractOkveds(array $data): ?array
    {
        if (!isset($data['okveds']) || !is_array($data['okveds'])) {
            return null;
        }

        $okveds = [];
        foreach ($data['okveds'] as $okved) {
            if (is_array($okved)) {
                /** @var array<string, mixed> $okved */
                $okveds[] = OkvedDto::fromArray($okved);
            }
        }

        return $okveds !== [] ? $okveds : null;
    }

    /**
     * @param array<string, mixed> $data
     */
    private static function extractAuthorities(array $data): ?AuthoritiesDto
    {
        if (!isset($data['authorities']) || !is_array($data['authorities'])) {
            return null;
        }

        /** @var array<string, mixed> $authoritiesData */
        $authoritiesData = $data['authorities'];

        return AuthoritiesDto::fromArray($authoritiesData);
    }

    /**
     * @param array<string, mixed> $data
     */
    private static function extractDocuments(array $data): ?DocumentsDto
    {
        if (!isset($data['documents']) || !is_array($data['documents'])) {
            return null;
        }

        /** @var array<string, mixed> $documentsData */
        $documentsData = $data['documents'];

        return DocumentsDto::fromArray($documentsData);
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return list<LicenseDto>|null
     */
    private static function extractLicenses(array $data): ?array
    {
        if (!isset($data['licenses']) || !is_array($data['licenses'])) {
            return null;
        }

        $licenses = [];
        foreach ($data['licenses'] as $license) {
            if (is_array($license)) {
                /** @var array<string, mixed> $license */
                $licenses[] = LicenseDto::fromArray($license);
            }
        }

        return $licenses !== [] ? $licenses : null;
    }

    /**
     * @param array<string, mixed> $data
     */
    private static function extractFinance(array $data): ?FinanceDto
    {
        if (!isset($data['finance']) || !is_array($data['finance'])) {
            return null;
        }

        /** @var array<string, mixed> $financeData */
        $financeData = $data['finance'];

        return FinanceDto::fromArray($financeData);
    }

    /**
     * @param array<string, mixed> $data
     */
    private static function extractInvalid(array $data): ?InvalidityDto
    {
        if (!isset($data['invalid']) || !is_array($data['invalid'])) {
            return null;
        }

        /** @var array<string, mixed> $invalidData */
        $invalidData = $data['invalid'];

        return InvalidityDto::fromArray($invalidData);
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return list<FounderDto>|null
     */
    private static function extractFounders(array $data): ?array
    {
        if (!isset($data['founders']) || !is_array($data['founders'])) {
            return null;
        }

        $founders = [];
        foreach ($data['founders'] as $founder) {
            if (is_array($founder)) {
                /** @var array<string, mixed> $founder */
                $founders[] = FounderDto::fromArray($founder);
            }
        }

        return $founders !== [] ? $founders : null;
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return list<PredecessorDto>|null
     */
    private static function extractPredecessors(array $data): ?array
    {
        if (!isset($data['predecessors']) || !is_array($data['predecessors'])) {
            return null;
        }

        $predecessors = [];
        foreach ($data['predecessors'] as $predecessor) {
            if (is_array($predecessor)) {
                /** @var array<string, mixed> $predecessor */
                $predecessors[] = PredecessorDto::fromArray($predecessor);
            }
        }

        return $predecessors !== [] ? $predecessors : null;
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return list<SuccessorDto>|null
     */
    private static function extractSuccessors(array $data): ?array
    {
        if (!isset($data['successors']) || !is_array($data['successors'])) {
            return null;
        }

        $successors = [];
        foreach ($data['successors'] as $successor) {
            if (is_array($successor)) {
                /** @var array<string, mixed> $successor */
                $successors[] = SuccessorDto::fromArray($successor);
            }
        }

        return $successors !== [] ? $successors : null;
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
            'okveds'          => $this->okveds !== null ? array_map(fn (OkvedDto $okved) => $okved->toArray(), $this->okveds) : null,
            'authorities'     => $this->authorities?->toArray(),
            'documents'       => $this->documents?->toArray(),
            'licenses'        => $this->licenses !== null ? array_map(fn (LicenseDto $license) => $license->toArray(), $this->licenses) : null,
            'finance'         => $this->finance?->toArray(),
            'address'         => $this->address?->toArray(),
            'phones'          => $this->phones !== null ? array_map(fn (PartyPhoneDto $phone) => $phone->toArray(), $this->phones) : null,
            'emails'          => $this->emails !== null ? array_map(fn (PartyEmailDto $email) => $email->toArray(), $this->emails) : null,
            'sites'           => $this->sites,
            'ogrn_date'       => $this->ogrnDate,
            'okved_type'      => $this->okvedType,
            'finance_history' => $this->financeHistory,
            'employee_count'  => $this->employeeCount,
            'kpp'             => $this->kpp,
            'kpp_largest'     => $this->kppLargest,
            'capital'         => $this->capital?->toArray(),
            'invalid'         => $this->invalid?->toArray(),
            'management'      => $this->management?->toArray(),
            'founders'        => $this->founders     !== null ? array_map(fn (FounderDto $founder) => $founder->toArray(), $this->founders) : null,
            'managers'        => $this->managers     !== null ? array_map(fn (PartyManagerDto $manager) => $manager->toArray(), $this->managers) : null,
            'predecessors'    => $this->predecessors !== null ? array_map(fn (PredecessorDto $predecessor) => $predecessor->toArray(), $this->predecessors) : null,
            'successors'      => $this->successors   !== null ? array_map(fn (SuccessorDto $successor) => $successor->toArray(), $this->successors) : null,
            'branch_type'     => $this->branchType?->value,
            'branch_count'    => $this->branchCount,
        ];
    }
}

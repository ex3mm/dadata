<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\DTO\Response\FindParty;

use Ex3mm\Dadata\Enums\PartyStatus;
use Ex3mm\Dadata\Enums\PartyType;

/**
 * Расширенные данные организации из FindParty API.
 */
final readonly class PartyDetailsDTO
{
    /**
     * @param string|null $name Краткое наименование
     * @param string|null $fullName Полное наименование
     * @param string|null $inn ИНН
     * @param string|null $kpp КПП
     * @param string|null $ogrn ОГРН
     * @param string|null $ogrnDate Дата присвоения ОГРН
     * @param PartyType|null $type Тип организации
     * @param PartyStatus|null $status Статус организации
     * @param string|null $address Адрес
     * @param string|null $registrationDate Дата регистрации
     * @param string|null $registrationAuthority Орган регистрации
     * @param string|null $liquidationDate Дата ликвидации
     * @param float|null $capitalSize Размер уставного капитала
     * @param list<string> $okvedCodes Коды ОКВЭД
     */
    public function __construct(
        public ?string $name,
        public ?string $fullName,
        public ?string $inn,
        public ?string $kpp,
        public ?string $ogrn,
        public ?string $ogrnDate,
        public ?PartyType $type,
        public ?PartyStatus $status,
        public ?string $address,
        public ?string $registrationDate,
        public ?string $registrationAuthority,
        public ?string $liquidationDate,
        public ?float $capitalSize,
        public array $okvedCodes,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): static
    {
        $name    = is_array($data['name'] ?? null) ? $data['name'] : [];
        $state   = is_array($data['state'] ?? null) ? $data['state'] : [];
        $address = is_array($data['address'] ?? null) ? $data['address'] : [];
        $capital = is_array($data['capital'] ?? null) ? $data['capital'] : [];

        return new self(
            name: isset($name['short_with_opf'])                           && is_string($name['short_with_opf']) ? $name['short_with_opf'] : null,
            fullName: isset($name['full_with_opf'])                        && is_string($name['full_with_opf']) ? $name['full_with_opf'] : null,
            inn: isset($data['inn'])                                       && is_string($data['inn']) ? $data['inn'] : null,
            kpp: isset($data['kpp'])                                       && is_string($data['kpp']) ? $data['kpp'] : null,
            ogrn: isset($data['ogrn'])                                     && is_string($data['ogrn']) ? $data['ogrn'] : null,
            ogrnDate: isset($data['ogrn_date'])                            && is_string($data['ogrn_date']) ? $data['ogrn_date'] : null,
            type: isset($data['type'])                                     && (is_int($data['type']) || is_string($data['type'])) ? PartyType::from($data['type']) : null,
            status: isset($state['status'])                                && (is_int($state['status']) || is_string($state['status'])) ? PartyStatus::from($state['status']) : null,
            address: isset($address['value'])                              && is_string($address['value']) ? $address['value'] : null,
            registrationDate: isset($state['registration_date'])           && is_string($state['registration_date']) ? $state['registration_date'] : null,
            registrationAuthority: isset($state['registration_authority']) && is_string($state['registration_authority']) ? $state['registration_authority'] : null,
            liquidationDate: isset($state['liquidation_date'])             && is_string($state['liquidation_date']) ? $state['liquidation_date'] : null,
            capitalSize: isset($capital['value'])                          && (is_float($capital['value']) || is_int($capital['value']) || is_string($capital['value'])) ? (float) $capital['value'] : null,
            okvedCodes: is_array($data['okveds'] ?? null) ? array_values(array_filter($data['okveds'], is_string(...))) : [],
        );
    }
}

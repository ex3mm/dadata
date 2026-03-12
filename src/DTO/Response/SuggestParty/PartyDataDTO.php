<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\DTO\Response\SuggestParty;

use Ex3mm\Dadata\Enums\PartyStatus;
use Ex3mm\Dadata\Enums\PartyType;

/**
 * Детальные данные организации из DaData API.
 */
final readonly class PartyDataDTO
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
     * @param string|null $managementName ФИО руководителя
     * @param string|null $managementPost Должность руководителя
     * @param string|null $address Адрес
     * @param string|null $okved Основной ОКВЭД
     * @param string|null $okvedType Тип ОКВЭД
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
        public ?string $managementName,
        public ?string $managementPost,
        public ?string $address,
        public ?string $okved,
        public ?string $okvedType,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): static
    {
        $name       = is_array($data['name'] ?? null) ? $data['name'] : [];
        $state      = is_array($data['state'] ?? null) ? $data['state'] : [];
        $management = is_array($data['management'] ?? null) ? $data['management'] : [];
        $address    = is_array($data['address'] ?? null) ? $data['address'] : [];

        return new self(
            name: isset($name['short_with_opf'])       && is_string($name['short_with_opf']) ? $name['short_with_opf'] : null,
            fullName: isset($name['full_with_opf'])    && is_string($name['full_with_opf']) ? $name['full_with_opf'] : null,
            inn: isset($data['inn'])                   && is_string($data['inn']) ? $data['inn'] : null,
            kpp: isset($data['kpp'])                   && is_string($data['kpp']) ? $data['kpp'] : null,
            ogrn: isset($data['ogrn'])                 && is_string($data['ogrn']) ? $data['ogrn'] : null,
            ogrnDate: isset($data['ogrn_date'])        && is_string($data['ogrn_date']) ? $data['ogrn_date'] : null,
            type: isset($data['type'])                 && (is_int($data['type']) || is_string($data['type'])) ? PartyType::from($data['type']) : null,
            status: isset($state['status'])            && (is_int($state['status']) || is_string($state['status'])) ? PartyStatus::from($state['status']) : null,
            managementName: isset($management['name']) && is_string($management['name']) ? $management['name'] : null,
            managementPost: isset($management['post']) && is_string($management['post']) ? $management['post'] : null,
            address: isset($address['value'])          && is_string($address['value']) ? $address['value'] : null,
            okved: isset($data['okved'])               && is_string($data['okved']) ? $data['okved'] : null,
            okvedType: isset($data['okved_type'])      && is_string($data['okved_type']) ? $data['okved_type'] : null,
        );
    }
}

<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\Tests\Unit\DTO;

use Ex3mm\Dadata\DTO\Response\Shared\AddressValueDto;
use Ex3mm\Dadata\DTO\Response\Shared\Party\PartyManagementDto;
use Ex3mm\Dadata\DTO\Response\Shared\Party\PartyResponseDto;
use Ex3mm\Dadata\Enums\PartyBranchType;
use Ex3mm\Dadata\Tests\TestCase;

/** Тест для полей юридического лица */
final class PartyLegalEntityTest extends TestCase
{
    public function test_parses_legal_entity_with_all_fields(): void
    {
        $data = [
            'value'              => 'ООО "СПЕЦИАЛИЗИРОВАННЫЙ ЗАСТРОЙЩИК "ВЕСЁЛОВКА-4"',
            'unrestricted_value' => 'ООО "СПЕЦИАЛИЗИРОВАННЫЙ ЗАСТРОЙЩИК "ВЕСЁЛОВКА-4"',
            'data'               => [
                'kpp'         => '583601001',
                'kpp_largest' => null,
                'capital'     => [
                    'type'  => 'УСТАВНЫЙ КАПИТАЛ',
                    'value' => 400000000,
                ],
                'invalid'    => null,
                'management' => [
                    'name'         => 'Полежаева Ирина Васильевна',
                    'post'         => 'ГЕНЕРАЛЬНЫЙ ДИРЕКТОР',
                    'start_date'   => 1552942800000,
                    'disqualified' => null,
                ],
                'founders' => [
                    [
                        'inn' => '645501716614',
                        'fio' => [
                            'surname'    => 'Фомичёв',
                            'name'       => 'Сергей',
                            'patronymic' => 'Сергеевич',
                            'gender'     => 'MALE',
                            'source'     => 'ФОМИЧЁВ СЕРГЕЙ СЕРГЕЕВИЧ',
                            'qc'         => null,
                        ],
                        'type'  => 'PHYSICAL',
                        'share' => [
                            'value' => 0.1,
                            'type'  => 'PERCENT',
                        ],
                    ],
                ],
                'managers' => [
                    [
                        'inn' => '583408173320',
                        'fio' => [
                            'surname'    => 'Полежаева',
                            'name'       => 'Ирина',
                            'patronymic' => 'Васильевна',
                            'gender'     => 'FEMALE',
                            'source'     => 'ПОЛЕЖАЕВА ИРИНА ВАСИЛЬЕВНА',
                            'qc'         => null,
                        ],
                        'post' => 'ГЕНЕРАЛЬНЫЙ ДИРЕКТОР',
                        'type' => 'EMPLOYEE',
                    ],
                ],
                'predecessors' => null,
                'successors'   => null,
                'branch_type'  => 'MAIN',
                'branch_count' => 0,
                'source'       => null,
                'qc'           => null,
                'hid'          => '55cef339dd77550d936e7bd825305a46e0d98b181369c9aac384be369313c910',
                'type'         => 'LEGAL',
                'state'        => [
                    'status'            => 'ACTIVE',
                    'code'              => null,
                    'actuality_date'    => 1776643200000,
                    'registration_date' => 1382659200000,
                    'liquidation_date'  => null,
                ],
                'opf' => [
                    'type'  => '2014',
                    'code'  => '12300',
                    'full'  => 'Общество с ограниченной ответственностью',
                    'short' => 'ООО',
                ],
                'name' => [
                    'full_with_opf'  => 'ОБЩЕСТВО С ОГРАНИЧЕННОЙ ОТВЕТСТВЕННОСТЬЮ "СПЕЦИАЛИЗИРОВАННЫЙ ЗАСТРОЙЩИК "ВЕСЁЛОВКА-4"',
                    'short_with_opf' => 'ООО "СПЕЦИАЛИЗИРОВАННЫЙ ЗАСТРОЙЩИК "ВЕСЁЛОВКА-4"',
                    'latin'          => null,
                    'full'           => 'СПЕЦИАЛИЗИРОВАННЫЙ ЗАСТРОЙЩИК ВЕСЁЛОВКА-4',
                    'short'          => 'СПЕЦИАЛИЗИРОВАННЫЙ ЗАСТРОЙЩИК ВЕСЁЛОВКА-4',
                ],
                'inn'    => '5836658151',
                'ogrn'   => '1135836003654',
                'okpo'   => '14812517',
                'okato'  => '56401368000',
                'oktmo'  => '56701000001',
                'okogu'  => '4210014',
                'okfs'   => '16',
                'okved'  => '71.12.2',
                'okveds' => [
                    [
                        'main' => true,
                        'type' => '2014',
                        'code' => '71.12.2',
                        'name' => 'Деятельность заказчика-застройщика, генерального подрядчика',
                    ],
                ],
                'authorities' => [
                    'fts_registration' => [
                        'type'    => 'FEDERAL_TAX_SERVICE',
                        'code'    => '5800',
                        'name'    => 'Управление Федеральной налоговой службы по Пензенской области',
                        'address' => '440008, Пензенская обл., г. Пенза, ул. Коммунистическая, д. 32',
                    ],
                ],
                'documents' => [
                    'fts_registration' => [
                        'type'            => 'FTS_REGISTRATION',
                        'series'          => '58',
                        'number'          => '002006157',
                        'issue_date'      => 1382659200000,
                        'issue_authority' => '5836',
                    ],
                ],
                'licenses' => null,
                'finance'  => [
                    'tax_system' => 'USN',
                    'income'     => 287602000,
                    'expense'    => 91942000,
                    'revenue'    => 279606000,
                    'debt'       => null,
                    'penalty'    => null,
                    'year'       => 2025,
                ],
                'address' => [
                    'value'              => 'г Пенза, ул Захарова, д 1 литера а, помещ 1',
                    'unrestricted_value' => '440008, Пензенская обл, г Пенза, Ленинский р-н, ул Захарова, д 1 литера а, помещ 1',
                    'invalidity'         => null,
                    'data'               => [
                        'postal_code' => '440008',
                        'region'      => 'Пензенская',
                        'city'        => 'Пенза',
                    ],
                ],
                'phones' => [
                    [
                        'value'              => '+7 8412 209580',
                        'unrestricted_value' => '+7 8412 209580',
                        'data'               => [
                            'type'   => 'Прямой мобильный',
                            'number' => '209580',
                        ],
                    ],
                ],
                'emails'          => null,
                'sites'           => null,
                'ogrn_date'       => 1382659200000,
                'okved_type'      => '2014',
                'finance_history' => null,
                'employee_count'  => 4,
            ],
        ];

        $dto = PartyResponseDto::fromArray($data);

        $this->assertSame('ООО "СПЕЦИАЛИЗИРОВАННЫЙ ЗАСТРОЙЩИК "ВЕСЁЛОВКА-4"', $dto->value);

        // Проверяем поля юрлица
        $this->assertSame('583601001', $dto->data->kpp);
        $this->assertNull($dto->data->kppLargest);
        $this->assertIsArray($dto->data->capital);
        $this->assertSame('УСТАВНЫЙ КАПИТАЛ', $dto->data->capital['type']);
        $this->assertSame(400000000, $dto->data->capital['value']);
        $this->assertNull($dto->data->invalid);

        // Проверяем management
        $this->assertInstanceOf(PartyManagementDto::class, $dto->data->management);
        $this->assertSame('Полежаева Ирина Васильевна', $dto->data->management->name);
        $this->assertSame('ГЕНЕРАЛЬНЫЙ ДИРЕКТОР', $dto->data->management->post);

        // Проверяем founders и managers
        $this->assertIsArray($dto->data->founders);
        $this->assertCount(1, $dto->data->founders);
        $this->assertSame('645501716614', $dto->data->founders[0]['inn']);

        $this->assertIsArray($dto->data->managers);
        $this->assertCount(1, $dto->data->managers);
        $this->assertSame('583408173320', $dto->data->managers[0]['inn']);

        // Проверяем branch_type
        $this->assertSame(PartyBranchType::MAIN, $dto->data->branchType);
        $this->assertSame(0, $dto->data->branchCount);

        // Проверяем что citizenship и fio null для юрлица
        $this->assertNull($dto->data->citizenship);
        $this->assertNull($dto->data->fio);

        // Проверяем общие поля
        $this->assertSame('LEGAL', $dto->data->type->value);
        $this->assertSame('5836658151', $dto->data->inn);
        $this->assertSame('1135836003654', $dto->data->ogrn);
        $this->assertSame(4, $dto->data->employeeCount);

        // Проверяем finance
        $this->assertIsArray($dto->data->finance);
        $this->assertSame('USN', $dto->data->finance['tax_system']);
        $this->assertSame(287602000, $dto->data->finance['income']);

        // Проверяем address
        $this->assertInstanceOf(AddressValueDto::class, $dto->data->address);

        // Проверяем phones
        $this->assertIsArray($dto->data->phones);
        $this->assertCount(1, $dto->data->phones);
    }

    public function test_to_array_includes_legal_entity_fields(): void
    {
        $data = [
            'value'              => 'ООО "Тестовая"',
            'unrestricted_value' => 'ООО "Тестовая"',
            'data'               => [
                'kpp'     => '123456789',
                'capital' => [
                    'type'  => 'УСТАВНЫЙ КАПИТАЛ',
                    'value' => 10000,
                ],
                'invalid'     => null,
                'type'        => 'LEGAL',
                'branch_type' => 'MAIN',
                'inn'         => '1234567890',
                'name'        => [
                    'full' => 'Тестовая',
                ],
                'opf' => [
                    'code' => '12300',
                    'full' => 'Общество с ограниченной ответственностью',
                ],
                'state' => [
                    'status' => 'ACTIVE',
                ],
            ],
        ];

        $dto   = PartyResponseDto::fromArray($data);
        $array = $dto->toArray();

        $this->assertArrayHasKey('data', $array);
        $this->assertArrayHasKey('kpp', $array['data']);
        $this->assertArrayHasKey('capital', $array['data']);
        $this->assertArrayHasKey('invalid', $array['data']);
        $this->assertArrayHasKey('branch_type', $array['data']);

        $this->assertSame('123456789', $array['data']['kpp']);
        $this->assertIsArray($array['data']['capital']);
        $this->assertNull($array['data']['invalid']);
        $this->assertSame('MAIN', $array['data']['branch_type']);
    }
}

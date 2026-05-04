<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\Tests\Unit\DTO;

use Ex3mm\Dadata\DTO\Response\Shared\Party\CitizenshipCodeDto;
use Ex3mm\Dadata\DTO\Response\Shared\Party\CitizenshipDto;
use Ex3mm\Dadata\DTO\Response\Shared\Party\CitizenshipNameDto;
use Ex3mm\Dadata\DTO\Response\Shared\Party\FioDto;
use Ex3mm\Dadata\DTO\Response\Shared\Party\PartyResponseDto;
use Ex3mm\Dadata\Tests\TestCase;

/** Тест для полей ИП (citizenship, fio) */
final class PartyIndividualEntrepreneurTest extends TestCase
{
    public function test_parses_individual_entrepreneur_with_citizenship_and_fio(): void
    {
        $data = [
            'value'              => 'ИП Шляхтина Анна Олеговна',
            'unrestricted_value' => 'ИП Шляхтина Анна Олеговна',
            'data'               => [
                'citizenship' => [
                    'code' => [
                        'numeric' => 643,
                        'alpha_3' => 'RUS',
                    ],
                    'name' => [
                        'full'  => 'Российская Федерация',
                        'short' => 'Россия',
                    ],
                ],
                'fio' => [
                    'surname'    => 'Шляхтина',
                    'name'       => 'Анна',
                    'patronymic' => 'Олеговна',
                    'gender'     => null,
                    'source'     => null,
                    'qc'         => null,
                ],
                'hid'   => '88eaa51b1c48a528492386c985336a7e2a76213df6c41a909db8dc20744f6a5e',
                'type'  => 'INDIVIDUAL',
                'state' => [
                    'status'            => 'ACTIVE',
                    'code'              => null,
                    'actuality_date'    => 1776297600000,
                    'registration_date' => 1776211200000,
                    'liquidation_date'  => null,
                ],
                'opf' => [
                    'type'  => '2014',
                    'code'  => '50102',
                    'full'  => 'Индивидуальный предприниматель',
                    'short' => 'ИП',
                ],
                'name' => [
                    'full_with_opf'  => 'Индивидуальный предприниматель Шляхтина Анна Олеговна',
                    'short_with_opf' => 'ИП Шляхтина Анна Олеговна',
                    'latin'          => null,
                    'full'           => 'Шляхтина Анна Олеговна',
                    'short'          => null,
                ],
                'inn'    => '740419554675',
                'ogrn'   => '326774600279782',
                'okpo'   => '2051301700',
                'okato'  => '45283582000',
                'oktmo'  => '45371000000',
                'okogu'  => '4210015',
                'okfs'   => '16',
                'emails' => [
                    [
                        'value'              => 'K.E.V.FINANCEDXB@GMAIL.COM',
                        'unrestricted_value' => 'K.E.V.FINANCEDXB@GMAIL.COM',
                        'data'               => [
                            'local'  => 'K.E.V.FINANCEDXB',
                            'domain' => 'GMAIL.COM',
                            'type'   => null,
                            'source' => 'K.E.V.FINANCEDXB@GMAIL.COM',
                            'qc'     => null,
                        ],
                    ],
                ],
                'sites'           => null,
                'ogrn_date'       => 1776211200000,
                'okved_type'      => '2014',
                'finance_history' => null,
                'employee_count'  => null,
            ],
        ];

        $dto = PartyResponseDto::fromArray($data);

        $this->assertSame('ИП Шляхтина Анна Олеговна', $dto->value);
        $this->assertSame('ИП Шляхтина Анна Олеговна', $dto->unrestrictedValue);

        // Проверяем citizenship
        $this->assertInstanceOf(CitizenshipDto::class, $dto->data->citizenship);
        $this->assertInstanceOf(CitizenshipCodeDto::class, $dto->data->citizenship->code);
        $this->assertSame(643, $dto->data->citizenship->code->numeric);
        $this->assertSame('RUS', $dto->data->citizenship->code->alpha3);

        $this->assertInstanceOf(CitizenshipNameDto::class, $dto->data->citizenship->name);
        $this->assertSame('Российская Федерация', $dto->data->citizenship->name->full);
        $this->assertSame('Россия', $dto->data->citizenship->name->short);

        // Проверяем fio
        $this->assertInstanceOf(FioDto::class, $dto->data->fio);
        $this->assertSame('Шляхтина', $dto->data->fio->surname);
        $this->assertSame('Анна', $dto->data->fio->name);
        $this->assertSame('Олеговна', $dto->data->fio->patronymic);
        $this->assertNull($dto->data->fio->gender);
        $this->assertNull($dto->data->fio->source);
        $this->assertNull($dto->data->fio->qc);

        // Проверяем новые поля
        $this->assertNull($dto->data->sites);
        $this->assertNull($dto->data->financeHistory);
        $this->assertIsArray($dto->data->emails);
        $this->assertCount(1, $dto->data->emails);
        $this->assertInstanceOf(\Ex3mm\Dadata\DTO\Response\Shared\Party\PartyEmailDto::class, $dto->data->emails[0]);
        $this->assertSame('K.E.V.FINANCEDXB@GMAIL.COM', $dto->data->emails[0]->value);
        $this->assertSame('K.E.V.FINANCEDXB@GMAIL.COM', $dto->data->emails[0]->unrestrictedValue);
        $this->assertInstanceOf(\Ex3mm\Dadata\DTO\Response\Shared\Party\PartyEmailDataDto::class, $dto->data->emails[0]->data);
        $this->assertSame('K.E.V.FINANCEDXB', $dto->data->emails[0]->data->local);
        $this->assertSame('GMAIL.COM', $dto->data->emails[0]->data->domain);
    }

    public function test_handles_missing_citizenship_and_fio_gracefully(): void
    {
        $data = [
            'value'              => 'ООО "Тестовая компания"',
            'unrestricted_value' => 'ООО "Тестовая компания"',
            'data'               => [
                'type' => 'LEGAL',
                'inn'  => '1234567890',
                'name' => [
                    'full'  => 'Тестовая компания',
                    'short' => 'Тестовая',
                ],
                'opf' => [
                    'type'  => '2014',
                    'code'  => '12300',
                    'full'  => 'Общество с ограниченной ответственностью',
                    'short' => 'ООО',
                ],
                'state' => [
                    'status' => 'ACTIVE',
                ],
            ],
        ];

        $dto = PartyResponseDto::fromArray($data);

        // Для юрлиц citizenship и fio должны быть null
        $this->assertNull($dto->data->citizenship);
        $this->assertNull($dto->data->fio);
    }

    public function test_to_array_includes_citizenship_and_fio(): void
    {
        $data = [
            'value'              => 'ИП Иванов Иван Иванович',
            'unrestricted_value' => 'ИП Иванов Иван Иванович',
            'data'               => [
                'citizenship' => [
                    'code' => [
                        'numeric' => 643,
                        'alpha_3' => 'RUS',
                    ],
                    'name' => [
                        'full'  => 'Российская Федерация',
                        'short' => 'Россия',
                    ],
                ],
                'fio' => [
                    'surname'    => 'Иванов',
                    'name'       => 'Иван',
                    'patronymic' => 'Иванович',
                    'gender'     => 'MALE',
                    'source'     => null,
                    'qc'         => null,
                ],
                'type' => 'INDIVIDUAL',
                'inn'  => '123456789012',
                'name' => [
                    'full' => 'Иванов Иван Иванович',
                ],
                'opf' => [
                    'code' => '50102',
                    'full' => 'Индивидуальный предприниматель',
                ],
                'state' => [
                    'status' => 'ACTIVE',
                ],
            ],
        ];

        $dto   = PartyResponseDto::fromArray($data);
        $array = $dto->toArray();

        $this->assertArrayHasKey('data', $array);
        $this->assertArrayHasKey('citizenship', $array['data']);
        $this->assertArrayHasKey('fio', $array['data']);

        $this->assertSame(643, $array['data']['citizenship']['code']['numeric']);
        $this->assertSame('RUS', $array['data']['citizenship']['code']['alpha_3']);
        $this->assertSame('Российская Федерация', $array['data']['citizenship']['name']['full']);

        $this->assertSame('Иванов', $array['data']['fio']['surname']);
        $this->assertSame('Иван', $array['data']['fio']['name']);
        $this->assertSame('Иванович', $array['data']['fio']['patronymic']);
        $this->assertSame('MALE', $array['data']['fio']['gender']);
    }

    public function test_parses_liquidated_individual_entrepreneur(): void
    {
        $data = [
            'value'              => 'ИП Кудашев Сергей Борисович',
            'unrestricted_value' => 'ИП Кудашев Сергей Борисович',
            'data'               => [
                'citizenship' => [
                    'code' => [
                        'numeric' => 643,
                        'alpha_3' => 'RUS',
                    ],
                    'name' => [
                        'full'  => 'Российская Федерация',
                        'short' => 'Россия',
                    ],
                ],
                'fio' => [
                    'surname'    => 'Кудашев',
                    'name'       => 'Сергей',
                    'patronymic' => 'Борисович',
                    'gender'     => null,
                    'source'     => null,
                    'qc'         => null,
                ],
                'source' => null,
                'qc'     => null,
                'hid'    => '97778346394797098dac229506a33fc5bbeee346134498585f034914cc41b334',
                'type'   => 'INDIVIDUAL',
                'state'  => [
                    'status'            => 'LIQUIDATED',
                    'code'              => '201',
                    'actuality_date'    => 1185753600000,
                    'registration_date' => 1013385600000,
                    'liquidation_date'  => 1113868800000,
                ],
                'opf' => [
                    'type'  => '2014',
                    'code'  => '50102',
                    'full'  => 'Индивидуальный предприниматель',
                    'short' => 'ИП',
                ],
                'name' => [
                    'full_with_opf'  => 'Индивидуальный предприниматель Кудашев Сергей Борисович',
                    'short_with_opf' => 'ИП Кудашев Сергей Борисович',
                    'latin'          => null,
                    'full'           => 'Кудашев Сергей Борисович',
                    'short'          => null,
                ],
                'inn'    => '645102144394',
                'ogrn'   => '304645110000173',
                'okpo'   => null,
                'okato'  => null,
                'oktmo'  => null,
                'okogu'  => null,
                'okfs'   => null,
                'okved'  => '52.12',
                'okveds' => [
                    [
                        'main' => true,
                        'type' => '2001',
                        'code' => '52.12',
                        'name' => 'Прочая розничная торговля в неспециализированных магазинах',
                    ],
                    [
                        'main' => false,
                        'type' => '2001',
                        'code' => '74.84',
                        'name' => 'Предоставление прочих услуг',
                    ],
                ],
                'authorities' => [
                    'fts_registration' => [
                        'type'    => 'FEDERAL_TAX_SERVICE',
                        'code'    => '6457',
                        'name'    => 'Межрайонная инспекция Федеральной налоговой службы №22 по Саратовской области',
                        'address' => ',410010,Саратовская обл,,Саратов г,,Бирюзова ул,7А,,',
                    ],
                    'fts_report' => null,
                    'pf'         => [
                        'type'    => 'PENSION_FUND',
                        'code'    => '073040',
                        'name'    => 'Отделение Фонда пенсионного и социального страхования Российской Федерации по Саратовской области',
                        'address' => null,
                    ],
                    'sif' => null,
                ],
                'documents' => [
                    'fts_registration' => [
                        'type'            => 'FTS_REGISTRATION',
                        'series'          => '64',
                        'number'          => '001642409',
                        'issue_date'      => 1081468800000,
                        'issue_authority' => '6451',
                    ],
                    'fts_report'      => null,
                    'pf_registration' => [
                        'type'            => 'PF_REGISTRATION',
                        'series'          => null,
                        'number'          => '073040018675',
                        'issue_date'      => 1074556800000,
                        'issue_authority' => '073040',
                    ],
                    'sif_registration' => null,
                    'smb'              => null,
                ],
                'licenses' => null,
                'finance'  => null,
                'address'  => [
                    'value'              => 'г Саратов',
                    'unrestricted_value' => '410000, Саратовская обл, г Саратов',
                    'invalidity'         => null,
                    'data'               => [
                        'postal_code' => '410000',
                        'region'      => 'Саратовская',
                        'city'        => 'Саратов',
                    ],
                ],
                'phones'          => null,
                'emails'          => null,
                'sites'           => null,
                'ogrn_date'       => 1081468800000,
                'okved_type'      => '2001',
                'finance_history' => null,
                'employee_count'  => null,
            ],
        ];

        $dto = PartyResponseDto::fromArray($data);

        $this->assertSame('ИП Кудашев Сергей Борисович', $dto->value);
        $this->assertInstanceOf(CitizenshipDto::class, $dto->data->citizenship);
        $this->assertInstanceOf(FioDto::class, $dto->data->fio);
        $this->assertSame('Кудашев', $dto->data->fio->surname);
        $this->assertSame('Сергей', $dto->data->fio->name);
        $this->assertSame('Борисович', $dto->data->fio->patronymic);

        // Проверяем статус ликвидации
        $this->assertSame('LIQUIDATED', $dto->data->state->status->value);
        $this->assertSame('201', $dto->data->state->code);
        $this->assertSame(1113868800000, $dto->data->state->liquidationDate);

        // Проверяем что null-поля корректно обрабатываются
        $this->assertNull($dto->data->okpo);
        $this->assertNull($dto->data->okato);
        $this->assertNull($dto->data->oktmo);
        $this->assertNull($dto->data->phones);
        $this->assertNull($dto->data->emails);
    }
}

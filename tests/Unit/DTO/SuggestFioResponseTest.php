<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\Tests\Unit\DTO;

use Ex3mm\Dadata\DTO\Response\SuggestFio\FioDataDto;
use Ex3mm\Dadata\DTO\Response\SuggestFio\FioSuggestionResponseDto;
use Ex3mm\Dadata\Enums\Gender;
use Ex3mm\Dadata\Tests\TestCase;

/** Тест для подсказок ФИО */
final class SuggestFioResponseTest extends TestCase
{
    public function test_parses_fio_suggestion_male_name(): void
    {
        $data = [
            'value'              => 'Виктор',
            'unrestricted_value' => 'Виктор',
            'data'               => [
                'surname'    => null,
                'name'       => 'Виктор',
                'patronymic' => null,
                'gender'     => 'MALE',
                'source'     => null,
                'qc'         => '0',
            ],
        ];

        $dto = FioSuggestionResponseDto::fromArray($data);

        $this->assertSame('Виктор', $dto->value);
        $this->assertSame('Виктор', $dto->unrestrictedValue);
        $this->assertInstanceOf(FioDataDto::class, $dto->data);
        $this->assertNull($dto->data->surname);
        $this->assertSame('Виктор', $dto->data->name);
        $this->assertNull($dto->data->patronymic);
        $this->assertSame(Gender::MALE, $dto->data->gender);
        $this->assertNull($dto->data->source);
        $this->assertSame('0', $dto->data->qc);
    }

    public function test_parses_fio_suggestion_female_name(): void
    {
        $data = [
            'value'              => 'Виктория',
            'unrestricted_value' => 'Виктория',
            'data'               => [
                'surname'    => null,
                'name'       => 'Виктория',
                'patronymic' => null,
                'gender'     => 'FEMALE',
                'source'     => null,
                'qc'         => '0',
            ],
        ];

        $dto = FioSuggestionResponseDto::fromArray($data);

        $this->assertSame('Виктория', $dto->value);
        $this->assertSame(Gender::FEMALE, $dto->data->gender);
        $this->assertSame('Виктория', $dto->data->name);
    }

    public function test_parses_fio_suggestion_surname(): void
    {
        $data = [
            'value'              => 'Викторов',
            'unrestricted_value' => 'Викторов',
            'data'               => [
                'surname'    => 'Викторов',
                'name'       => null,
                'patronymic' => null,
                'gender'     => 'MALE',
                'source'     => null,
                'qc'         => '0',
            ],
        ];

        $dto = FioSuggestionResponseDto::fromArray($data);

        $this->assertSame('Викторов', $dto->value);
        $this->assertSame('Викторов', $dto->data->surname);
        $this->assertNull($dto->data->name);
        $this->assertSame(Gender::MALE, $dto->data->gender);
    }

    public function test_parses_fio_suggestion_unknown_gender(): void
    {
        $data = [
            'value'              => 'Викторенко',
            'unrestricted_value' => 'Викторенко',
            'data'               => [
                'surname'    => 'Викторенко',
                'name'       => null,
                'patronymic' => null,
                'gender'     => 'UNKNOWN',
                'source'     => null,
                'qc'         => '0',
            ],
        ];

        $dto = FioSuggestionResponseDto::fromArray($data);

        $this->assertSame('Викторенко', $dto->value);
        $this->assertSame(Gender::UNKNOWN, $dto->data->gender);
    }

    public function test_to_array_returns_correct_structure(): void
    {
        $data = [
            'value'              => 'Виктор',
            'unrestricted_value' => 'Виктор',
            'data'               => [
                'surname'    => null,
                'name'       => 'Виктор',
                'patronymic' => null,
                'gender'     => 'MALE',
                'source'     => null,
                'qc'         => '0',
            ],
        ];

        $dto   = FioSuggestionResponseDto::fromArray($data);
        $array = $dto->toArray();

        $this->assertArrayHasKey('value', $array);
        $this->assertArrayHasKey('unrestricted_value', $array);
        $this->assertArrayHasKey('data', $array);
        $this->assertSame('Виктор', $array['value']);
        $this->assertSame('MALE', $array['data']['gender']);
        $this->assertSame('Виктор', $array['data']['name']);
        $this->assertNull($array['data']['surname']);
    }
}

<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\Tests\Unit\DTO;

use Ex3mm\Dadata\DTO\Response\Shared\AddressDataDto;
use Ex3mm\Dadata\DTO\Response\Shared\AddressValueDto;
use Ex3mm\Dadata\DTO\Response\Shared\Bank\BankDataDto;
use Ex3mm\Dadata\DTO\Response\Shared\Bank\BankNameDto;
use Ex3mm\Dadata\DTO\Response\Shared\Bank\BankOpfDto;
use Ex3mm\Dadata\DTO\Response\Shared\Bank\BankResponseDto;
use Ex3mm\Dadata\DTO\Response\Shared\Bank\BankStateDto;
use Ex3mm\Dadata\Enums\BankOpfType;
use Ex3mm\Dadata\Enums\BankStateStatus;
use Ex3mm\Dadata\Tests\TestCase;

final class SuggestBankResponseTest extends TestCase
{
    public function test_parses_bank_suggestion_from_fixture(): void
    {
        $fixtureJson = file_get_contents(__DIR__ . '/../../fixtures/suggest_bank_response.json');
        $this->assertNotFalse($fixtureJson);
        $fixture = json_decode($fixtureJson, true);
        $this->assertIsArray($fixture);

        $item = $fixture['suggestions'][0];
        $dto  = BankResponseDto::fromArray($item);

        $this->assertInstanceOf(BankResponseDto::class, $dto);
        $this->assertSame((string) $item['value'], $dto->value);
        $this->assertSame((string) $item['unrestricted_value'], $dto->unrestrictedValue);
        $this->assertInstanceOf(BankDataDto::class, $dto->data);
    }

    public function test_bank_data_fields_are_mapped(): void
    {
        $fixtureJson = file_get_contents(__DIR__ . '/../../fixtures/suggest_bank_response.json');
        $fixture     = json_decode((string) $fixtureJson, true);
        $item        = $fixture['suggestions'][0];

        $dto = BankResponseDto::fromArray($item);

        $expectedData = $item['data'];

        $this->assertSame($expectedData['bic'] ?? null, $dto->data->bic);
        $this->assertSame($expectedData['swift'] ?? null, $dto->data->swift);
        $this->assertSame($expectedData['inn'] ?? null, $dto->data->inn);
        $this->assertSame($expectedData['kpp'] ?? null, $dto->data->kpp);
        $this->assertSame($expectedData['correspondent_account'] ?? null, $dto->data->correspondentAccount);
        $this->assertSame($expectedData['registration_number'] ?? null, $dto->data->registrationNumber);
        $this->assertSame($expectedData['payment_city'] ?? null, $dto->data->paymentCity);
        $this->assertInstanceOf(BankOpfDto::class, $dto->data->opf);
        $this->assertSame(
            isset($expectedData['opf']['type']) && is_string($expectedData['opf']['type']) ? BankOpfType::tryFrom($expectedData['opf']['type']) : null,
            $dto->data->opf?->type
        );
        $this->assertInstanceOf(BankNameDto::class, $dto->data->name);
        $this->assertSame($expectedData['name']['payment'] ?? null, $dto->data->name?->payment);
        $this->assertInstanceOf(BankStateDto::class, $dto->data->state);
        $this->assertSame(
            isset($expectedData['state']['status']) && is_string($expectedData['state']['status']) ? BankStateStatus::tryFrom($expectedData['state']['status']) : null,
            $dto->data->state?->status
        );
        $this->assertInstanceOf(AddressValueDto::class, $dto->data->address);
        $this->assertSame($expectedData['address']['value'] ?? null, $dto->data->address?->value);
        $this->assertInstanceOf(AddressDataDto::class, $dto->data->address?->data);
    }
}

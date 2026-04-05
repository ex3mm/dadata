<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\Tests\Unit\DTO;

use Ex3mm\Dadata\DTO\Response\Shared\AddressValueDto;
use Ex3mm\Dadata\DTO\Response\Shared\Party\PartyDataDto;
use Ex3mm\Dadata\DTO\Response\Shared\Party\PartyManagementDto;
use Ex3mm\Dadata\DTO\Response\Shared\Party\PartyNameDto;
use Ex3mm\Dadata\DTO\Response\Shared\Party\PartyOpfDto;
use Ex3mm\Dadata\DTO\Response\Shared\Party\PartyResponseDto;
use Ex3mm\Dadata\Enums\PartyBranchType;
use Ex3mm\Dadata\Enums\PartyType;
use Ex3mm\Dadata\Tests\TestCase;

final class SuggestPartyResponseTest extends TestCase
{
    public function test_parses_party_suggestion_from_fixture(): void
    {
        $fixtureJson = file_get_contents(__DIR__ . '/../../fixtures/suggest_party_response.json');
        $this->assertNotFalse($fixtureJson);
        $fixture = json_decode($fixtureJson, true);
        $this->assertIsArray($fixture);

        $item = $fixture['suggestions'][0];
        $dto  = PartyResponseDto::fromArray($item);

        $this->assertInstanceOf(PartyResponseDto::class, $dto);
        $this->assertSame((string) $item['value'], $dto->value);
        $this->assertSame((string) $item['unrestricted_value'], $dto->unrestrictedValue);
        $this->assertInstanceOf(PartyDataDto::class, $dto->data);
    }

    public function test_party_data_fields_are_mapped(): void
    {
        $fixtureJson = file_get_contents(__DIR__ . '/../../fixtures/suggest_party_response.json');
        $fixture     = json_decode((string) $fixtureJson, true);
        $item        = $fixture['suggestions'][0];

        $dto          = PartyResponseDto::fromArray($item);
        $expectedData = $item['data'];

        $this->assertSame($expectedData['inn'] ?? null, $dto->data->inn);
        $this->assertSame($expectedData['kpp'] ?? null, $dto->data->kpp);
        $this->assertSame($expectedData['ogrn'] ?? null, $dto->data->ogrn);
        $this->assertSame($expectedData['hid'] ?? null, $dto->data->hid);
        $this->assertSame($expectedData['okved'] ?? null, $dto->data->okved);
        $this->assertSame($expectedData['okved_type'] ?? null, $dto->data->okvedType);
        $this->assertSame($expectedData['employee_count'] ?? null, $dto->data->employeeCount);
        $this->assertSame(
            isset($expectedData['type']) && is_string($expectedData['type']) ? PartyType::tryFrom($expectedData['type']) : null,
            $dto->data->type
        );
        $this->assertSame(
            isset($expectedData['branch_type']) && is_string($expectedData['branch_type']) ? PartyBranchType::tryFrom($expectedData['branch_type']) : null,
            $dto->data->branchType
        );
        $this->assertInstanceOf(PartyManagementDto::class, $dto->data->management);
        $this->assertInstanceOf(PartyNameDto::class, $dto->data->name);
        $this->assertInstanceOf(PartyOpfDto::class, $dto->data->opf);
        $this->assertInstanceOf(AddressValueDto::class, $dto->data->address);
    }
}

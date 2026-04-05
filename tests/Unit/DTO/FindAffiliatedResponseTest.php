<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\Tests\Unit\DTO;

use Ex3mm\Dadata\DTO\Response\Shared\AddressValueDto;
use Ex3mm\Dadata\DTO\Response\Shared\Party\AffiliatedPartyDataDto;
use Ex3mm\Dadata\DTO\Response\Shared\Party\AffiliatedPartyResponseDto;
use Ex3mm\Dadata\DTO\Response\Shared\Party\PartyStateDto;
use Ex3mm\Dadata\Enums\PartyBranchType;
use Ex3mm\Dadata\Enums\PartyStateStatus;
use Ex3mm\Dadata\Enums\PartyType;
use Ex3mm\Dadata\Tests\TestCase;

final class FindAffiliatedResponseTest extends TestCase
{
    public function test_parses_affiliated_party_from_fixture(): void
    {
        $fixtureJson = file_get_contents(__DIR__ . '/../../fixtures/find_affiliated_response.json');
        $this->assertNotFalse($fixtureJson);
        $fixture = json_decode($fixtureJson, true);
        $this->assertIsArray($fixture);

        $item = $fixture['suggestions'][0];
        $dto  = AffiliatedPartyResponseDto::fromArray($item);

        $this->assertInstanceOf(AffiliatedPartyResponseDto::class, $dto);
        $this->assertSame((string) $item['value'], $dto->value);
        $this->assertSame((string) $item['unrestricted_value'], $dto->unrestrictedValue);
        $this->assertInstanceOf(AffiliatedPartyDataDto::class, $dto->data);
    }

    public function test_affiliated_party_data_fields_are_mapped(): void
    {
        $fixtureJson = file_get_contents(__DIR__ . '/../../fixtures/find_affiliated_response.json');
        $fixture     = json_decode((string) $fixtureJson, true);
        $item        = $fixture['suggestions'][0];

        $dto          = AffiliatedPartyResponseDto::fromArray($item);
        $expectedData = $item['data'];

        $this->assertSame($expectedData['inn'] ?? null, $dto->data->inn);
        $this->assertSame($expectedData['kpp'] ?? null, $dto->data->kpp);
        $this->assertSame($expectedData['ogrn'] ?? null, $dto->data->ogrn);
        $this->assertSame($expectedData['hid'] ?? null, $dto->data->hid);
        $this->assertSame(
            isset($expectedData['type']) && is_string($expectedData['type']) ? PartyType::tryFrom($expectedData['type']) : null,
            $dto->data->type
        );
        $this->assertSame($expectedData['branch_count'] ?? null, $dto->data->branchCount);
        $this->assertSame(
            isset($expectedData['branch_type']) && is_string($expectedData['branch_type']) ? PartyBranchType::tryFrom($expectedData['branch_type']) : null,
            $dto->data->branchType
        );
        $this->assertInstanceOf(AddressValueDto::class, $dto->data->address);
        $this->assertSame($expectedData['address']['value'] ?? null, $dto->data->address?->value);
        $this->assertInstanceOf(PartyStateDto::class, $dto->data->state);
        $this->assertSame(
            isset($expectedData['state']['status']) && is_string($expectedData['state']['status']) ? PartyStateStatus::tryFrom($expectedData['state']['status']) : null,
            $dto->data->state?->status
        );
    }
}

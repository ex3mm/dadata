<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\Tests\Unit\Enums;

use Ex3mm\Dadata\Enums\AddressBound;
use Ex3mm\Dadata\Enums\Language;
use Ex3mm\Dadata\Enums\PartyStatus;
use Ex3mm\Dadata\Enums\PartyType;
use Ex3mm\Dadata\Tests\TestCase;

final class EnumsTest extends TestCase
{
    public function test_address_bound_values_match_dadata_api(): void
    {
        $this->assertSame('country', AddressBound::COUNTRY->value);
        $this->assertSame('region', AddressBound::REGION->value);
        $this->assertSame('area', AddressBound::AREA->value);
        $this->assertSame('city', AddressBound::CITY->value);
        $this->assertSame('city_district', AddressBound::CITY_DISTRICT->value);
        $this->assertSame('settlement', AddressBound::SETTLEMENT->value);
        $this->assertSame('street', AddressBound::STREET->value);
        $this->assertSame('house', AddressBound::HOUSE->value);
        $this->assertSame('flat', AddressBound::FLAT->value);
    }

    public function test_language_values_match_dadata_api(): void
    {
        $this->assertSame('ru', Language::RU->value);
        $this->assertSame('en', Language::EN->value);
    }

    public function test_party_type_values_match_dadata_api(): void
    {
        $this->assertSame('LEGAL', PartyType::LEGAL->value);
        $this->assertSame('INDIVIDUAL', PartyType::INDIVIDUAL->value);
    }

    public function test_party_status_values_match_dadata_api(): void
    {
        $this->assertSame('ACTIVE', PartyStatus::ACTIVE->value);
        $this->assertSame('LIQUIDATING', PartyStatus::LIQUIDATING->value);
        $this->assertSame('LIQUIDATED', PartyStatus::LIQUIDATED->value);
        $this->assertSame('BANKRUPT', PartyStatus::BANKRUPT->value);
        $this->assertSame('REORGANIZING', PartyStatus::REORGANIZING->value);
    }
}

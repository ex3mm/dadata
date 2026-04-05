<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\Enums;

/**
 * Тип подразделения организации.
 */
enum PartyBranchType: string
{
    /** Головная организация */
    case MAIN = 'MAIN';
    /** Филиал */
    case BRANCH = 'BRANCH';
}

<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\Enums;

/**
 * Статус организации.
 */
enum PartyStatus: string
{
    /** Действующая */
    case ACTIVE = 'ACTIVE';

    /** Ликвидируется */
    case LIQUIDATING = 'LIQUIDATING';

    /** Ликвидирована */
    case LIQUIDATED = 'LIQUIDATED';

    /** Банкротство */
    case BANKRUPT = 'BANKRUPT';

    /** Реорганизуется */
    case REORGANIZING = 'REORGANIZING';
}

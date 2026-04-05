<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\Enums;

/**
 * Статус организации в ответе findAffiliated.
 */
enum PartyStateStatus: string
{
    /** Действующая организация */
    case ACTIVE = 'ACTIVE';
    /** Организация в процессе ликвидации */
    case LIQUIDATING = 'LIQUIDATING';
    /** Ликвидированная организация */
    case LIQUIDATED = 'LIQUIDATED';
    /** Организация в процедуре банкротства */
    case BANKRUPT = 'BANKRUPT';
    /** Организация в процессе реорганизации */
    case REORGANIZING = 'REORGANIZING';
}

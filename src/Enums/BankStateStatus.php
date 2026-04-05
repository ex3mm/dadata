<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\Enums;

/**
 * Статус состояния банка в DaData.
 */
enum BankStateStatus: string
{
    /** Действующая организация */
    case ACTIVE = 'ACTIVE';
    /** Организация в процессе ликвидации */
    case LIQUIDATING = 'LIQUIDATING';
    /** Ликвидированная организация */
    case LIQUIDATED = 'LIQUIDATED';
}

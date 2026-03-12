<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\Enums;

/**
 * Тип организации.
 */
enum PartyType: string
{
    /** Юридическое лицо */
    case LEGAL = 'LEGAL';

    /** Индивидуальный предприниматель */
    case INDIVIDUAL = 'INDIVIDUAL';
}

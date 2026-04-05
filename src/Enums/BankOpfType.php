<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\Enums;

/**
 * Тип кредитной организации в DaData.
 */
enum BankOpfType: string
{
    /** Банк */
    case BANK = 'BANK';
    /** Филиал банка */
    case BANK_BRANCH = 'BANK_BRANCH';
    /** Небанковская кредитная организация */
    case NKO = 'NKO';
    /** Филиал небанковской кредитной организации */
    case NKO_BRANCH = 'NKO_BRANCH';
    /** Расчетно-кассовый центр */
    case RKC = 'RKC';
    /** Управление ЦБ РФ */
    case CBR = 'CBR';
    /** Управление Казначейства */
    case TREASURY = 'TREASURY';
    /** Другой тип кредитной организации */
    case OTHER = 'OTHER';
}

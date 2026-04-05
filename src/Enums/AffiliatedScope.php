<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\Enums;

/**
 * Область поиска аффилированных компаний.
 */
enum AffiliatedScope: string
{
    /** Искать среди учредителей */
    case FOUNDERS = 'FOUNDERS';

    /** Искать среди руководителей */
    case MANAGERS = 'MANAGERS';
}

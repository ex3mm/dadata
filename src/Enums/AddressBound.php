<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\Enums;

/**
 * Уровни детализации адреса для фильтрации подсказок.
 */
enum AddressBound: string
{
    /** Страна */
    case COUNTRY = 'country';

    /** Регион */
    case REGION = 'region';

    /** Район */
    case AREA = 'area';

    /** Город */
    case CITY = 'city';

    /** Район города */
    case CITY_DISTRICT = 'city_district';

    /** Населённый пункт */
    case SETTLEMENT = 'settlement';

    /** Улица */
    case STREET = 'street';

    /** Дом */
    case HOUSE = 'house';

    /** Квартира */
    case FLAT = 'flat';
}

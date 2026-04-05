<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\Enums;

enum AddressFiasLevel: int
{
    /** Уровень не определён */
    case UNKNOWN = -1;
    /** Страна */
    case COUNTRY = 0;
    /** Регион */
    case REGION = 1;
    /** Район */
    case AREA = 3;
    /** Город */
    case CITY = 4;
    /** Район города */
    case CITY_DISTRICT = 5;
    /** Населённый пункт */
    case SETTLEMENT = 6;
    /** Улица */
    case STREET = 7;
    /** Дом */
    case HOUSE = 8;
    /** Квартира */
    case FLAT = 9;
}

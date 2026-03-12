<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\Enums;

enum AddressFiasLevel: int
{
    case UNKNOWN       = -1;
    case COUNTRY       = 0;
    case REGION        = 1;
    case AREA          = 3;
    case CITY          = 4;
    case CITY_DISTRICT = 5;
    case SETTLEMENT    = 6;
    case STREET        = 7;
    case HOUSE         = 8;
    case FLAT          = 9;
}

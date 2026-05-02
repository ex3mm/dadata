<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\Enums;

/** Пол */
enum Gender: string
{
    case MALE    = 'MALE';
    case FEMALE  = 'FEMALE';
    case UNKNOWN = 'UNKNOWN';
}

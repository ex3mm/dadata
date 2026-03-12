<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\Enums;

/**
 * Язык для подсказок адресов.
 */
enum Language: string
{
    /** Русский */
    case RU = 'ru';

    /** Английский */
    case EN = 'en';
}

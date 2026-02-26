<?php

declare(strict_types=1);

namespace App\Enum;

enum RegionalEnum: string
{
    case REGIONAL_LOCALE = 'en';
    case REGIONAL_CURRENCY = 'CZK';
    case REGIONAL_REGION = 'cz';
    case REGIONAL_TIMEZONE = 'Europe/Prague';
    case REGIONAL_TIMEZONE_OFFSET = '60';
}

<?php

declare(strict_types=1);

namespace App\Enum;

enum RegionalEnum: string
{
    case REGIONAL_LOCALE = 'en';
    case REGIONAL_CURRECNY = 'czk';
    case REGIONAL_REGION = 'cz';
    case REGIONAL_TIMEZONE = 'europe/prague';
    case REGIONAL_TIMEZONE_OFFSET = '0';
}

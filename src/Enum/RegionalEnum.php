<?php

declare(strict_types=1);

namespace App\Enum;

enum RegionalEnum: string
{
    case REGIONAL_LOCALE = 'en';
    case REGIONAL_CURRECNY = 'CZK';
    case REGIONAL_REGION = 'CZE';
    case REGIONAL_TIMEZONE = 'Europe/Prague';
}

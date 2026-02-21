<?php

declare(strict_types=1);

namespace App\Enum;

enum CurrencyCodeEnum: string
{
    case CZK = 'CZK';
    case EUR = 'EUR';
    case USD = 'USD';
    case GBP = 'GBP';
}

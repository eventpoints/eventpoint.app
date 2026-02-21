<?php

declare(strict_types=1);

namespace App\Enum;

enum MerchantTypeEnum: string
{
    case INDIVIDUAL = 'individual';
    case BUSINESS = 'business';
}

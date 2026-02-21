<?php

declare(strict_types=1);

namespace App\Enum;

enum SellerTypeEnum: string
{
    case TRADER = 'trader';
    case PRIVATE_INDIVIDUAL = 'private_individual';
}

<?php

declare(strict_types=1);

namespace App\Enum;

enum RefundPolicyTypeEnum: string
{
    case NO_REFUNDS = 'no_refunds';
    case UNTIL_DAYS_BEFORE = 'until_days_before';
    case UNTIL_START = 'until_start';
    case CUSTOM = 'custom';
}

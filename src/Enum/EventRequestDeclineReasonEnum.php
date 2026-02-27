<?php

declare(strict_types=1);

namespace App\Enum;

enum EventRequestDeclineReasonEnum: string
{
    case FULL = 'full';
    case NOT_THIS_TIME = 'not_this_time';
    case NOT_A_GOOD_FIT = 'not_a_good_fit';
}

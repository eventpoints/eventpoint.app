<?php

declare(strict_types=1);

namespace App\Enum;

enum EventFilterDateRangeEnum: string
{
    case RECENTLY = 'recently';
    case TODAY = 'today';
    case TOMORROW = 'tomorrow';
    case THIS_WEEK = 'this-week';
    case THIS_WEEKEND = 'this-weekend';
    case NEXT_WEEK = 'next-week';
    case NEXT_MONTH = 'next-month';
}

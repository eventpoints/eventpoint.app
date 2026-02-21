<?php

declare(strict_types=1);

namespace App\Enum;

use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

enum EventFilterDateRangeEnum: string implements TranslatableInterface
{
    case RECENTLY = 'recently';
    case IN_PROGRESS = 'in-progress';
    case TODAY = 'today';
    case TOMORROW = 'tomorrow';
    case THIS_WEEK = 'this-week';
    case THIS_WEEKEND = 'this-weekend';
    case NEXT_WEEK = 'next-week';
    case NEXT_MONTH = 'next-month';

    public function trans(TranslatorInterface $translator, ?string $locale = null): string
    {
        return $translator->trans('event-filter-date-range.' . $this->value, locale: $locale);
    }
}

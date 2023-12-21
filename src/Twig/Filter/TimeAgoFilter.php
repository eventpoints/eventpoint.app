<?php

declare(strict_types=1);

namespace App\Twig\Filter;

use Carbon\CarbonImmutable;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class TimeAgoFilter extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('time_ago', fn (CarbonImmutable $date): string => $this->getTimeAgo($date)),
        ];
    }

    public function getTimeAgo(CarbonImmutable $date): string
    {
        return $date->diffForHumans();
    }
}

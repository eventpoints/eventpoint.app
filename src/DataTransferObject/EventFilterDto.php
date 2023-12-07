<?php

declare(strict_types=1);

namespace App\DataTransferObject;

use App\Entity\Category;
use App\Enum\EventFilterDateRangeEnum;

class EventFilterDto
{
    private null|string $title = null;

    private null|EventFilterDateRangeEnum $period = EventFilterDateRangeEnum::TODAY;

    private null|Category $category = null;

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    public function getPeriod(): null|EventFilterDateRangeEnum
    {
        return $this->period;
    }

    public function setPeriod(null|EventFilterDateRangeEnum $period): void
    {
        $this->period = $period;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): void
    {
        $this->category = $category;
    }
}

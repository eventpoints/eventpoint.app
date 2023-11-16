<?php

namespace App\DataTransferObject;

use App\Entity\Category;

class EventFilterDto
{
    private null|string $title = null;
    private null|string $period = 'today';
    private null|Category $category = null;
    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    public function getPeriod(): ?string
    {
        return $this->period;
    }

    public function setPeriod(?string $period): void
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
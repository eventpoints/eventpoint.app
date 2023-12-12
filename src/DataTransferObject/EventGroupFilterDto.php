<?php

declare(strict_types=1);

namespace App\DataTransferObject;

use App\Entity\Category;

final class EventGroupFilterDto
{
    private null|string $keyword = null;

    private null|Category $category = null;

    public function getKeyword(): ?string
    {
        return $this->keyword;
    }

    public function setKeyword(?string $keyword): void
    {
        $this->keyword = $keyword;
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

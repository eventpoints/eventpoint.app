<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Category;

final class CategoryFactory
{
    public function create(null|string $title, null|Category $parent = null): Category
    {
        $category = new Category();
        $category->setTitle($title);
        if ($parent instanceof Category) {
            $category->addCategory($parent);
        }
        return $category;
    }
}

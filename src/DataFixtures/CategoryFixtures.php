<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Data\Categories;
use App\Factory\CategoryFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    public function __construct(
        private readonly CategoryFactory $categoryFactory,
        private readonly Categories $categories,
    ) {
    }

    #[\Override]
    public function load(ObjectManager $manager): void
    {
        foreach ($this->categories->getCategories() as $title) {
            $category = $this->categoryFactory->create(title: $title);
            $manager->persist($category);
        }
        $manager->flush();
    }
}

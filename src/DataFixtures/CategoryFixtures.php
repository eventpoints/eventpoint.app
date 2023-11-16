<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        foreach ($this->getAllCategories() as $cat) {
            $category = new Category();
            $category->setTitle($cat);
            $manager->persist($category);
        }
        $manager->flush();
    }

    /**
     * @return string[]
     */
    public function getAllCategories(): array
    {
        return [
            'category.art',
            'category.celebration',
            'category.comedy',
            'category.concert',
            'category.dinner',
            'category.educational',
            'category.exhibition',
            'category.family',
            'category.festival',
            'category.food-and-drink',
            'category.fundraising',
            'category.gaming',
            'category.health-and-wellness',
            'category.hobby',
            'category.holiday',
            'category.live-performance',
            'category.business-networking',
            'category.outdoor',
            'category.party',
            'category.social',
            'category.sports',
            'category.technology',
            'category.travel',
            'category.workshop',
        ];
    }
}

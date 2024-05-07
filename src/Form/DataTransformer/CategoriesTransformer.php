<?php

namespace App\Form\DataTransformer;

use App\Entity\Event\Category;
use App\Repository\Event\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

readonly class CategoriesTransformer implements DataTransformerInterface
{
    public function __construct(
        private CategoryRepository $categoryRepository
    ) {
    }

    /**
     * @return string[]
     */
    public function transform($value): array
    {
        $ids = [];
        /** @var Category $category */
        foreach ($value as $category) {
            $ids[] = $category->getId()->toRfc4122();
        }

        return $ids;
    }

    /**
     * @throws NonUniqueResultException
     */
    public function reverseTransform(mixed $value): mixed
    {
        $categories = [];

        /** @var Category $category */
        foreach ($value as $category) {
            $category = $this->categoryRepository->find($category->getId());
            if (! $category) {
                throw new TransformationFailedException(sprintf(
                    'Category with ID "%s" does not exist!',
                    $category
                ));
            }
            $categories[] = $category;
        }

        return new ArrayCollection($categories);
    }
}

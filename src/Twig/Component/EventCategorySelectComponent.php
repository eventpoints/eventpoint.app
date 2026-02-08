<?php

declare(strict_types=1);

namespace App\Twig\Component;

use App\DataTransferObject\Event\EventDto;
use App\Entity\Event\Category;
use App\Repository\Event\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent('event_category_select', defaultAction: 'search')]
class EventCategorySelectComponent extends AbstractController
{
    use DefaultActionTrait;

    #[LiveProp]
    public EventDto $eventDto;

    #[LiveProp(writable: true)]
    public string $search = '';

    /** @var Category[] */
    public array $searchResults = [];

    public function __construct(
        private readonly CategoryRepository $categoryRepository,
    ) {
    }

    public function mount(EventDto $eventDto): void
    {
        $this->eventDto = $eventDto;
    }

    #[LiveAction]
    public function search(): void
    {
        if (empty($this->search)) {
            $this->searchResults = [];
            return;
        }

        $this->searchResults = $this->categoryRepository->findByTitleSearch($this->search);
    }

    #[LiveAction]
    public function addCategory(Category $category): void
    {
        $this->eventDto->addCategory($category);
        $this->search = '';
        $this->searchResults = [];
    }

    #[LiveAction]
    public function removeCategory(Category $category): void
    {
        $this->eventDto->removeCategory($category);
    }

    public function getSelectedCategories(): array
    {
        return $this->eventDto->getCategories()->toArray();
    }

    public function isSelected(Category $category): bool
    {
        return $this->eventDto->getCategories()->contains($category);
    }
}

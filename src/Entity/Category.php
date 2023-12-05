<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Contract\StaticEntityInterface;
use App\Entity\Event\Event;
use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Stringable;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category implements Stringable, StaticEntityInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\CustomIdGenerator(UuidGenerator::class)]
    private Uuid $id;

    #[ORM\Column(length: 255)]
    private null|string $title = null;

    #[ORM\ManyToMany(targetEntity: self::class, inversedBy: 'subcategories')]
    private Collection $categories;

    #[ORM\ManyToMany(targetEntity: self::class, mappedBy: 'categories', cascade: ['persist'])]
    private Collection $subcategories;

    #[ORM\ManyToMany(targetEntity: Event::class, mappedBy: 'categories')]
    private Collection $events;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
        $this->subcategories = new ArrayCollection();
        $this->events = new ArrayCollection();
    }

    public function __toString(): string
    {
        return (string) $this->getTitle();
    }

    public function getId(): null|Uuid
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getSubcategories(): Collection
    {
        return $this->subcategories;
    }

    public function addSubcategory(self $subcategory): static
    {
        if (! $this->subcategories->contains($subcategory)) {
            $this->subcategories->add($subcategory);
            $subcategory->addCategory($this);
        }

        return $this;
    }

    public function removeSubcategory(self $subcategory): static
    {
        if ($this->subcategories->removeElement($subcategory)) {
            $subcategory->removeCategory($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(self $category): static
    {
        if (! $this->categories->contains($category)) {
            $this->categories->add($category);
            $category->addSubcategory($this); // Bidirectional: Add this category as a child for the parent category
        }

        return $this;
    }

    public function removeCategory(self $category): static
    {
        if ($this->categories->removeElement($category)) {
            $category->removeSubcategory($this); // Bidirectional: Remove this category as a child for the parent category
        }

        return $this;
    }

    /**
     * @return Collection<int, Event>
     */
    public function getEvents(): Collection
    {
        return $this->events;
    }

    public function addEvent(Event $event): static
    {
        if (! $this->events->contains($event)) {
            $this->events->add($event);
            $event->addCategory($this);
        }

        return $this;
    }

    public function removeEvent(Event $event): static
    {
        if ($this->events->removeElement($event)) {
            $event->removeCategory($this);
        }

        return $this;
    }
}

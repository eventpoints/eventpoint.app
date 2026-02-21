<?php

declare(strict_types=1);

namespace App\DataTransferObject;

use App\Entity\City;
use App\Entity\Country;
use App\Entity\Event\Category;
use App\Enum\EventFilterDateRangeEnum;
use Doctrine\Common\Collections\ArrayCollection;

class EventFilterDto
{
    private null|string $keyword = null;

    private null|EventFilterDateRangeEnum $period = EventFilterDateRangeEnum::TODAY;

    private ArrayCollection $categories;

    private null|City $city = null;

    private null|Country $country = null;

    private null|int $radius = null;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
    }

    public function getKeyword(): ?string
    {
        return $this->keyword;
    }

    public function setKeyword(?string $keyword): void
    {
        $this->keyword = $keyword;
    }

    public function getPeriod(): null|EventFilterDateRangeEnum
    {
        return $this->period;
    }

    public function setPeriod(null|EventFilterDateRangeEnum $period): void
    {
        $this->period = $period;
    }

    /**
     * @return ArrayCollection<int, Category>
     */
    public function getCategories(): ArrayCollection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): void
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
        }
    }

    public function removeCategory(Category $category): void
    {
        if ($this->categories->contains($category)) {
            $this->categories->removeElement($category);
        }
    }

    public function getCity(): ?City
    {
        return $this->city;
    }

    public function setCity(?City $city): void
    {
        $this->city = $city;
    }

    public function getCountry(): ?Country
    {
        return $this->country;
    }

    public function setCountry(?Country $country): void
    {
        $this->country = $country;
    }

    public function getRadius(): ?int
    {
        return $this->radius;
    }

    public function setRadius(?int $radius): void
    {
        $this->radius = $radius;
    }
}

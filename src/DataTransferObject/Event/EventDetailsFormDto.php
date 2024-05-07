<?php

namespace App\DataTransferObject\Event;

use App\Entity\Event\Category;
use App\Entity\EventGroup\EventGroup;
use Carbon\CarbonImmutable;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;

final class EventDetailsFormDto
{
    private null|string $title = null;

    private null|CarbonImmutable|DateTimeImmutable $startAt = null;

    private null|CarbonImmutable|DateTimeImmutable $endAt = null;

    private null|string $description = null;

    private string|null $base64image = null;

    private bool $isPrivate = false;

    private null|EventGroup $eventGroup = null;

    private CarbonImmutable $createdAt;

    private ArrayCollection $categories;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
        $this->createdAt = new CarbonImmutable();
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    public function getStartAt(): DateTimeImmutable|CarbonImmutable|null
    {
        return $this->startAt;
    }

    public function setStartAt(DateTimeImmutable|CarbonImmutable|null $startAt): void
    {
        $this->startAt = $startAt;
    }

    public function getEndAt(): DateTimeImmutable|CarbonImmutable|null
    {
        return $this->endAt;
    }

    public function setEndAt(DateTimeImmutable|CarbonImmutable|null $endAt): void
    {
        $this->endAt = $endAt;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function isPrivate(): bool
    {
        return $this->isPrivate;
    }

    public function setIsPrivate(bool $isPrivate): void
    {
        $this->isPrivate = $isPrivate;
    }

    public function getEventGroup(): ?EventGroup
    {
        return $this->eventGroup;
    }

    public function setEventGroup(?EventGroup $eventGroup): void
    {
        $this->eventGroup = $eventGroup;
    }

    public function getBase64image(): ?string
    {
        return $this->base64image;
    }

    public function setBase64image(?string $base64image): void
    {
        $this->base64image = $base64image;
    }

    public function getCreatedAt(): CarbonImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(CarbonImmutable $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getCategories(): ArrayCollection
    {
        return $this->categories;
    }

    public function setCategories(ArrayCollection $categories): void
    {
        $this->categories = $categories;
    }

}

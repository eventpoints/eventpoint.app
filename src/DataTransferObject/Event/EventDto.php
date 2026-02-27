<?php

declare(strict_types=1);

namespace App\DataTransferObject\Event;

use App\Entity\Event\Category;
use App\Entity\EventGroup\EventGroup;
use App\Entity\User\User;
use Carbon\CarbonImmutable;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class EventDto
{
    #[Assert\Count(min: 1, max: 4, minMessage: 'event.categories.min', maxMessage: 'event.categories.max')]
    private readonly Collection $categories;

    private null|CarbonImmutable $createdAt;

    public function __construct(
        private null|string $email = null,
        private null|string $firstName = null,
        private null|string $lastName = null,
        private null|string $title = null,
        private null|CarbonImmutable $startAt = null,
        private null|CarbonImmutable $endAt = null,
        private null|string $description = null,
        private null|string $latitude = null,
        private null|string $longitude = null,
        private null|bool $isPrivate = false,
        private null|string $address = null,
        private null|User $owner = null,
        private null|EventGroup $eventGroup = null,
    ) {
        $this->categories = new ArrayCollection();
        $this->createdAt = new CarbonImmutable();
    }

    #[Assert\Callback]
    public function validate(ExecutionContextInterface $context): void
    {
        // Validate start date is not in the past
        if ($this->startAt !== null) {
            $now = new CarbonImmutable();
            if ($this->startAt->lessThan($now)) {
                $context->buildViolation('event.start_date_must_be_in_future')
                    ->atPath('startAt')
                    ->addViolation();
            }
        }

        // Validate end date is after start date
        if ($this->startAt !== null && $this->endAt !== null) {
            if ($this->endAt->lessThanOrEqualTo($this->startAt)) {
                $context->buildViolation('event.end_date_must_be_after_start_date')
                    ->atPath('endAt')
                    ->addViolation();
            }
        }
    }

    public function getTitle(): null|string
    {
        return $this->title;
    }

    public function setTitle(null|string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getStartAt(): null|CarbonImmutable
    {
        return $this->startAt;
    }

    public function setStartAt(null|DateTimeImmutable|CarbonImmutable $startAt): static
    {
        $startAt instanceof DateTimeImmutable ? $this->startAt = CarbonImmutable::parse($startAt->format('Y-m-d H:i:s')) : $this->startAt = $startAt;
        return $this;
    }

    public function getEndAt(): null|CarbonImmutable
    {
        return $this->endAt;
    }

    public function setEndAt(null|DateTimeImmutable|CarbonImmutable $endAt): static
    {
        $endAt instanceof DateTimeImmutable ? $this->endAt = CarbonImmutable::parse($endAt->format('Y-m-d H:i:s')) : $this->endAt = $endAt;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection<int, Category>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): static
    {
        if (! $this->categories->contains($category)) {
            $this->categories->add($category);
        }

        return $this;
    }

    public function removeCategory(Category $category): static
    {
        $this->categories->removeElement($category);

        return $this;
    }

    public function IsPrivate(): null|bool
    {
        return $this->isPrivate;
    }

    public function setIsPrivate(null|bool $isPrivate): static
    {
        $this->isPrivate = $isPrivate;

        return $this;
    }

    public function getLatitude(): null|string
    {
        return $this->latitude;
    }

    public function setLatitude(null|string $latitude): void
    {
        $this->latitude = $latitude;
    }

    public function getLongitude(): null|string
    {
        return $this->longitude;
    }

    public function setLongitude(null|string $longitude): void
    {
        $this->longitude = $longitude;
    }

    public function getEventGroup(): null|EventGroup
    {
        return $this->eventGroup;
    }

    public function setEventGroup(null|EventGroup $eventGroup): static
    {
        $this->eventGroup = $eventGroup;

        return $this;
    }

    public function getAddress(): null|string
    {
        return $this->address;
    }

    public function setAddress(null|string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getCreatedAt(): CarbonImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(CarbonImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): static
    {
        $this->owner = $owner;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): void
    {
        $this->firstName = $firstName;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): void
    {
        $this->lastName = $lastName;
    }
}

<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Event\Event;
use App\Repository\EventReviewRepository;
use Carbon\CarbonImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: EventReviewRepository::class)]
class EventReview
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\CustomIdGenerator(UuidGenerator::class)]
    private Uuid $id;

    #[ORM\ManyToOne(inversedBy: 'eventReviews')]
    private ?Event $event = null;

    #[ORM\ManyToOne(inversedBy: 'eventReviews')]
    private ?User $owner = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private CarbonImmutable|null $createdAt = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 2, scale: 1)]
    private ?string $venueRating = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 2, scale: 1)]
    private ?string $contentRating = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 2, scale: 1)]
    private ?string $hostRating = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 2, scale: 1)]
    private ?string $guestRating = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 2, scale: 1)]
    private ?string $expectationRating = null;

    public function __construct()
    {
        $this->createdAt = new CarbonImmutable();
    }

    public function getId(): null|Uuid
    {
        return $this->id;
    }

    public function getEvent(): ?Event
    {
        return $this->event;
    }

    public function setEvent(?Event $event): static
    {
        $this->event = $event;

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

    public function getCreatedAt(): null|CarbonImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(null|CarbonImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getVenueRating(): ?string
    {
        return $this->venueRating;
    }

    public function setVenueRating(string $venueRating): static
    {
        $this->venueRating = $venueRating;

        return $this;
    }

    public function getContentRating(): ?string
    {
        return $this->contentRating;
    }

    public function setContentRating(string $contentRating): static
    {
        $this->contentRating = $contentRating;

        return $this;
    }

    public function getHostRating(): ?string
    {
        return $this->hostRating;
    }

    public function setHostRating(string $hostRating): static
    {
        $this->hostRating = $hostRating;

        return $this;
    }

    public function getGuestRating(): ?string
    {
        return $this->guestRating;
    }

    public function setGuestRating(string $guestRating): static
    {
        $this->guestRating = $guestRating;

        return $this;
    }

    public function getExpectationRating(): ?string
    {
        return $this->expectationRating;
    }

    public function setExpectationRating(string $expectationRating): static
    {
        $this->expectationRating = $expectationRating;

        return $this;
    }
}

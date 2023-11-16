<?php

namespace App\Entity\Event;

use App\Entity\Category;
use App\Entity\EventGroup\EventGroup;
use App\Entity\Image;
use App\Entity\ImageCollection;
use App\Entity\User;
use App\Repository\Event\EventRepository;
use Carbon\CarbonImmutable;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: EventRepository::class)]
class Event
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\CustomIdGenerator(UuidGenerator::class)]
    private Uuid $id;

    #[ORM\Column(length: 255)]
    private null|string $title = null;

    #[ORM\Column]
    private null|DateTimeImmutable $startAt = null;

    #[ORM\Column(nullable: true)]
    private null|DateTimeImmutable $endAt = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private null|string $description = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 7, nullable: true)]
    private string $latitude;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 7, nullable: true)]
    private string $longitude;

    #[ORM\Column]
    private null|bool $isOnline = false;

    #[ORM\ManyToMany(targetEntity: Category::class, inversedBy: 'events')]
    private Collection $categories;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $base64Image = null;

    /**
     * @var Collection<int, EventParticipant>
     */
    #[ORM\OneToMany(mappedBy: 'event', targetEntity: EventParticipant::class, cascade: ['persist', 'remove'])]
    private Collection $eventParticipants;

    /**
     * @var Collection<int, EventInvitation>
     */
    #[ORM\OneToMany(mappedBy: 'event', targetEntity: EventInvitation::class, cascade: ['persist', 'remove'])]
    private Collection $eventInvitations;

    /**
     * @var Collection<int, EventRequest>
     */
    #[ORM\OneToMany(mappedBy: 'event', targetEntity: EventRequest::class, cascade: ['persist', 'remove'])]
    private Collection $eventRequests;

    /**
     * @var Collection<int, EventRejection>
     */
    #[ORM\OneToMany(mappedBy: 'event', targetEntity: EventRejection::class, cascade: ['persist', 'remove'])]
    private Collection $eventRejections;

    #[ORM\OneToMany(mappedBy: 'event', targetEntity: Image::class, cascade: ['persist', 'remove'])]
    private Collection $images;

    #[ORM\OneToMany(mappedBy: 'event', targetEntity: EventOrganiser::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $eventOrganisers;

    #[ORM\Column]
    private ?bool $isPrivate = false;

    #[ORM\OneToMany(mappedBy: 'event', targetEntity: ImageCollection::class)]
    #[ORM\OrderBy(['createdAt' => Criteria::DESC])]
    private Collection $imageCollections;

    #[ORM\OneToMany(mappedBy: 'event', targetEntity: EventEmailInvitation::class, cascade: ['persist', 'remove'])]
    private Collection $eventEmailInvitations;

    #[ORM\ManyToOne(inversedBy: 'events')]
    private null|EventGroup $eventGroup = null;

    #[ORM\Column(length: 255)]
    private ?string $address = null;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
        $this->images = new ArrayCollection();
        $this->eventOrganisers = new ArrayCollection();
        $this->imageCollections = new ArrayCollection();
        $this->eventEmailInvitations = new ArrayCollection();
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

    public function getStartAt(): null|DateTimeImmutable|CarbonImmutable
    {
        return $this->startAt;
    }

    public function setStartAt(DateTimeImmutable $startAt): static
    {
        $this->startAt = $startAt;

        return $this;
    }

    public function getEndAt(): null|DateTimeImmutable|CarbonImmutable
    {
        return $this->endAt;
    }

    public function setEndAt(?DateTimeImmutable $endAt): static
    {
        $this->endAt = $endAt;

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

    public function isIsOnline(): ?bool
    {
        return $this->isOnline;
    }

    public function setIsOnline(bool $isOnline): static
    {
        $this->isOnline = $isOnline;

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
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
        }

        return $this;
    }

    public function removeCategory(Category $category): static
    {
        $this->categories->removeElement($category);

        return $this;
    }

    public function getInterval(): string
    {
        return $this->getStartAt()->diffAsCarbonInterval($this->getEndAt())->forHumans(short: true);
    }

    public function getDurationInHours(): float
    {
        return round($this->getStartAt()->diffInRealHours($this->getEndAt()) / 60, 2);
    }

    public function getElapsedTimeInMinutes(): int
    {
        return CarbonImmutable::now()->diffInRealMinutes($this->getStartAt());
    }

    public function getElapsedTimeAsInterval(): string
    {
        return CarbonImmutable::now()->diffAsCarbonInterval($this->getStartAt())->forHumans(short: true);
    }

    public function getTimeRemainingAsInterval(): string
    {
        return CarbonImmutable::now()->diffAsCarbonInterval($this->getEndAt())->forHumans(short: true);
    }

    public function getDurationInMinutes(): int
    {
        return $this->getStartAt()->diffInRealMinutes($this->getEndAt());
    }

    public function getDurationInHour(): float
    {
        return round($this->getStartAt()->diffInRealMinutes($this->getEndAt()) / 60, 2);
    }

    public function isPendingStart(): bool
    {
        return CarbonImmutable::now()->isBefore($this->getStartAt());
    }

    public function isInProgress(): bool
    {
        return CarbonImmutable::now()->isAfter($this->getStartAt()) && CarbonImmutable::now()->isBefore($this->getEndAt());
    }

    public function isComplete(): bool
    {
        return CarbonImmutable::now()->isAfter($this->getEndAt());
    }

    public function getBase64Image(): ?string
    {
        return $this->base64Image;
    }

    public function setBase64Image(?string $base64Image): static
    {
        $this->base64Image = $base64Image;

        return $this;
    }

    /**
     * @return Collection<int, EventRequest>
     */
    public function getEventRequests(): Collection
    {
        return $this->eventRequests;
    }

    /**
     * @return Collection<int, EventParticipant>
     */
    public function getEventParticipants(): Collection
    {
        return $this->eventParticipants;
    }

    public function addEventParticipant(EventParticipant $user): self
    {
        if (!$this->eventParticipants->contains($user)) {
            $this->eventParticipants->add($user);
            $user->setEvent($this);
        }

        return $this;
    }

    public function removeEventParticipant(EventParticipant $user): self
    {
        // set the owning side to null (unless already changed)
        $this->eventParticipants->removeElement($user);

        return $this;
    }

    public function addEventRequest(EventRequest $eventRequest): self
    {
        if (!$this->eventRequests->contains($eventRequest)) {
            $this->eventRequests->add($eventRequest);
            $eventRequest->setEvent($this);
        }

        return $this;
    }

    public function removeEventRequest(EventRequest $eventRequest): self
    {
        $this->eventRequests->removeElement($eventRequest);

        return $this;
    }

    /**
     * @return Collection<int, EventInvitation>
     */
    public function getEventInvitations(): Collection
    {
        return $this->eventInvitations;
    }

    public function addEventInvitation(EventInvitation $eventInvite): self
    {
        if (!$this->eventParticipants->exists(function (int $key, EventParticipant $eventParticipant) use ($eventInvite) {
                return $eventParticipant->getEvent() === $eventInvite->getEvent() && $eventParticipant->getOwner() === $eventInvite->getOwner();
            }) && !$this->eventInvitations->exists(function (int $key, EventInvitation $existingEventInvite) use ($eventInvite) {
                return $existingEventInvite->getEvent() === $eventInvite->getEvent() && $existingEventInvite->getOwner() === $eventInvite->getOwner();
            })) {
            $this->eventInvitations->add($eventInvite);
            $eventInvite->setEvent($this);
        }

        return $this;
    }

    public function removeEventInvitation(EventInvitation $eventInvite): self
    {
        // set the owning side to null (unless already changed)
        if ($eventInvite->getEvent() === $this) {
            $this->eventInvitations->removeElement($eventInvite);
        }

        return $this;
    }

    /**
     * @return Collection<int, EventRejection>
     */
    public function getEventRejections(): Collection
    {
        return $this->eventRejections;
    }

    public function addEventRejection(EventRejection $eventRejection): self
    {
        if (!$this->eventRejections->contains($eventRejection)) {
            $this->eventRejections->add($eventRejection);
            $eventRejection->setEvent($this);
        }

        return $this;
    }

    public function removeEventRejection(EventRejection $eventRejection): self
    {
        // set the owning side to null (unless already changed)
        if ($this->eventRejections->removeElement($eventRejection) && $eventRejection->getEvent() === $this) {
            $eventRejection->setEvent(null);
        }

        return $this;
    }

    /**
     * @return Collection<int, Image>
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Image $image): static
    {
        if (!$this->images->contains($image)) {
            $this->images->add($image);
            $image->setEvent($this);
        }

        return $this;
    }

    public function removeImage(Image $image): static
    {
        if ($this->images->removeElement($image)) {
            // set the owning side to null (unless already changed)
            if ($image->getEvent() === $this) {
                $image->setEvent(null);
            }
        }

        return $this;
    }

    public function isAfterStart(): bool
    {
        return CarbonImmutable::now()->isAfter($this->getStartAt());
    }

    /**
     * @return Collection<int, EventOrganiser>
     */
    public function getEventOrganisers(): Collection
    {
        return $this->eventOrganisers;
    }

    public function addEventOrganiser(EventOrganiser $eventOrganiser): static
    {
        if (!$this->eventOrganisers->contains($eventOrganiser)) {
            $this->eventOrganisers->add($eventOrganiser);
            $eventOrganiser->setEvent($this);
        }

        return $this;
    }

    public function removeEventOrganiser(EventOrganiser $eventOrganiser): static
    {
        if ($this->eventOrganisers->removeElement($eventOrganiser)) {
            // set the owning side to null (unless already changed)
            if ($eventOrganiser->getEvent() === $this) {
                $eventOrganiser->setEvent(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->getTitle();
    }

    public function isIsPrivate(): ?bool
    {
        return $this->isPrivate;
    }

    public function setIsPrivate(bool $isPrivate): static
    {
        $this->isPrivate = $isPrivate;

        return $this;
    }

    /**
     * @return Collection<int, ImageCollection>
     */
    public function getImageCollections(): Collection
    {
        return $this->imageCollections;
    }

    public function addImageCollection(ImageCollection $imageCollection): static
    {
        if (!$this->imageCollections->contains($imageCollection)) {
            $this->imageCollections->add($imageCollection);
            $imageCollection->setEvent($this);
        }

        return $this;
    }

    public function removeImageCollection(ImageCollection $imageCollection): static
    {
        if ($this->imageCollections->removeElement($imageCollection)) {
            // set the owning side to null (unless already changed)
            if ($imageCollection->getEvent() === $this) {
                $imageCollection->setEvent(null);
            }
        }

        return $this;
    }

    public function getLatitude(): string
    {
        return $this->latitude;
    }

    public function setLatitude(string $latitude): void
    {
        $this->latitude = $latitude;
    }

    public function getLongitude(): string
    {
        return $this->longitude;
    }

    public function setLongitude(string $longitude): void
    {
        $this->longitude = $longitude;
    }

    /**
     * @return Collection<int, EventEmailInvitation>
     */
    public function getEventEmailInvitations(): Collection
    {
        return $this->eventEmailInvitations;
    }

    public function addEmailInvitation(EventEmailInvitation $emailInvitation): static
    {
        if (!$this->eventEmailInvitations->contains($emailInvitation)) {
            $this->eventEmailInvitations->add($emailInvitation);
            $emailInvitation->setEvent($this);
        }

        return $this;
    }

    public function removeEmailInvitation(EventEmailInvitation $emailInvitation): static
    {
        if ($this->eventEmailInvitations->removeElement($emailInvitation)) {
            // set the owning side to null (unless already changed)
            if ($emailInvitation->getEvent() === $this) {
                $emailInvitation->setEvent(null);
            }
        }

        return $this;
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

    public function getTitleAndDate(): string
    {
        return $this->getTitle() . ' (' . $this->getStartAt()->rawFormat('j.m.y H:i') . ' - ' . $this->getEndAt()->rawFormat('j.m.y H:i') . ')';
    }

    public function getUnansweredInvitation(User $user) : null|EventInvitation
    {
        return $this->getEventInvitations()->findFirst(function (int $key, EventInvitation $eventInvitation) use ($user){
            return $eventInvitation->getOwner() === $user;
        });
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): static
    {
        $this->address = $address;

        return $this;
    }


}

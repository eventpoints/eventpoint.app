<?php

declare(strict_types=1);

namespace App\Entity\Event;

use App\Entity\EventGroup\EventGroup;
use App\Entity\Image\Image;
use App\Entity\Image\ImageCollection;
use App\Entity\User\User;
use App\Repository\Event\EventRepository;
use Carbon\CarbonImmutable;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Order;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Stringable;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: EventRepository::class)]
class Event implements Stringable
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\CustomIdGenerator(UuidGenerator::class)]
    private Uuid $id;

    #[ORM\ManyToMany(targetEntity: Category::class, inversedBy: 'events')]
    private Collection $categories;

    /**
     * @var Collection<int, EventParticipant> $eventParticipants
     */
    #[ORM\OneToMany(mappedBy: 'event', targetEntity: EventParticipant::class, cascade: ['persist', 'remove'])]
    private Collection $eventParticipants;

    /**
     * @var Collection<int, EventInvitation> $eventInvitations
     */
    #[ORM\OneToMany(mappedBy: 'event', targetEntity: EventInvitation::class, cascade: ['persist', 'remove'])]
    private Collection $eventInvitations;

    /**
     * @var Collection<int, EventRequest> $eventRequests
     */
    #[ORM\OneToMany(mappedBy: 'event', targetEntity: EventRequest::class, cascade: ['persist', 'remove'])]
    private Collection $eventRequests;

    /**
     * @var Collection<int, EventRejection> $eventRejections
     */
    #[ORM\OneToMany(mappedBy: 'event', targetEntity: EventRejection::class, cascade: ['persist', 'remove'])]
    private Collection $eventRejections;

    #[ORM\OneToMany(mappedBy: 'event', targetEntity: Image::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $images;

    #[ORM\OneToMany(mappedBy: 'event', targetEntity: EventOrganiser::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $eventOrganisers;

    #[ORM\OneToMany(mappedBy: 'event', targetEntity: ImageCollection::class)]
    #[ORM\OrderBy([
        'createdAt' => Criteria::DESC,
    ])]
    private Collection $imageCollections;

    #[ORM\OneToMany(mappedBy: 'event', targetEntity: EventEmailInvitation::class, cascade: ['persist', 'remove'])]
    private Collection $eventEmailInvitations;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private null|CarbonImmutable $createdAt;

    #[ORM\OneToOne(mappedBy: 'event', cascade: ['persist', 'remove'])]
    private null|EventCancellation $eventCancellation = null;

    #[ORM\OneToMany(mappedBy: 'event', targetEntity: EventOrganiserInvitation::class, cascade: ['persist'])]
    private Collection $eventOrganiserInvitations;

    #[ORM\OneToMany(mappedBy: 'event', targetEntity: EventReview::class)]
    private Collection $eventReviews;

    #[ORM\OneToMany(mappedBy: 'event', targetEntity: EventMoment::class, cascade: ['persist'])]
    #[ORM\OrderBy([
        'createdAt' => Order::Descending->value,
    ])]
    private Collection $eventMoments;

    #[ORM\OneToMany(mappedBy: 'event', targetEntity: EventTicketOption::class, cascade: ['persist'])]
    private Collection $ticketOptions;

    #[ORM\Column(nullable: true)]
    private null|string $url = null;

    public function __construct(
        #[ORM\Column(length: 255)]
        private null|string $title = null,
        #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
        private null|CarbonImmutable $startAt = null,
        #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
        private null|CarbonImmutable $endAt = null,
        #[ORM\Column(type: Types::TEXT, nullable: true)]
        private null|string $description = null,
        #[ORM\Column(type: 'decimal', precision: 10, scale: 7, nullable: true)]
        private null|string $latitude = null,
        #[ORM\Column(type: 'decimal', precision: 10, scale: 7, nullable: true)]
        private null|string $longitude = null,
        #[ORM\Column(type: Types::TEXT, nullable: true)]
        private null|string $base64Image = null,
        #[ORM\Column]
        private null|bool $isPrivate = false,
        #[ORM\Column(length: 255)]
        private null|string $address = null,
        #[ORM\ManyToOne(inversedBy: 'authoredEvents')]
        #[ORM\JoinColumn(nullable: false)]
        private ?User $owner = null,
        #[ORM\ManyToOne(inversedBy: 'events')]
        #[ORM\JoinColumn(nullable: true)]
        private null|EventGroup $eventGroup = null,
    ) {
        $this->categories = new ArrayCollection();
        $this->images = new ArrayCollection();
        $this->eventOrganisers = new ArrayCollection();
        $this->imageCollections = new ArrayCollection();
        $this->eventEmailInvitations = new ArrayCollection();
        $this->eventOrganiserInvitations = new ArrayCollection();
        $this->eventReviews = new ArrayCollection();
        $this->eventParticipants = new ArrayCollection();
        $this->eventInvitations = new ArrayCollection();
        $this->eventRequests = new ArrayCollection();
        $this->eventRejections = new ArrayCollection();
        $this->eventMoments = new ArrayCollection();
        $this->ticketOptions = new ArrayCollection();
        $this->createdAt = new CarbonImmutable();
    }

    public function __toString(): string
    {
        return (string) $this->getTitle();
    }

    public function getId(): null|Uuid
    {
        return $this->id;
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

    /**
     * @throws \Exception
     */
    public function getInterval(): string
    {
        return $this->startAt->diffAsCarbonInterval($this->getEndAt())->forHumans(short: true);
    }

    public function getDurationInHours(): float
    {
        return round($this->startAt->diffInHours($this->getEndAt()), 2);
    }

    public function getTimeRemainingInMilliseconds(): float
    {
        $currentTime = CarbonImmutable::now();
        $endTime = $this->getEndAt();
        return $currentTime->diffInMilliseconds($endTime);
    }

    public function getElapsedTimeInMinutes(): float
    {
        return CarbonImmutable::now()->diffInMinutes($this->startAt);
    }

    public function getElapsedTimeAsInterval(): string
    {
        return CarbonImmutable::now()->diffAsCarbonInterval($this->startAt)->forHumans(short: true);
    }

    public function getTimeRemainingAsInterval(): string
    {
        return CarbonImmutable::now()->diffAsCarbonInterval($this->endAt)->forHumans(short: true);
    }

    public function getDurationInMinutes(): float
    {
        return $this->startAt->diffInMinutes($this->endAt);
    }

    public function getDurationInHour(): float
    {
        return round($this->endAt->diffInMinutes($this->endAt) / 60, 2);
    }

    public function isPendingStart(): bool
    {
        return CarbonImmutable::now()->isBefore($this->getStartAt());
    }

    public function isInProgress(): bool
    {
        return CarbonImmutable::now()->isAfter($this->getStartAt()) && CarbonImmutable::now()->isBefore($this->getEndAt());
    }

    public function getIsComplete(): bool
    {
        $now = CarbonImmutable::now();
        return $now->isAfter($this->getEndAt());
    }

    public function getBase64Image(): null|string
    {
        return $this->base64Image;
    }

    public function setBase64Image(null|string $base64Image): static
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
        if (! $this->getEventParticipants()->contains($user)) {
            $this->eventParticipants->add($user);
            $user->setEvent($this);
        }

        return $this;
    }

    public function removeEventParticipant(EventParticipant $user): self
    {
        // set the owning side to null (unless already changed)
        $this->getEventParticipants()->removeElement($user);

        return $this;
    }

    public function addEventRequest(EventRequest $eventRequest): self
    {
        if (! $this->getEventRequests()->contains($eventRequest)) {
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
        if (! $this->getEventParticipants()->exists(fn (int $key, EventParticipant $eventParticipant) => $eventParticipant->getEvent() === $eventInvite->getEvent() && $eventParticipant->getOwner() === $eventInvite->getTarget()) && ! $this->eventInvitations->exists(fn (int $key, EventInvitation $existingEventInvite) => $existingEventInvite->getEvent() === $eventInvite->getEvent() && $existingEventInvite->getTarget() === $eventInvite->getTarget())) {
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
        if (! $this->getEventRejections()->contains($eventRejection)) {
            $this->eventRejections->add($eventRejection);
            $eventRejection->setEvent($this);
        }

        return $this;
    }

    public function removeEventRejection(EventRejection $eventRejection): self
    {
        if ($this->getEventRejections()->removeElement($eventRejection) && $eventRejection->getEvent() === $this) {
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
        if (! $this->images->contains($image)) {
            $this->images->add($image);
        }

        return $this;
    }

    public function removeImage(Image $image): static
    {
        $this->images->removeElement($image);
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
        if (! $this->getEventOrganisers()->contains($eventOrganiser)) {
            $this->eventOrganisers->add($eventOrganiser);
            $eventOrganiser->setEvent($this);
        }

        return $this;
    }

    public function removeEventOrganiser(EventOrganiser $eventOrganiser): static
    {
        if ($this->getEventOrganisers()->removeElement($eventOrganiser)) {
            // set the owning side to null (unless already changed)
            if ($eventOrganiser->getEvent() === $this) {
                $eventOrganiser->setEvent(null);
            }
        }

        return $this;
    }

    public function isIsPrivate(): null|bool
    {
        return $this->isPrivate;
    }

    public function setIsPrivate(null|bool $isPrivate): static
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
        if (! $this->getImageCollections()->contains($imageCollection)) {
            $this->imageCollections->add($imageCollection);
            $imageCollection->setEvent($this);
        }

        return $this;
    }

    public function removeImageCollection(ImageCollection $imageCollection): static
    {
        if ($this->getImageCollections()->removeElement($imageCollection)) {
            // set the owning side to null (unless already changed)
            if ($imageCollection->getEvent() === $this) {
                $imageCollection->setEvent(null);
            }
        }

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

    /**
     * @return Collection<int, EventEmailInvitation>
     */
    public function getEventEmailInvitations(): Collection
    {
        return $this->eventEmailInvitations;
    }

    public function addEmailInvitation(EventEmailInvitation $emailInvitation): static
    {
        if (! $this->getEventEmailInvitations()->contains($emailInvitation)) {
            $this->eventEmailInvitations->add($emailInvitation);
            $emailInvitation->setEvent($this);
        }

        return $this;
    }

    public function removeEmailInvitation(EventEmailInvitation $emailInvitation): static
    {
        if ($this->getEventEmailInvitations()->removeElement($emailInvitation)) {
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

    public function getUnansweredInvitation(User $user): null|EventInvitation
    {
        return $this->getEventInvitations()->findFirst(fn (int $key, EventInvitation $eventInvitation) => $eventInvitation->getTarget() === $user);
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

    public function getEventCancellation(): ?EventCancellation
    {
        return $this->eventCancellation;
    }

    public function setEventCancellation(?EventCancellation $eventCancellation): static
    {
        // unset the owning side of the relation if necessary
        if ($eventCancellation === null && $this->eventCancellation !== null) {
            $this->eventCancellation->setEvent(null);
        }

        // set the owning side of the relation if necessary
        if ($eventCancellation !== null && $eventCancellation->getEvent() !== $this) {
            $eventCancellation->setEvent($this);
        }

        $this->eventCancellation = $eventCancellation;

        return $this;
    }

    /**
     * @return Collection<int, EventOrganiserInvitation>
     */
    public function getEventOrganiserInvitations(): Collection
    {
        return $this->eventOrganiserInvitations;
    }

    public function addEventOrganiserInvitation(EventOrganiserInvitation $eventOrganiserInvitation): static
    {
        if (! $this->eventOrganiserInvitations->contains($eventOrganiserInvitation)) {
            $this->eventOrganiserInvitations->add($eventOrganiserInvitation);
            $eventOrganiserInvitation->setEvent($this);
        }

        return $this;
    }

    public function removeEventOrganiserInvitation(EventOrganiserInvitation $eventOrganiserInvitation): static
    {
        if ($this->eventOrganiserInvitations->removeElement($eventOrganiserInvitation)) {
            // set the owning side to null (unless already changed)
            if ($eventOrganiserInvitation->getEvent() === $this) {
                $eventOrganiserInvitation->setEvent(null);
            }
        }

        return $this;
    }

    public function hasRequestedToAttend(User $user): bool
    {
        return $this->getEventRequests()->exists(fn (int $key, EventRequest $eventRequest) => $eventRequest->getOwner() === $user);
    }

    public function hasBeenInvited(User $user): bool
    {
        return $this->getEventInvitations()->exists(fn (int $key, EventInvitation $eventInvitation) => $eventInvitation->getTarget() === $user);
    }

    public function hasEmailBeenInvited(string $emailAddress): bool
    {
        return $this->getEventEmailInvitations()->exists(fn (int $key, EventEmailInvitation $eventEmailInvitation) => $eventEmailInvitation->getEmail()->getAddress() === $emailAddress);
    }

    public function getRequestToAttend(User $user): null|EventRequest
    {
        return $this->getEventRequests()->findFirst(fn (int $key, EventRequest $eventRequest) => $eventRequest->getOwner() === $user);
    }

    public function attendRequest(User $user): null|EventRequest
    {
        return $this->getEventRequests()->findFirst(fn (int $key, EventRequest $eventRequest) => $eventRequest->getOwner() === $user);
    }

    public function getIsAttending(User $user): bool
    {
        return $this->getEventParticipants()->exists(fn (int $key, EventParticipant $eventParticipant) => $eventParticipant->getOwner() === $user);
    }

    public function hasRated(User $user): bool
    {
        return $this->getEventReviews()->exists(fn (int $key, EventReview $eventReview) => $eventReview->getOwner() === $user);
    }

    public function getIsOrganiser(User $user): bool
    {
        return $this->getEventOrganisers()->exists(fn (int $key, EventOrganiser $eventOrganiser) => $eventOrganiser->getOwner() === $user);
    }

    public function isAlreadyInvitedOrganiser(User $user): bool
    {
        return $this->eventOrganiserInvitations->exists(fn (int $key, EventOrganiserInvitation $eventOrganiserInvitation) => $eventOrganiserInvitation->getOwner() instanceof User && $eventOrganiserInvitation->getOwner() === $user);
    }

    public function isEmailAlreadyInvitedOrganiser(string $emailAddress): bool
    {
        return $this->eventOrganiserInvitations->exists(fn (int $key, EventOrganiserInvitation $eventOrganiserInvitation) => $eventOrganiserInvitation->getOwner()->getEmail()->getAddress() === $emailAddress);
    }

    /**
     * @return Collection<int, EventReview>
     */
    public function getEventReviews(): Collection
    {
        return $this->eventReviews;
    }

    public function addEventReview(EventReview $eventReview): static
    {
        if (! $this->eventReviews->contains($eventReview)) {
            $this->eventReviews->add($eventReview);
            $eventReview->setEvent($this);
        }

        return $this;
    }

    public function removeEventReview(EventReview $eventReview): static
    {
        if ($this->eventReviews->removeElement($eventReview)) {
            // set the owning side to null (unless already changed)
            if ($eventReview->getEvent() === $this) {
                $eventReview->setEvent(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, EventMoment>
     */
    public function getEventMoments(): Collection
    {
        return $this->eventMoments;
    }

    public function addEventMoment(EventMoment $eventChangeLog): static
    {
        if (! $this->eventMoments->contains($eventChangeLog)) {
            $this->eventMoments->add($eventChangeLog);
            $eventChangeLog->setEvent($this);
        }

        return $this;
    }

    public function removeEventMoment(EventMoment $eventMoment): static
    {
        if ($this->eventMoments->removeElement($eventMoment)) {
            // set the owning side to null (unless already changed)
            if ($eventMoment->getEvent() === $this) {
                $eventMoment->setEvent(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, EventTicketOption>
     */
    public function getTicketOptions(): Collection
    {
        return $this->ticketOptions;
    }

    public function addTicketOption(EventTicketOption $ticketOption): static
    {
        if (! $this->ticketOptions->contains($ticketOption)) {
            $this->ticketOptions->add($ticketOption);
            $ticketOption->setEvent($this);
        }

        return $this;
    }

    public function removeTicketOption(EventTicketOption $ticketOption): static
    {
        if ($this->ticketOptions->removeElement($ticketOption)) {
            // set the owning side to null (unless already changed)
            if ($ticketOption->getEvent() === $this) {
                $ticketOption->setEvent(null);
            }
        }

        return $this;
    }

    public function getAllInvitationsCount(): int
    {
        return $this->getEventEmailInvitations()->count() + $this->getEventInvitations()->count();
    }

    public function getAllParticipantsCount(): int
    {
        return $this->getEventOrganisers()->count() + $this->getEventParticipants()->count();
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): void
    {
        $this->url = $url;
    }
}

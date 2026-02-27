<?php

declare(strict_types=1);

namespace App\Entity\EventGroup;

use App\Entity\Event\Category;
use App\Entity\Event\Event;
use App\Entity\User\User;
use App\Enum\EventGroupRoleEnum;
use App\Repository\Event\EventGroupRepository;
use Carbon\CarbonImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: EventGroupRepository::class)]
#[UniqueEntity(fields: ['name'], message: 'There\'s already a group with this name')]
#[Vich\Uploadable]
class EventGroup
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\CustomIdGenerator(UuidGenerator::class)]
    private Uuid $id;

    #[ORM\OneToMany(targetEntity: Event::class, mappedBy: 'eventGroup', cascade: ['persist'])]
    #[ORM\OrderBy([
        'startAt' => Criteria::DESC,
    ])]
    private Collection $events;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private CarbonImmutable $createdAt;

    #[ORM\OneToMany(targetEntity: EventGroupMember::class, mappedBy: 'eventGroup', cascade: ['persist'])]
    private Collection $eventGroupMembers;

    #[ORM\ManyToMany(targetEntity: Category::class)]
    #[Assert\Count(min: 1, max: 5)]
    private Collection $categories;

    #[ORM\OneToMany(targetEntity: EventGroupInvitation::class, mappedBy: 'eventGroup')]
    private Collection $eventGroupInvitations;

    #[ORM\OneToMany(targetEntity: EventGroupJoinRequest::class, mappedBy: 'eventGroup')]
    private Collection $eventGroupJoinRequests;

    #[ORM\Column(length: 2, nullable: true)]
    private null|string $country = null;

    #[ORM\Column(length: 255, nullable: true)]
    private null|string $city = null;

    #[ORM\Column(length: 255, nullable: true)]
    private null|string $entityIdentificationNumber = null;

    #[ORM\Column(length: 255, nullable: true)]
    private null|string $purpose = null;

    #[ORM\Column]
    private bool $isPrivate = false;

    #[Vich\UploadableField(mapping: 'event_group_image', fileNameProperty: 'imageName')]
    private ?File $imageFile = null;

    #[ORM\Column(nullable: true)]
    private ?string $imageName = null;

    public function __construct(
        #[ORM\Column(length: 255)]
        #[Assert\NotBlank]
        private null|string $name = null,
        #[ORM\Column(type: Types::STRING, length: 140)]
        #[Assert\NotBlank]
        #[Assert\Length(min: 20, max: 140)]
        private null|string $description = null,
        #[ORM\ManyToOne(inversedBy: 'eventGroups')]
        private null|User $owner = null,
        #[ORM\Column(length: 255, nullable: true)]
        private null|string $language = null,
    ) {
        $this->events = new ArrayCollection();
        $this->eventGroupMembers = new ArrayCollection();
        $this->createdAt = new CarbonImmutable();
        $this->categories = new ArrayCollection();
        $this->eventGroupInvitations = new ArrayCollection();
        $this->eventGroupJoinRequests = new ArrayCollection();
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getName(): null|string
    {
        return $this->name;
    }

    public function setName(null|string $name): static
    {
        $this->name = $name;

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
            $event->setEventGroup($this);
        }

        return $this;
    }

    public function removeEvent(Event $event): static
    {
        if ($this->events->removeElement($event)) {
            // set the owning side to null (unless already changed)
            if ($event->getEventGroup() === $this) {
                $event->setEventGroup(null);
            }
        }

        return $this;
    }

    public function getCreatedAt(): null|CarbonImmutable
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

    /**
     * @return Collection<int, EventGroupMember>
     */
    public function getEventGroupMembers(): Collection
    {
        return $this->eventGroupMembers;
    }

    public function addEventGroupMember(EventGroupMember $eventGroupMember): static
    {
        if (! $this->eventGroupMembers->contains($eventGroupMember)) {
            $this->eventGroupMembers->add($eventGroupMember);
            $eventGroupMember->setEventGroup($this);
        }

        return $this;
    }

    public function removeEventGroupMember(EventGroupMember $eventGroupMember): static
    {
        if ($this->eventGroupMembers->removeElement($eventGroupMember)) {
            // set the owning side to null (unless already changed)
            if ($eventGroupMember->getEventGroup() === $this) {
                $eventGroupMember->setEventGroup(null);
            }
        }

        return $this;
    }

    /**
     * @return ArrayCollection<int, EventGroupMember>
     */
    public function getEventGroupMaintainers(): ArrayCollection
    {
        return $this->eventGroupMembers->filter(fn (EventGroupMember $eventGroupMember) => $eventGroupMember->getRoles()->exists(fn (int $key, EventGroupRole $eventGroupRole) => $eventGroupRole->getTitle() === EventGroupRoleEnum::ROLE_GROUP_MAINTAINER));
    }

    public function getDescription(): null|string
    {
        return $this->description;
    }

    public function setDescription(null|string $description): void
    {
        $this->description = $description;
    }

    public function getIsMember(User $user): bool
    {
        return $this->getEventGroupMembers()->exists(fn (int $key, EventGroupMember $eventGroupMember) => $eventGroupMember->getOwner() === $user && $eventGroupMember->getRoles()->exists(fn (int $key, EventGroupRole $eventGroupRole) => $eventGroupRole->getTitle() === EventGroupRoleEnum::ROLE_GROUP_MEMBER));
    }

    public function getIsMaintainer(User $user): bool
    {
        return $this->getEventGroupMembers()->exists(fn (int $key, EventGroupMember $eventGroupMember) => $eventGroupMember->getOwner() === $user && $eventGroupMember->getRoles()->exists(fn (int $key, EventGroupRole $eventGroupRole) => $eventGroupRole->getTitle() === EventGroupRoleEnum::ROLE_GROUP_MAINTAINER));
    }

    public function getMember(User $user): null|EventGroupMember
    {
        return $this->getEventGroupMembers()->findFirst(fn (int $key, EventGroupMember $eventGroupMember) => $eventGroupMember->getOwner() === $user);
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImageFile(?File $imageFile): static
    {
        $this->imageFile = $imageFile;

        return $this;
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function setImageName(?string $imageName): static
    {
        $this->imageName = $imageName;

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

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): void
    {
        $this->country = $country;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): void
    {
        $this->city = $city;
    }

    public function getEntityIdentificationNumber(): ?string
    {
        return $this->entityIdentificationNumber;
    }

    public function setEntityIdentificationNumber(?string $entityIdentificationNumber): void
    {
        $this->entityIdentificationNumber = $entityIdentificationNumber;
    }

    public function getPurpose(): ?string
    {
        return $this->purpose;
    }

    public function setPurpose(?string $purpose): void
    {
        $this->purpose = $purpose;
    }

    public function getIsPrivate(): bool
    {
        return $this->isPrivate;
    }

    public function setIsPrivate(bool $isPrivate): void
    {
        $this->isPrivate = $isPrivate;
    }

    public function getLanguage(): null|string
    {
        return $this->language;
    }

    public function setLanguage(null|string $language): static
    {
        $this->language = $language;

        return $this;
    }

    public function createdAgo(): string
    {
        return $this->createdAt->diffForHumans();
    }

    /**
     * @return Collection<int, EventGroupInvitation>
     */
    public function getEventGroupInvitations(): Collection
    {
        return $this->eventGroupInvitations;
    }

    public function addEventGroupInvitation(EventGroupInvitation $eventGroupInvitation): static
    {
        if (! $this->eventGroupInvitations->contains($eventGroupInvitation)) {
            $this->eventGroupInvitations->add($eventGroupInvitation);
            $eventGroupInvitation->setEventGroup($this);
        }

        return $this;
    }

    public function removeEventGroupInvitation(EventGroupInvitation $eventGroupInvitation): static
    {
        if ($this->eventGroupInvitations->removeElement($eventGroupInvitation)) {
            // set the owning side to null (unless already changed)
            if ($eventGroupInvitation->getEventGroup() === $this) {
                $eventGroupInvitation->setEventGroup(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, EventGroupJoinRequest>
     */
    public function getEventGroupJoinRequests(): Collection
    {
        return $this->eventGroupJoinRequests;
    }

    public function addEventGroupJoinRequest(EventGroupJoinRequest $eventGroupJoinRequest): static
    {
        if (! $this->eventGroupJoinRequests->contains($eventGroupJoinRequest)) {
            $this->eventGroupJoinRequests->add($eventGroupJoinRequest);
            $eventGroupJoinRequest->setEventGroup($this);
        }

        return $this;
    }

    public function removeEventGroupJoinRequest(EventGroupJoinRequest $eventGroupJoinRequest): static
    {
        if ($this->eventGroupJoinRequests->removeElement($eventGroupJoinRequest)) {
            // set the owning side to null (unless already changed)
            if ($eventGroupJoinRequest->getEventGroup() === $this) {
                $eventGroupJoinRequest->setEventGroup(null);
            }
        }

        return $this;
    }

    public function hasUserSentJoinRequest(User $user): bool
    {
        return $this->eventGroupJoinRequests->exists(fn (int $key, EventGroupJoinRequest $eventGroupJoinRequest) => $eventGroupJoinRequest->getOwner() === $user);
    }

    public function getUserJoinRequest(User $user): null|EventGroupJoinRequest
    {
        return $this->eventGroupJoinRequests->findFirst(fn (int $key, EventGroupJoinRequest $eventGroupJoinRequest) => $eventGroupJoinRequest->getOwner() === $user);
    }

    public function isUserAdmin(User $user): bool
    {
        return $this->getEventGroupMembers()->exists(fn (int $key, EventGroupMember $eventGroupMember) => $eventGroupMember->getOwner() === $user && $eventGroupMember->getRoles()->exists(fn (int $key, EventGroupRole $eventGroupRole) => $eventGroupRole->getTitle() === EventGroupRoleEnum::ROLE_GROUP_MANAGER || $eventGroupRole->getTitle() === EventGroupRoleEnum::ROLE_GROUP_CREATOR));
    }
}

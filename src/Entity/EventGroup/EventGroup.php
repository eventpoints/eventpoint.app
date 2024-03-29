<?php

declare(strict_types=1);

namespace App\Entity\EventGroup;

use App\Entity\City;
use App\Entity\Country;
use App\Entity\Event\Category;
use App\Entity\Event\Event;
use App\Entity\Poll\Poll;
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
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: EventGroupRepository::class)]
#[UniqueEntity(fields: ['name'], message: 'There\'s already a group with this name')]
#[UniqueEntity(fields: ['entityIdentificationNumber'], message: 'There is already a group with this entity identification number')]
class EventGroup
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\CustomIdGenerator(UuidGenerator::class)]
    private Uuid $id;

    #[ORM\OneToMany(mappedBy: 'eventGroup', targetEntity: Event::class, cascade: ['persist'])]
    #[ORM\OrderBy([
        'startAt' => Criteria::DESC,
    ])]
    private Collection $events;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private CarbonImmutable $createdAt;

    #[ORM\OneToMany(mappedBy: 'eventGroup', targetEntity: EventGroupMember::class, cascade: ['persist'])]
    private Collection $eventGroupMembers;

    #[ORM\OneToMany(mappedBy: 'eventGroup', targetEntity: EventGroupDiscussion::class)]
    private Collection $eventGroupDiscussions;

    #[ORM\OneToMany(mappedBy: 'eventGroup', targetEntity: Poll::class)]
    private Collection $polls;

    #[ORM\ManyToMany(targetEntity: Category::class)]
    #[Assert\Count(min: 1, max: 5)]
    private Collection $categories;

    #[ORM\OneToMany(mappedBy: 'eventGroup', targetEntity: EventGroupInvitation::class)]
    private Collection $eventGroupInvitations;

    #[ORM\OneToMany(mappedBy: 'eventGroup', targetEntity: EventGroupJoinRequest::class)]
    private Collection $eventGroupJoinRequests;

    public function __construct(
        #[ORM\Column(length: 255)]
        #[Assert\NotBlank]
        private null|string $name = null,
        #[ORM\Column(type: Types::STRING, length: 140)]
        #[Assert\NotBlank]
        #[Assert\Length(min: 20, max: 140)]
        private null|string $purpose = null,
        #[ORM\ManyToOne(inversedBy: 'eventGroups')]
        private null|User $owner = null,
        #[ORM\Column(length: 255)]
        #[Assert\NotBlank]
        private null|string $entityIdentificationNumber = null,
        #[ORM\Column(type: Types::TEXT, nullable: true)]
        private null|string $base64Image = null,
        #[ORM\ManyToOne(targetEntity: Country::class)]
        private null|Country $country = null,
        #[ORM\ManyToOne(targetEntity: City::class)]
        private null|City $city = null,
        #[ORM\Column(length: 255, nullable: true)]
        private null|string $language = null,
        #[ORM\Column(nullable: true)]
        private null|bool $isPrivate = null,
    ) {
        $this->events = new ArrayCollection();
        $this->eventGroupMembers = new ArrayCollection();
        $this->createdAt = new CarbonImmutable();
        $this->eventGroupDiscussions = new ArrayCollection();
        $this->polls = new ArrayCollection();
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

    /**
     * @return Collection<int, EventGroupDiscussion>
     */
    public function getEventGroupDiscussions(): Collection
    {
        return $this->eventGroupDiscussions;
    }

    public function addEventGroupDiscussion(EventGroupDiscussion $eventGroupDiscussion): static
    {
        if (! $this->eventGroupDiscussions->contains($eventGroupDiscussion)) {
            $this->eventGroupDiscussions->add($eventGroupDiscussion);
            $eventGroupDiscussion->setEventGroup($this);
        }

        return $this;
    }

    public function removeEventGroupDiscussion(EventGroupDiscussion $eventGroupDiscussion): static
    {
        if ($this->eventGroupDiscussions->removeElement($eventGroupDiscussion)) {
            // set the owning side to null (unless already changed)
            if ($eventGroupDiscussion->getEventGroup() === $this) {
                $eventGroupDiscussion->setEventGroup(null);
            }
        }

        return $this;
    }

    public function getEntityIdentificationNumber(): null|string
    {
        return $this->entityIdentificationNumber;
    }

    public function setEntityIdentificationNumber(null|string $entityIdentificationNumber): static
    {
        $this->entityIdentificationNumber = $entityIdentificationNumber;

        return $this;
    }

    public function getPurpose(): null|string
    {
        return $this->purpose;
    }

    public function setPurpose(null|string $purpose): void
    {
        $this->purpose = $purpose;
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

    /**
     * @return Collection<int, Poll>
     */
    public function getPolls(): Collection
    {
        return $this->polls;
    }

    public function addPoll(Poll $poll): static
    {
        if (! $this->polls->contains($poll)) {
            $this->polls->add($poll);
            $poll->setEventGroup($this);
        }

        return $this;
    }

    public function removePoll(Poll $poll): static
    {
        if ($this->polls->removeElement($poll)) {
            // set the owning side to null (unless already changed)
            if ($poll->getEventGroup() === $this) {
                $poll->setEventGroup(null);
            }
        }

        return $this;
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

    public function getCountry(): ?Country
    {
        return $this->country;
    }

    public function setCountry(?Country $country): void
    {
        $this->country = $country;
    }

    public function getCity(): null|City
    {
        return $this->city;
    }

    public function setCity(?City $city): void
    {
        $this->city = $city;
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

    public function getIsPrivate(): null|bool
    {
        return $this->isPrivate;
    }

    public function setIsPrivate(null|bool $isPrivate): static
    {
        $this->isPrivate = $isPrivate;

        return $this;
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

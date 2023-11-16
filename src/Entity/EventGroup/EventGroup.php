<?php

declare(strict_types=1);

namespace App\Entity\EventGroup;

use App\Entity\Event\Event;
use App\Entity\User;
use App\Enum\EventGroupRoleEnum;
use App\Repository\Event\EventGroupRepository;
use Carbon\CarbonImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: EventGroupRepository::class)]
#[UniqueEntity(fields: ['title'], message: 'There is already a group with this title')]
class EventGroup
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\CustomIdGenerator(UuidGenerator::class)]
    private Uuid $id;

    #[ORM\Column(length: 255)]
    private string $title;

    #[ORM\OneToMany(mappedBy: 'eventGroup', targetEntity: Event::class, cascade: ['persist'])]
    #[ORM\OrderBy([
        'startAt' => Criteria::DESC,
    ])]
    private Collection $events;

    #[ORM\Column]
    private CarbonImmutable $createdAt;

    #[ORM\ManyToOne(inversedBy: 'eventGroups')]
    private null|User $owner = null;

    #[ORM\OneToMany(mappedBy: 'eventGroup', targetEntity: EventGroupMember::class, cascade: ['persist'])]
    private Collection $eventGroupMembers;

    public function __construct()
    {
        $this->events = new ArrayCollection();
        $this->eventGroupMembers = new ArrayCollection();
        $this->createdAt = new CarbonImmutable();
    }

    public function getId(): Uuid
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
        return $this->eventGroupMembers->filter(fn (EventGroupMember $eventGroupMember) => $eventGroupMember->getRoles()->exists(fn (int $key, EventGroupRole $eventGroupRole) => $eventGroupRole->getTitle() === EventGroupRoleEnum::ROLE_GROUP_MAINTAINER->name));
    }
}

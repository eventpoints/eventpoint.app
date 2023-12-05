<?php

declare(strict_types=1);

namespace App\Entity\Event;

use App\Entity\User;
use App\Repository\Event\EventParticipantRepository;
use Carbon\CarbonImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: EventParticipantRepository::class)]
class EventParticipant
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\CustomIdGenerator(UuidGenerator::class)]
    private Uuid $id;

    #[ORM\ManyToOne(targetEntity: User::class, cascade: ['persist'], inversedBy: 'acceptedEvents')]
    private User $owner;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private CarbonImmutable $createdAt;

    #[ORM\ManyToOne(cascade: ['persist'], inversedBy: 'eventUsers')]
    private Event $event;

    #[ORM\JoinTable(name: 'event_participant_roles')]
    #[ORM\JoinColumn(name: 'event_participant_id', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'event_role_id', referencedColumnName: 'id')]
    #[ORM\ManyToMany(targetEntity: EventRole::class)]
    private Collection $roles;

    public function __construct()
    {
        $this->roles = new ArrayCollection();
        $this->createdAt = new CarbonImmutable();
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getOwner(): User
    {
        return $this->owner;
    }

    public function setOwner(User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    public function getCreatedAt(): null|CarbonImmutable
    {
        return $this->createdAt;
    }

    public function getEvent(): Event
    {
        return $this->event;
    }

    public function setEvent(Event $event): self
    {
        $this->event = $event;

        return $this;
    }

    /**
     * @return Collection<int, EventRole>
     */
    public function getRoles(): Collection
    {
        return $this->roles;
    }

    public function addRole(EventRole $role): static
    {
        if (! $this->roles->contains($role)) {
            $this->roles->add($role);
        }

        return $this;
    }

    public function removeRole(EventRole $role): static
    {
        $this->roles->removeElement($role);
        return $this;
    }
}

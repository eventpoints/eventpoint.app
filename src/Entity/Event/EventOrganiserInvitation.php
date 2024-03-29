<?php

declare(strict_types=1);

namespace App\Entity\Event;

use App\Entity\User\User;
use App\Repository\Event\EventOrganiserInvitationRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: EventOrganiserInvitationRepository::class)]
class EventOrganiserInvitation
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\CustomIdGenerator(UuidGenerator::class)]
    private Uuid $id;

    #[ORM\ManyToOne(inversedBy: 'eventOrganiserInvitations')]
    private null|Event $event = null;

    #[ORM\Column(type: 'uuid')]
    private null|Uuid $token = null;

    #[ORM\ManyToOne(inversedBy: 'eventOrganiserInvitations')]
    private ?User $owner = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private null|DateTimeImmutable $createdAt = null;

    /**
     * @var Collection<int, EventRole>
     */
    #[ORM\JoinTable(name: 'event_organiser_invitation_roles')]
    #[ORM\JoinColumn(name: 'event_organiser_invitation_id', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'event_role_id', referencedColumnName: 'id')]
    #[ORM\ManyToMany(targetEntity: EventRole::class)]
    private Collection $roles;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable();
        $this->token = Uuid::v4();
        $this->roles = new ArrayCollection();
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

    public function getToken(): null|Uuid
    {
        return $this->token;
    }

    public function setToken(Uuid $token): static
    {
        $this->token = $token;

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

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

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

<?php

declare(strict_types=1);

namespace App\Entity\EventGroup;

use App\Entity\User;
use App\Enum\EventGroupRoleEnum;
use App\Repository\EventGroupMemberRepository;
use Carbon\CarbonImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: EventGroupMemberRepository::class)]
class EventGroupMember
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\CustomIdGenerator(UuidGenerator::class)]
    private Uuid $id;

    #[ORM\ManyToOne(inversedBy: 'eventGroupMembers')]
    private ?User $owner = null;

    #[ORM\ManyToOne(inversedBy: 'eventGroupMembers')]
    private ?EventGroup $eventGroup = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private CarbonImmutable $createdAt;

    #[ORM\JoinTable(name: 'event_group_member_roles')]
    #[ORM\JoinColumn(name: 'event_organiser_id', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'event_role_id', referencedColumnName: 'id')]
    #[ORM\ManyToMany(targetEntity: EventGroupRole::class)]
    private Collection $roles;

    #[ORM\Column]
    private bool $isApproved = false;

    public function __construct()
    {
        $this->createdAt = new CarbonImmutable();
        $this->roles = new ArrayCollection();
    }

    public function getId(): null|Uuid
    {
        return $this->id;
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

    public function getEventGroup(): ?EventGroup
    {
        return $this->eventGroup;
    }

    public function setEventGroup(?EventGroup $eventGroup): static
    {
        $this->eventGroup = $eventGroup;

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

    public function isIsApproved(): ?bool
    {
        return $this->isApproved;
    }

    public function setIsApproved(bool $isApproved): static
    {
        $this->isApproved = $isApproved;

        return $this;
    }

    /**
     * @return Collection<int, EventGroupRole>
     */
    public function getRoles(): Collection
    {
        return $this->roles;
    }

    public function addRole(EventGroupRole $role): static
    {
        if (! $this->roles->contains($role)) {
            $this->roles->add($role);
        }

        return $this;
    }

    public function removeRole(EventGroupRole $role): static
    {
        $this->roles->removeElement($role);
        return $this;
    }

    public function isGroupAdmin(): bool
    {
        return $this->getRoles()->contains(fn (EventGroupRole $eventGroupRole) => $eventGroupRole->getTitle() === EventGroupRoleEnum::ROLE_GROUP_MANAGER ||
            $eventGroupRole->getTitle() === EventGroupRoleEnum::ROLE_GROUP_CREATOR ||
            $eventGroupRole->getTitle() === EventGroupRoleEnum::ROLE_GROUP_MAINTAINER);
    }
}

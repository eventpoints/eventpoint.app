<?php

namespace App\Entity\EventGroup;

use App\Entity\User;
use App\Repository\EventGroupMemberRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    #[ORM\Column]
    private DateTimeImmutable $createdAt;

    #[ORM\OneToMany(mappedBy: 'eventGroupMember', targetEntity: EventGroupRole::class)]
    private Collection $roles;

    #[ORM\Column]
    private bool $isApproved = false;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable();
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
     * @return Collection<int, EventGroupRole>
     */
    public function getRoles(): Collection
    {
        return $this->roles;
    }

    public function addRole(EventGroupRole $role): static
    {
        if (!$this->roles->contains($role)) {
            $this->roles->add($role);
            $role->setEventGroupMember($this);
        }

        return $this;
    }

    public function removeRole(EventGroupRole $role): static
    {
        if ($this->roles->removeElement($role)) {
            // set the owning side to null (unless already changed)
            if ($role->getEventGroupMember() === $this) {
                $role->setEventGroupMember(null);
            }
        }

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

}

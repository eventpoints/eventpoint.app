<?php

declare(strict_types=1);

namespace App\Entity\Event;

use App\Entity\User;
use App\Repository\Event\EventOrganiserRepository;
use Carbon\CarbonImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: EventOrganiserRepository::class)]
class EventOrganiser
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\CustomIdGenerator(UuidGenerator::class)]
    private Uuid $id;

    #[ORM\ManyToOne(inversedBy: 'eventOrganisers')]
    private ?User $owner = null;

    #[ORM\ManyToOne(inversedBy: 'eventOrganisers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Event $event = null;

    #[ORM\Column]
    private CarbonImmutable $createdAt;

    #[ORM\OneToMany(mappedBy: 'eventOrganiser', targetEntity: EventRole::class)]
    private Collection $roles;

    public function __construct()
    {
        $this->roles = new ArrayCollection();
        $this->createdAt = new CarbonImmutable();
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

    public function getEvent(): ?Event
    {
        return $this->event;
    }

    public function setEvent(?Event $event): static
    {
        $this->event = $event;

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
            $role->setEventOrganiser($this);
        }

        return $this;
    }

    public function removeRole(EventRole $role): static
    {
        if ($this->roles->removeElement($role)) {
            // set the owning side to null (unless already changed)
            if ($role->getEventOrganiser() === $this) {
                $role->setEventOrganiser(null);
            }
        }

        return $this;
    }
}

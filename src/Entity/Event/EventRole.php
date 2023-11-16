<?php

namespace App\Entity\Event;

use App\Repository\Event\EventRoleRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: EventRoleRepository::class)]

class EventRole
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\CustomIdGenerator(UuidGenerator::class)]
    private Uuid $id;

    #[ORM\ManyToOne(inversedBy: 'roles')]
    private ?EventParticipant $eventParticipant = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\ManyToOne(inversedBy: 'roles')]
    private ?EventOrganiser $eventOrganiser = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getEventParticipant(): ?EventParticipant
    {
        return $this->eventParticipant;
    }

    public function setEventParticipant(?EventParticipant $eventParticipant): static
    {
        $this->eventParticipant = $eventParticipant;

        return $this;
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

    public function getEventOrganiser(): ?EventOrganiser
    {
        return $this->eventOrganiser;
    }

    public function setEventOrganiser(?EventOrganiser $eventOrganiser): static
    {
        $this->eventOrganiser = $eventOrganiser;

        return $this;
    }

    public function __toString(): string
    {
        return $this->getTitle();
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

}
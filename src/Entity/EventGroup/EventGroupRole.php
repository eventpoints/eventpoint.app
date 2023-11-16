<?php

namespace App\Entity\EventGroup;

use App\Repository\EventGroupRoleRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: EventGroupRoleRepository::class)]
class EventGroupRole
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\CustomIdGenerator(UuidGenerator::class)]
    private Uuid $id;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\ManyToOne(inversedBy: 'roles')]
    private ?EventGroupMember $eventGroupMember = null;

    public function getId(): null|Uuid
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getEventGroupMember(): ?EventGroupMember
    {
        return $this->eventGroupMember;
    }

    public function setEventGroupMember(?EventGroupMember $eventGroupMember): static
    {
        $this->eventGroupMember = $eventGroupMember;

        return $this;
    }

    public function __toString(): string
    {
        return $this->getTitle();
    }
}

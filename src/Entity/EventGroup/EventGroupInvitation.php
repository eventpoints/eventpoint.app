<?php

declare(strict_types=1);

namespace App\Entity\EventGroup;

use App\Entity\User\User;
use App\Repository\EventGroup\EventGroupInvitationRepository;
use Carbon\CarbonImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: EventGroupInvitationRepository::class)]
class EventGroupInvitation
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\CustomIdGenerator(UuidGenerator::class)]
    private Uuid $id;

    #[ORM\ManyToOne(inversedBy: 'eventGroupInvitations')]
    #[ORM\JoinColumn(nullable: false)]
    private null|EventGroup $eventGroup = null;

    #[ORM\ManyToOne(inversedBy: 'eventGroupInvitations')]
    #[ORM\JoinColumn(nullable: false)]
    private null|User $owner = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private null|CarbonImmutable $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private null|CarbonImmutable $approvedAt = null;

    public function __construct()
    {
        $this->createdAt = new CarbonImmutable();
    }

    public function getId(): null|Uuid
    {
        return $this->id;
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

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): static
    {
        $this->owner = $owner;

        return $this;
    }

    public function getCreatedAt(): null|CarbonImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(null|CarbonImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getApprovedAt(): null|CarbonImmutable
    {
        return $this->approvedAt;
    }

    public function setApprovedAt(null|CarbonImmutable $approvedAt): static
    {
        $this->approvedAt = $approvedAt;

        return $this;
    }
}

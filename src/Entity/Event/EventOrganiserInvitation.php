<?php

declare(strict_types=1);

namespace App\Entity\Event;

use App\Entity\User\User;
use App\Enum\EventParticipantRoleEnum;
use App\Repository\Event\EventOrganiserInvitationRepository;
use DateTimeImmutable;
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

    #[ORM\Column(length: 255, enumType: EventParticipantRoleEnum::class, options: ['default' => EventParticipantRoleEnum::ROLE_ORGANISER->value])]
    private EventParticipantRoleEnum $role = EventParticipantRoleEnum::ROLE_ORGANISER;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable();
        $this->token = Uuid::v4();
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

    public function getRole(): EventParticipantRoleEnum
    {
        return $this->role;
    }

    public function setRole(EventParticipantRoleEnum $role): static
    {
        $this->role = $role;

        return $this;
    }
}

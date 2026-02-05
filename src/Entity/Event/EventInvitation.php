<?php

declare(strict_types=1);

namespace App\Entity\Event;

use App\Entity\User\Email;
use App\Entity\User\PhoneNumber;
use App\Entity\User\User;
use App\Enum\EventInvitationStatusEnum;
use App\Enum\EventInvitationTypeEnum;
use App\Repository\Event\EventInvitationRepository;
use Carbon\CarbonImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: EventInvitationRepository::class)]
class EventInvitation
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\CustomIdGenerator(UuidGenerator::class)]
    private Uuid $id;

    #[ORM\ManyToOne(inversedBy: 'eventInvitations')]
    private Event $event;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private CarbonImmutable $createdAt;

    #[ORM\Column(length: 20, enumType: EventInvitationTypeEnum::class)]
    private EventInvitationTypeEnum $type;

    #[ORM\Column(length: 20, enumType: EventInvitationStatusEnum::class, options: ['default' => EventInvitationStatusEnum::PENDING->value])]
    private EventInvitationStatusEnum $status = EventInvitationStatusEnum::PENDING;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'receivedEventInvitations')]
    #[ORM\JoinColumn(nullable: true)]
    private null|User $targetUser = null;

    #[ORM\ManyToOne(cascade: ['persist'])]
    private null|Email $targetEmail = null;

    #[ORM\ManyToOne(cascade: ['persist'])]
    private null|PhoneNumber $targetPhone = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'createdEventInvitations')]
    #[ORM\JoinColumn(nullable: false)]
    private User $owner;

    #[ORM\Column(type: 'uuid', nullable: true)]
    private null|Uuid $token = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private null|CarbonImmutable $resolvedAt = null;

    public function __construct()
    {
        $this->createdAt = new CarbonImmutable();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
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

    public function getCreatedAt(): null|CarbonImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(CarbonImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getType(): EventInvitationTypeEnum
    {
        return $this->type;
    }

    public function setType(EventInvitationTypeEnum $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getStatus(): EventInvitationStatusEnum
    {
        return $this->status;
    }

    public function setStatus(EventInvitationStatusEnum $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getTargetUser(): null|User
    {
        return $this->targetUser;
    }

    public function setTargetUser(null|User $targetUser): self
    {
        $this->targetUser = $targetUser;

        return $this;
    }

    /**
     * @deprecated Use getTargetUser() instead
     */
    public function getTarget(): null|User
    {
        return $this->targetUser;
    }

    /**
     * @deprecated Use setTargetUser() instead
     */
    public function setTarget(null|User $target): void
    {
        $this->targetUser = $target;
    }

    public function getTargetEmail(): null|Email
    {
        return $this->targetEmail;
    }

    public function setTargetEmail(null|Email $targetEmail): self
    {
        $this->targetEmail = $targetEmail;

        return $this;
    }

    public function getTargetPhone(): null|PhoneNumber
    {
        return $this->targetPhone;
    }

    public function setTargetPhone(null|PhoneNumber $targetPhone): self
    {
        $this->targetPhone = $targetPhone;

        return $this;
    }

    public function getOwner(): null|User
    {
        return $this->owner;
    }

    public function setOwner(null|User $owner): void
    {
        $this->owner = $owner;
    }

    public function getToken(): null|Uuid
    {
        return $this->token;
    }

    public function setToken(null|Uuid $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function getResolvedAt(): null|CarbonImmutable
    {
        return $this->resolvedAt;
    }

    public function setResolvedAt(null|CarbonImmutable $resolvedAt): self
    {
        $this->resolvedAt = $resolvedAt;

        return $this;
    }

    public function isInvitation(): bool
    {
        return $this->type === EventInvitationTypeEnum::INVITATION;
    }

    public function isRequest(): bool
    {
        return $this->type === EventInvitationTypeEnum::REQUEST;
    }

    public function isPending(): bool
    {
        return $this->status === EventInvitationStatusEnum::PENDING;
    }

    public function isAccepted(): bool
    {
        return $this->status === EventInvitationStatusEnum::ACCEPTED;
    }

    public function isDeclined(): bool
    {
        return $this->status === EventInvitationStatusEnum::DECLINED;
    }

    public function accept(): self
    {
        $this->status = EventInvitationStatusEnum::ACCEPTED;
        $this->resolvedAt = new CarbonImmutable();

        return $this;
    }

    public function decline(): self
    {
        $this->status = EventInvitationStatusEnum::DECLINED;
        $this->resolvedAt = new CarbonImmutable();

        return $this;
    }

    /**
     * Get the target user - either the direct targetUser or via targetEmail/targetPhone ownership.
     */
    public function getResolvedTargetUser(): null|User
    {
        if ($this->targetUser !== null) {
            return $this->targetUser;
        }

        if ($this->targetEmail?->getOwner() !== null) {
            return $this->targetEmail->getOwner();
        }

        if ($this->targetPhone?->getOwner() !== null) {
            return $this->targetPhone->getOwner();
        }

        return null;
    }
}

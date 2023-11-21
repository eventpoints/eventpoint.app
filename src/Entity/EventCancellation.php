<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Event\Event;
use App\Enum\EventCancellationReasonEnum;
use App\Repository\EventCancellationRepository;
use Carbon\CarbonImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: EventCancellationRepository::class)]
class EventCancellation
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\CustomIdGenerator(UuidGenerator::class)]
    private Uuid $id;

    #[ORM\OneToOne(inversedBy: 'eventCancellation', cascade: ['persist', 'remove'])]
    private null|Event $event = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private null|CarbonImmutable $createdAt = null;

    #[ORM\Column(length: 255, enumType: EventCancellationReasonEnum::class)]
    private null|EventCancellationReasonEnum $reason = null;

    #[ORM\Column(length: 500, nullable: true)]
    private null|string $notice = null;

    #[ORM\ManyToOne(inversedBy: 'eventCancellations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $owner = null;

    public function __construct()
    {
        $this->createdAt = new CarbonImmutable();
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

    public function getCreatedAt(): null|CarbonImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(CarbonImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getReason(): null|EventCancellationReasonEnum
    {
        return $this->reason;
    }

    public function setReason(null|EventCancellationReasonEnum $reason): static
    {
        $this->reason = $reason;

        return $this;
    }

    public function getNotice(): ?string
    {
        return $this->notice;
    }

    public function setNotice(?string $notice): static
    {
        $this->notice = $notice;

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
}

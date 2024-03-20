<?php

namespace App\Entity\Event;

use App\Enum\EventMomentTypeEnum;
use App\Repository\Event\EventEventMomentRepository;
use Carbon\CarbonImmutable;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: EventEventMomentRepository::class)]
class EventMoment
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\CustomIdGenerator(UuidGenerator::class)]
    private Uuid $id;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private null|CarbonImmutable|DateTimeImmutable $createdAt = null;

    public function __construct(
        #[ORM\ManyToOne(inversedBy: 'eventChangeLogs')]
        private ?Event $event,
        #[ORM\Column(length: 255, enumType: EventMomentTypeEnum::class)]
        private null|EventMomentTypeEnum $type,
        #[ORM\Column(length: 255, nullable: true)]
        private null|string $oldValue,
        #[ORM\Column(length: 255, nullable: true)]
        private null|string $newValue,
    ) {
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

    public function getType(): null|EventMomentTypeEnum
    {
        return $this->type;
    }

    public function setType(null|EventMomentTypeEnum $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getCreatedAt(): null|CarbonImmutable|DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(null|CarbonImmutable|DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getOldValue(): ?string
    {
        return $this->oldValue;
    }

    public function setOldValue(?string $oldValue): void
    {
        $this->oldValue = $oldValue;
    }

    public function getNewValue(): ?string
    {
        return $this->newValue;
    }

    public function setNewValue(?string $newValue): void
    {
        $this->newValue = $newValue;
    }
}

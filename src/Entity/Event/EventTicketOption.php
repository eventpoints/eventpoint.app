<?php

namespace App\Entity\Event;

use App\Entity\Embeddable\Money;
use App\Repository\Event\EventTicketOptionRepository;
use Carbon\CarbonImmutable;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: EventTicketOptionRepository::class)]
class EventTicketOption
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\CustomIdGenerator(UuidGenerator::class)]
    private Uuid $id;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private null|CarbonImmutable|DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private null|int $quantityAvailable = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(nullable: true)]
    private ?int $maxPerOrder = null;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $isEnabled = true;

    #[ORM\Embedded(class: Money::class, columnPrefix: 'price_')]
    private Money $price;

    public function __construct(
        #[ORM\ManyToOne(inversedBy: 'ticketOptions')]
        private ?Event $event = null,
        #[ORM\Column(length: 255)]
        private ?string $title = null,
    ) {
        $this->price = new Money();
        $this->createdAt = new DateTimeImmutable();
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

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getPrice(): Money
    {
        return $this->price;
    }

    public function setPrice(Money $price): static
    {
        $this->price = $price;

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

    public function getQuantityAvailable(): ?int
    {
        return $this->quantityAvailable;
    }

    public function setQuantityAvailable(int $quantityAvailable): static
    {
        $this->quantityAvailable = $quantityAvailable;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getMaxPerOrder(): ?int
    {
        return $this->maxPerOrder;
    }

    public function setMaxPerOrder(?int $maxPerOrder): static
    {
        $this->maxPerOrder = $maxPerOrder;

        return $this;
    }

    public function isEnabled(): bool
    {
        return $this->isEnabled;
    }

    public function setIsEnabled(bool $isEnabled): static
    {
        $this->isEnabled = $isEnabled;

        return $this;
    }
}

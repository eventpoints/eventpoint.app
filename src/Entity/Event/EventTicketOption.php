<?php

namespace App\Entity\Event;

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

    #[ORM\Column]
    private ?int $quantityAvailable = null;

    public function __construct(
        #[ORM\ManyToOne(inversedBy: 'ticketOptions')]
        private ?Event $event = null,
        #[ORM\Column(length: 255)]
        private ?string $title = null,
        #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
        private ?string $price = null,
        #[ORM\Column(length: 3)]
        private ?string $currency = null
    ) {
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

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(?string $price): static
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

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): static
    {
        $this->currency = $currency;

        return $this;
    }
}

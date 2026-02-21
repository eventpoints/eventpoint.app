<?php

declare(strict_types=1);

namespace App\Entity\Ticketing;

use App\Entity\Embeddable\Money;
use App\Entity\Event\EventTicketOption;
use App\Repository\Ticketing\OrderLineRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: OrderLineRepository::class)]
class OrderLine
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\CustomIdGenerator(UuidGenerator::class)]
    private Uuid $id;

    #[ORM\ManyToOne(inversedBy: 'orderLines')]
    #[ORM\JoinColumn(nullable: false)]
    private Order $order;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private EventTicketOption $ticketOption;

    #[ORM\Column(type: Types::INTEGER)]
    private int $quantity;

    #[ORM\Embedded(class: Money::class, columnPrefix: 'unit_price_')]
    private Money $unitPrice;

    public function __construct(
        Order $order,
        EventTicketOption $ticketOption,
        int $quantity,
        Money $unitPrice,
    ) {
        $this->order = $order;
        $this->ticketOption = $ticketOption;
        $this->quantity = $quantity;
        $this->unitPrice = $unitPrice;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getOrder(): Order
    {
        return $this->order;
    }

    public function setOrder(Order $order): static
    {
        $this->order = $order;
        return $this;
    }

    public function getTicketOption(): EventTicketOption
    {
        return $this->ticketOption;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;
        return $this;
    }

    public function getUnitPrice(): Money
    {
        return $this->unitPrice;
    }

    public function getLineTotal(): Money
    {
        return new Money(
            $this->unitPrice->getAmount() !== null ? $this->unitPrice->getAmount() * $this->quantity : null,
            $this->unitPrice->getCurrency(),
        );
    }
}

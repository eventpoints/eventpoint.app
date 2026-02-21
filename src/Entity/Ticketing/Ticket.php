<?php

declare(strict_types=1);

namespace App\Entity\Ticketing;

use App\Enum\TicketStatusEnum;
use App\Repository\Ticketing\TicketRepository;
use Carbon\CarbonImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: TicketRepository::class)]
class Ticket
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\CustomIdGenerator(UuidGenerator::class)]
    private Uuid $id;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private OrderLine $orderLine;

    #[ORM\Column(length: 64, unique: true)]
    private string $referenceCode;

    #[ORM\Column(length: 20, enumType: TicketStatusEnum::class)]
    private TicketStatusEnum $status = TicketStatusEnum::VALID;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private CarbonImmutable $issuedAt;

    public function __construct(OrderLine $orderLine)
    {
        $this->orderLine = $orderLine;
        $this->referenceCode = strtoupper(substr(str_replace('-', '', Uuid::v4()->toRfc4122()), 0, 10));
        $this->issuedAt = CarbonImmutable::now();
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getOrderLine(): OrderLine
    {
        return $this->orderLine;
    }

    public function getReferenceCode(): string
    {
        return $this->referenceCode;
    }

    public function getStatus(): TicketStatusEnum
    {
        return $this->status;
    }

    public function setStatus(TicketStatusEnum $status): static
    {
        $this->status = $status;
        return $this;
    }

    public function getIssuedAt(): CarbonImmutable
    {
        return $this->issuedAt;
    }
}

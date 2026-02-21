<?php

declare(strict_types=1);

namespace App\Entity\Ticketing;

use App\Repository\Ticketing\StripeWebhookEventRepository;
use Carbon\CarbonImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: StripeWebhookEventRepository::class)]
class StripeWebhookEvent
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\CustomIdGenerator(UuidGenerator::class)]
    private Uuid $id;

    #[ORM\Column(length: 255, unique: true)]
    private string $stripeEventId;

    #[ORM\Column(length: 100)]
    private string $type;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?CarbonImmutable $processedAt = null;

    #[ORM\Column(length: 20)]
    private string $status = 'received';

    public function __construct(string $stripeEventId, string $type)
    {
        $this->stripeEventId = $stripeEventId;
        $this->type = $type;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getStripeEventId(): string
    {
        return $this->stripeEventId;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getProcessedAt(): ?CarbonImmutable
    {
        return $this->processedAt;
    }

    public function setProcessedAt(?CarbonImmutable $processedAt): static
    {
        $this->processedAt = $processedAt;
        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;
        return $this;
    }
}

<?php

declare(strict_types=1);

namespace App\Entity\Event;

use App\Contract\EventableInterface;
use App\Enum\ExternalEventStatusEnum;
use App\Repository\Event\ExternalEventRepository;
use Carbon\CarbonImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: ExternalEventRepository::class)]
#[ORM\UniqueConstraint(name: 'uq_external_event_source', columns: ['source_name', 'external_id'])]
class ExternalEvent implements EventableInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\CustomIdGenerator(UuidGenerator::class)]
    private Uuid $id;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?CarbonImmutable $endAt = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $venueName = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $address = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 7, nullable: true)]
    private ?string $latitude = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 7, nullable: true)]
    private ?string $longitude = null;

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $imageUrl = null;

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $ticketUrl = null;

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $categories = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $externalId = null;

    #[ORM\Column(length: 30, enumType: ExternalEventStatusEnum::class, options: [
        'default' => ExternalEventStatusEnum::PENDING->value,
    ])]
    private ExternalEventStatusEnum $status = ExternalEventStatusEnum::PENDING;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private CarbonImmutable $createdAt;

    public function __construct(
        #[ORM\Column(length: 255)]
        private string $title,
        #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
        private CarbonImmutable $startAt,
        #[ORM\Column(length: 255)]
        private string $sourceName,
        #[ORM\Column(length: 500)]
        private string $sourceUrl,
        #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
        private CarbonImmutable $scrapedAt,
    ) {
        $this->createdAt = new CarbonImmutable();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    #[\Override]
    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    #[\Override]
    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    #[\Override]
    public function getStartAt(): CarbonImmutable
    {
        return $this->startAt;
    }

    public function setStartAt(CarbonImmutable $startAt): static
    {
        $this->startAt = $startAt;

        return $this;
    }

    #[\Override]
    public function getEndAt(): ?CarbonImmutable
    {
        return $this->endAt;
    }

    public function setEndAt(?CarbonImmutable $endAt): static
    {
        $this->endAt = $endAt;

        return $this;
    }

    #[\Override]
    public function getVenueName(): ?string
    {
        return $this->venueName;
    }

    public function setVenueName(?string $venueName): static
    {
        $this->venueName = $venueName;

        return $this;
    }

    #[\Override]
    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): static
    {
        $this->address = $address;

        return $this;
    }

    #[\Override]
    public function getLatitude(): ?string
    {
        return $this->latitude;
    }

    public function setLatitude(?string $latitude): static
    {
        $this->latitude = $latitude;

        return $this;
    }

    #[\Override]
    public function getLongitude(): ?string
    {
        return $this->longitude;
    }

    public function setLongitude(?string $longitude): static
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }

    public function setImageUrl(?string $imageUrl): static
    {
        $this->imageUrl = $imageUrl;

        return $this;
    }

    public function getTicketUrl(): ?string
    {
        return $this->ticketUrl;
    }

    public function setTicketUrl(?string $ticketUrl): static
    {
        $this->ticketUrl = $ticketUrl;

        return $this;
    }

    public function getCategories(): ?string
    {
        return $this->categories;
    }

    public function setCategories(?string $categories): static
    {
        $this->categories = $categories;

        return $this;
    }

    public function getSourceName(): string
    {
        return $this->sourceName;
    }

    public function setSourceName(string $sourceName): static
    {
        $this->sourceName = $sourceName;

        return $this;
    }

    public function getSourceUrl(): string
    {
        return $this->sourceUrl;
    }

    public function setSourceUrl(string $sourceUrl): static
    {
        $this->sourceUrl = $sourceUrl;

        return $this;
    }

    public function getExternalId(): ?string
    {
        return $this->externalId;
    }

    public function setExternalId(?string $externalId): static
    {
        $this->externalId = $externalId;

        return $this;
    }

    public function getScrapedAt(): CarbonImmutable
    {
        return $this->scrapedAt;
    }

    public function setScrapedAt(CarbonImmutable $scrapedAt): static
    {
        $this->scrapedAt = $scrapedAt;

        return $this;
    }

    public function getStatus(): ExternalEventStatusEnum
    {
        return $this->status;
    }

    public function setStatus(ExternalEventStatusEnum $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getCreatedAt(): CarbonImmutable
    {
        return $this->createdAt;
    }
}

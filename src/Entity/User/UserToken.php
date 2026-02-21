<?php

declare(strict_types=1);

namespace App\Entity\User;

use App\Enum\UserTokenPurposeEnum;
use App\Repository\User\UserTokenRepository;
use Carbon\CarbonImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: UserTokenRepository::class)]
class UserToken
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(UuidGenerator::class)]
    private ?Uuid $id = null;

    #[ORM\Column(type: 'uuid', unique: true)]
    private Uuid $value;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private CarbonImmutable $createdAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?CarbonImmutable $consumedAt = null;

    public function __construct(
        #[ORM\ManyToOne(targetEntity: User::class)]
        #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
        private User $owner,
        #[ORM\Column(enumType: UserTokenPurposeEnum::class)]
        private UserTokenPurposeEnum $purpose,
        #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
        private CarbonImmutable $expiresAt,
    ) {
        $this->value = Uuid::v7();
        $this->createdAt = CarbonImmutable::now();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getOwner(): User
    {
        return $this->owner;
    }

    public function getPurpose(): UserTokenPurposeEnum
    {
        return $this->purpose;
    }

    public function getValue(): Uuid
    {
        return $this->value;
    }

    public function getExpiresAt(): CarbonImmutable
    {
        return $this->expiresAt;
    }

    public function getConsumedAt(): ?CarbonImmutable
    {
        return $this->consumedAt;
    }

    public function isExpired(): bool
    {
        return CarbonImmutable::now() >= $this->expiresAt;
    }

    public function isConsumed(): bool
    {
        return $this->consumedAt instanceof CarbonImmutable;
    }

    public function isActive(): bool
    {
        return !$this->isExpired() && !$this->isConsumed();
    }

    public function consume(): void
    {
        $this->consumedAt = CarbonImmutable::now();
    }

    public function getCreatedAt(): CarbonImmutable
    {
        return $this->createdAt;
    }

    public function getHoursUntilExpiry(): int
    {
        return (int) round($this->expiresAt->diffInHours(CarbonImmutable::now(timezone: 'UTC'), true), 0);
    }
}

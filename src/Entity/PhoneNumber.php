<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\PhoneNumberRepository;
use Carbon\CarbonImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: PhoneNumberRepository::class)]
class PhoneNumber
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\CustomIdGenerator(UuidGenerator::class)]
    private Uuid $id;

    #[ORM\Column(length: 5)]
    private ?string $code = null;

    #[ORM\Column(length: 50)]
    private ?string $number = null;

    #[ORM\ManyToOne(inversedBy: 'phoneNumbers')]
    private ?User $owner = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private CarbonImmutable|null $createdAt = null;

    public function __construct()
    {
        $this->createdAt = new CarbonImmutable();
    }

    public function getId(): null|Uuid
    {
        return $this->id;
    }

    public function getCode(): null|string
    {
        return $this->code;
    }

    public function setCode(null|string $code): static
    {
        $this->code = $code;

        return $this;
    }

    public function getNumber(): null|string
    {
        return $this->number;
    }

    public function getPhoneNumberWithCode(): null|string
    {
        return $this->code . $this->number;
    }

    public function setNumber(null|string $number): static
    {
        $this->number = $number;

        return $this;
    }

    public function getOwner(): null|User
    {
        return $this->owner;
    }

    public function setOwner(null|User $owner): static
    {
        $this->owner = $owner;

        return $this;
    }

    public function getCreatedAt(): null|CarbonImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(null|CarbonImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}

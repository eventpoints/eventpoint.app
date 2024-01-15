<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\UserContactRepository;
use Carbon\CarbonImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: UserContactRepository::class)]
class UserContact
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\CustomIdGenerator(UuidGenerator::class)]
    #[Groups(['user_contact'])]
    private Uuid $id;

    #[ORM\ManyToOne(cascade: ['persist'])]
    #[Groups(['user_contact'])]
    private null|Email $email = null;

    #[ORM\ManyToOne(inversedBy: 'contacts')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['user_contact'])]
    private null|User $owner = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    #[Groups(['user_contact'])]
    private null|CarbonImmutable $createdAt = null;

    public function __construct()
    {
        $this->createdAt = new CarbonImmutable();
    }

    public function getId(): null|Uuid
    {
        return $this->id;
    }

    public function getEmail(): null|Email
    {
        return $this->email;
    }

    public function setEmail(null|Email $email): static
    {
        $this->email = $email;

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

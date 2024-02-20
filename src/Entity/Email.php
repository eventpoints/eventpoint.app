<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\EmailRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: EmailRepository::class)]
#[UniqueEntity(fields: ['address'], message: self::DUPLICATE_EMAIL_ADDRESS)]
class Email implements \Stringable
{
    final public const DUPLICATE_EMAIL_ADDRESS = 'There is already an account with this email address';

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\CustomIdGenerator(UuidGenerator::class)]
    #[Groups(['user_contact'])]
    private Uuid $id;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\Email]
    private string $address;

    #[ORM\ManyToOne(inversedBy: 'emails')]
    #[ORM\JoinColumn(nullable: true)]
    private ?User $owner = null;

    public function __toString(): string
    {
        return $this->address;
    }

    public function getId(): null|Uuid
    {
        return $this->id;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function setAddress(null|string $address): static
    {
        $this->address = $address;

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
}

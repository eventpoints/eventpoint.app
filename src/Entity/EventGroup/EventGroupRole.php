<?php

declare(strict_types=1);

namespace App\Entity\EventGroup;

use App\Entity\Contract\StaticEntityInterface;
use App\Enum\EventGroupRoleEnum;
use App\Repository\EventGroupRoleRepository;
use Doctrine\ORM\Mapping as ORM;
use Stringable;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: EventGroupRoleRepository::class)]
class EventGroupRole implements Stringable, StaticEntityInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\CustomIdGenerator(UuidGenerator::class)]
    private Uuid $id;

    #[ORM\Column(length: 255, enumType: EventGroupRoleEnum::class)]
    private null|EventGroupRoleEnum $title = null;

    public function __toString(): string
    {
        return $this->title->value;
    }

    public function getId(): null|Uuid
    {
        return $this->id;
    }

    public function getTitle(): null|EventGroupRoleEnum
    {
        return $this->title;
    }

    public function setTitle(null|EventGroupRoleEnum $title): static
    {
        $this->title = $title;

        return $this;
    }
}

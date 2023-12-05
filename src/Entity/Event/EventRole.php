<?php

declare(strict_types=1);

namespace App\Entity\Event;

use App\Entity\Contract\StaticEntityInterface;
use App\Enum\EventOrganiserRoleEnum;
use App\Repository\Event\EventRoleRepository;
use Doctrine\ORM\Mapping as ORM;
use Stringable;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: EventRoleRepository::class)]
class EventRole implements Stringable, StaticEntityInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\CustomIdGenerator(UuidGenerator::class)]
    private Uuid $id;

    #[ORM\ManyToOne(inversedBy: 'roles')]
    private ?EventOrganiser $eventOrganiser = null;

    #[ORM\Column(length: 255, enumType: EventOrganiserRoleEnum::class)]
    private null|EventOrganiserRoleEnum $title = null;

    public function __toString(): string
    {
        return $this->title->value;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getTitle(): null|EventOrganiserRoleEnum
    {
        return $this->title;
    }

    public function setTitle(null|EventOrganiserRoleEnum $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getEventOrganiser(): ?EventOrganiser
    {
        return $this->eventOrganiser;
    }

    public function setEventOrganiser(?EventOrganiser $eventOrganiser): static
    {
        $this->eventOrganiser = $eventOrganiser;

        return $this;
    }
}

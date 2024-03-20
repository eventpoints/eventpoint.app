<?php

declare(strict_types=1);

namespace App\Entity\EventGroup;

use App\Entity\User\User;
use App\Repository\EventGroup\EventGroupDiscussionRepository;
use Carbon\CarbonImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: EventGroupDiscussionRepository::class)]
class EventGroupDiscussion
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\CustomIdGenerator(UuidGenerator::class)]
    private Uuid $id;

    #[ORM\Column(length: 255)]
    private null|string $agenda = null;

    #[ORM\Column]
    private null|bool $isResolved = false;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private CarbonImmutable $createdAt;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $owner = null;

    #[ORM\OneToMany(mappedBy: 'discussion', targetEntity: EventGroupDiscussionComment::class, cascade: ['persist', 'remove'])]
    private Collection $eventDiscussionComments;

    #[ORM\ManyToOne(inversedBy: 'eventGroupDiscussions')]
    private ?EventGroup $eventGroup = null;

    public function __construct()
    {
        $this->eventDiscussionComments = new ArrayCollection();
        $this->createdAt = new CarbonImmutable();
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getAgenda(): null|string
    {
        return $this->agenda;
    }

    public function setAgenda(null|string $agenda): static
    {
        $this->agenda = $agenda;

        return $this;
    }

    public function isIsResolved(): null|bool
    {
        return $this->isResolved;
    }

    public function setIsResolved(null|bool $isResolved): static
    {
        $this->isResolved = $isResolved;

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

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): static
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * @return Collection<int, EventGroupDiscussionComment>
     */
    public function getEventDiscussionComments(): Collection
    {
        return $this->eventDiscussionComments;
    }

    public function addComment(EventGroupDiscussionComment $comment): static
    {
        if (! $this->eventDiscussionComments->contains($comment)) {
            $this->eventDiscussionComments->add($comment);
            $comment->setDiscussion($this);
        }

        return $this;
    }

    public function removeComment(EventGroupDiscussionComment $comment): static
    {
        if ($this->eventDiscussionComments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getDiscussion() === $this) {
                $comment->setDiscussion(null);
            }
        }

        return $this;
    }

    public function getEventGroup(): ?EventGroup
    {
        return $this->eventGroup;
    }

    public function setEventGroup(?EventGroup $eventGroup): static
    {
        $this->eventGroup = $eventGroup;

        return $this;
    }
}

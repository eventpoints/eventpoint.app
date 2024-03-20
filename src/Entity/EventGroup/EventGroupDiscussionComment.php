<?php

declare(strict_types=1);

namespace App\Entity\EventGroup;

use App\Entity\User\User;
use App\Enum\VoteEnum;
use App\Repository\EventGroup\EventGroupDiscussionCommentRepository;
use Carbon\CarbonImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: EventGroupDiscussionCommentRepository::class)]
class EventGroupDiscussionComment
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\CustomIdGenerator(UuidGenerator::class)]
    private Uuid $id;

    #[ORM\Column(type: Types::TEXT)]
    private null|string $content = null;

    #[ORM\ManyToOne(inversedBy: 'eventDiscussionComments')]
    private ?User $owner = null;

    #[ORM\ManyToOne(inversedBy: 'comments')]
    private ?EventGroupDiscussion $discussion = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private null|CarbonImmutable $createdAt = null;

    #[ORM\OneToMany(mappedBy: 'discussionComment', targetEntity: EventGroupDiscussionCommentVote::class)]
    private Collection $eventDiscussionCommentVotes;

    public function __construct()
    {
        $this->createdAt = new CarbonImmutable();
        $this->eventDiscussionCommentVotes = new ArrayCollection();
    }

    public function getId(): null|Uuid
    {
        return $this->id;
    }

    public function getContent(): null|string
    {
        return $this->content;
    }

    public function setContent(null|string $content): static
    {
        $this->content = $content;

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

    public function getDiscussion(): ?EventGroupDiscussion
    {
        return $this->discussion;
    }

    public function setDiscussion(?EventGroupDiscussion $discussion): static
    {
        $this->discussion = $discussion;

        return $this;
    }

    public function getCreatedAt(): null|CarbonImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(CarbonImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * @return Collection<int, EventGroupDiscussionCommentVote>
     */
    public function getEventDiscussionCommentVotes(): Collection
    {
        return $this->eventDiscussionCommentVotes;
    }

    public function addEventDiscussionCommentVote(EventGroupDiscussionCommentVote $eventDiscussionCommentVote): static
    {
        if (! $this->eventDiscussionCommentVotes->contains($eventDiscussionCommentVote)) {
            $this->eventDiscussionCommentVotes->add($eventDiscussionCommentVote);
            $eventDiscussionCommentVote->setDiscussionComment($this);
        }

        return $this;
    }

    public function removeEventDiscussionCommentVote(EventGroupDiscussionCommentVote $eventDiscussionCommentVote): static
    {
        if ($this->eventDiscussionCommentVotes->removeElement($eventDiscussionCommentVote)) {
            // set the owning side to null (unless already changed)
            if ($eventDiscussionCommentVote->getDiscussionComment() === $this) {
                $eventDiscussionCommentVote->setDiscussionComment(null);
            }
        }

        return $this;
    }

    /**
     * @return ArrayCollection<int, EventGroupDiscussionCommentVote>
     */
    public function getUpVotes(): ArrayCollection
    {
        return $this->eventDiscussionCommentVotes->filter(fn (EventGroupDiscussionCommentVote $commentVote) => $commentVote->getType() === VoteEnum::VOTE_UP);
    }

    /**
     * @return ArrayCollection<int, EventGroupDiscussionCommentVote>
     */
    public function getDownVotes(): ArrayCollection
    {
        return $this->eventDiscussionCommentVotes->filter(fn (EventGroupDiscussionCommentVote $commentVote) => $commentVote->getType() === VoteEnum::VOTE_DOWN);
    }
}

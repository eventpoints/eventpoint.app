<?php

declare(strict_types=1);

namespace App\Entity;

use App\Enum\VoteEnum;
use App\Repository\EventDiscussionCommentVoteRepository;
use Carbon\CarbonImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: EventDiscussionCommentVoteRepository::class)]
class EventDiscussionCommentVote
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\CustomIdGenerator(UuidGenerator::class)]
    private Uuid $id;

    #[ORM\ManyToOne(inversedBy: 'eventDiscussionCommentVotes')]
    private null|EventDiscussionComment $discussionComment = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private null|CarbonImmutable $createdAt = null;

    #[ORM\Column(length: 255, enumType: VoteEnum::class)]
    private null|VoteEnum $type = null;

    #[ORM\ManyToOne(inversedBy: 'eventDiscussionCommentVotes')]
    private ?User $owner = null;

    public function __construct()
    {
        $this->createdAt = new CarbonImmutable();
    }

    public function getId(): null|Uuid
    {
        return $this->id;
    }

    public function getDiscussionComment(): null|EventDiscussionComment
    {
        return $this->discussionComment;
    }

    public function setDiscussionComment(null|EventDiscussionComment $discussionComment): static
    {
        $this->discussionComment = $discussionComment;

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

    public function getType(): null|VoteEnum
    {
        return $this->type;
    }

    public function setType(null|VoteEnum $type): static
    {
        $this->type = $type;

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
}

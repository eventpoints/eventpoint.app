<?php

declare(strict_types=1);

namespace App\Entity\Poll;

use App\Entity\User\User;
use App\Repository\Poll\PollAnswerRepository;
use Carbon\CarbonImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: PollAnswerRepository::class)]
class PollAnswer
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\CustomIdGenerator(UuidGenerator::class)]
    private Uuid $id;

    #[ORM\ManyToOne(inversedBy: 'pollAnswers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?PollOption $pollOption = null;

    #[ORM\ManyToOne(inversedBy: 'pollAnswers')]
    private ?Poll $poll = null;

    #[ORM\ManyToOne(inversedBy: 'pollAnswers')]
    private ?User $owner = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private null|CarbonImmutable $createdAt = null;

    public function __construct()
    {
        $this->createdAt = new CarbonImmutable();
    }

    public function getId(): null|Uuid
    {
        return $this->id;
    }

    public function getPollOption(): ?PollOption
    {
        return $this->pollOption;
    }

    public function setPollOption(?PollOption $pollOption): static
    {
        $this->pollOption = $pollOption;

        return $this;
    }

    public function getPoll(): ?Poll
    {
        return $this->poll;
    }

    public function setPoll(?Poll $poll): static
    {
        $this->poll = $poll;

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

    public function calculateOptionPercentages(PollOption $pollOption): float
    {
        $totalVotes = $this->getPoll()->getPollAnswers()->count();

        $pollAnswers = $pollOption->getPollAnswers()->filter(fn (self $pollAnswer) => $pollAnswer->getPollOption() === $pollOption);

        return $totalVotes > 0 ? round($pollAnswers->count() / $totalVotes * 100, 2) : 0;
    }
}

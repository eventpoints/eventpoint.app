<?php

declare(strict_types=1);

namespace App\Entity\Poll;

use App\Entity\EventGroup\EventGroup;
use App\Entity\User;
use App\Repository\PollRepository;
use Carbon\CarbonImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PollRepository::class)]
class Poll
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\CustomIdGenerator(UuidGenerator::class)]
    private Uuid $id;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $prompt = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private null|CarbonImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'polls')]
    private ?EventGroup $eventGroup = null;

    #[ORM\OneToMany(mappedBy: 'poll', targetEntity: PollOption::class, cascade: ['persist'])]
    #[Assert\Count(min: 2, max: 5)]
    private Collection $pollOptions;

    #[ORM\OneToMany(mappedBy: 'poll', targetEntity: PollAnswer::class, cascade: ['persist'])]
    private Collection $pollAnswers;

    #[ORM\ManyToOne(inversedBy: 'polls')]
    private null|User $owner = null;

    public function __construct()
    {
        $this->pollOptions = new ArrayCollection();
        $this->pollAnswers = new ArrayCollection();
        $this->createdAt = new CarbonImmutable();
    }

    public function getId(): null|Uuid
    {
        return $this->id;
    }

    public function getPrompt(): null|string
    {
        return $this->prompt;
    }

    public function setPrompt(null|string $prompt): static
    {
        $this->prompt = $prompt;

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

    public function getEventGroup(): ?EventGroup
    {
        return $this->eventGroup;
    }

    public function setEventGroup(?EventGroup $eventGroup): static
    {
        $this->eventGroup = $eventGroup;

        return $this;
    }

    /**
     * @return Collection<int, PollOption>
     */
    public function getPollOptions(): Collection
    {
        return $this->pollOptions;
    }

    public function addPollOption(PollOption $pollOption): static
    {
        if (! $this->pollOptions->contains($pollOption)) {
            $this->pollOptions->add($pollOption);
            $pollOption->setPoll($this);
        }

        return $this;
    }

    public function removePollOption(PollOption $pollOption): static
    {
        if ($this->pollOptions->removeElement($pollOption)) {
            // set the owning side to null (unless already changed)
            if ($pollOption->getPoll() === $this) {
                $pollOption->setPoll(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, PollAnswer>
     */
    public function getPollAnswers(): Collection
    {
        return $this->pollAnswers;
    }

    public function addPollAnswer(PollAnswer $pollAnswer): static
    {
        if (! $this->pollAnswers->contains($pollAnswer)) {
            $this->pollAnswers->add($pollAnswer);
            $pollAnswer->setPoll($this);
        }

        return $this;
    }

    public function removePollAnswer(PollAnswer $pollAnswer): static
    {
        if ($this->pollAnswers->removeElement($pollAnswer)) {
            // set the owning side to null (unless already changed)
            if ($pollAnswer->getPoll() === $this) {
                $pollAnswer->setPoll(null);
            }
        }

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

    public function hasUserAnswered(User $user): bool
    {
        return $this->getPollAnswers()->exists(fn (int $key, PollAnswer $pollAnswer) => $pollAnswer->getOwner() === $user);
    }

    public function getUserAnswer(User $user): null|PollAnswer
    {
        return $this->getPollAnswers()->findFirst(fn (int $key, PollAnswer $pollAnswer) => $pollAnswer->getOwner() === $user);
    }
}

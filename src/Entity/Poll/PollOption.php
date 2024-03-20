<?php

declare(strict_types=1);

namespace App\Entity\Poll;

use App\Repository\Poll\PollOptionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PollOptionRepository::class)]
class PollOption
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\CustomIdGenerator(UuidGenerator::class)]
    private Uuid $id;

    #[ORM\ManyToOne(inversedBy: 'pollOptions')]
    private ?Poll $poll = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private null|string $content = null;

    #[ORM\OneToMany(mappedBy: 'pollOption', targetEntity: PollAnswer::class, orphanRemoval: true)]
    private Collection $pollAnswers;

    public function __construct()
    {
        $this->pollAnswers = new ArrayCollection();
    }

    public function getId(): null|Uuid
    {
        return $this->id;
    }

    public function getPoll(): null|Poll
    {
        return $this->poll;
    }

    public function setPoll(?Poll $poll): static
    {
        $this->poll = $poll;

        return $this;
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
            $pollAnswer->setPollOption($this);
        }

        return $this;
    }

    public function removePollAnswer(PollAnswer $pollAnswer): static
    {
        if ($this->pollAnswers->removeElement($pollAnswer)) {
            // set the owning side to null (unless already changed)
            if ($pollAnswer->getPollOption() === $this) {
                $pollAnswer->setPollOption(null);
            }
        }

        return $this;
    }
}

<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ConversationRepository;
use Carbon\CarbonImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: ConversationRepository::class)]
class Conversation
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\CustomIdGenerator(UuidGenerator::class)]
    private Uuid $id;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private null|CarbonImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'authoredConversations')]
    private ?User $owner = null;

    #[ORM\OneToMany(mappedBy: 'conversation', targetEntity: ConversationParticipant::class, cascade: ['persist', 'remove'])]
    private Collection $conversationParticipants;

    #[ORM\OneToMany(mappedBy: 'conversation', targetEntity: Message::class, cascade: ['persist', 'remove'])]
    private Collection $messages;

    public function __construct()
    {
        $this->createdAt = new CarbonImmutable();
        $this->conversationParticipants = new ArrayCollection();
        $this->messages = new ArrayCollection();
    }

    public function getId(): null|Uuid
    {
        return $this->id;
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
     * @return Collection<int, ConversationParticipant>
     */
    public function getConversationParticipants(): Collection
    {
        return $this->conversationParticipants;
    }

    public function addConversationParticipant(ConversationParticipant $conversationParticipant): static
    {
        if (! $this->conversationParticipants->contains($conversationParticipant)) {
            $this->conversationParticipants->add($conversationParticipant);
            $conversationParticipant->setConversation($this);
        }

        return $this;
    }

    public function removeConversationParticipant(ConversationParticipant $conversationParticipant): static
    {
        if ($this->conversationParticipants->removeElement($conversationParticipant)) {
            // set the owning side to null (unless already changed)
            if ($conversationParticipant->getConversation() === $this) {
                $conversationParticipant->setConversation(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Message>
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(Message $message): static
    {
        if (!$this->messages->contains($message)) {
            $this->messages->add($message);
            $message->setConversation($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): static
    {
        if ($this->messages->removeElement($message)) {
            // set the owning side to null (unless already changed)
            if ($message->getConversation() === $this) {
                $message->setConversation(null);
            }
        }

        return $this;
    }
}

<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Contract\UpdatedAtInterface;
use App\Entity\Event\Event;
use App\Entity\Event\EventInvitation;
use App\Entity\Event\EventOrganiser;
use App\Entity\Event\EventRequest;
use App\Entity\EventGroup\EventGroup;
use App\Entity\EventGroup\EventGroupInvitation;
use App\Entity\EventGroup\EventGroupJoinRequest;
use App\Entity\EventGroup\EventGroupMember;
use App\Entity\EventGroup\EventGroupRole;
use App\Entity\Poll\Poll;
use App\Entity\Poll\PollAnswer;
use App\Enum\EventGroupRoleEnum;
use App\Enum\RegionalEnum;
use App\Repository\UserRepository;
use Carbon\CarbonImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Stringable;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface, Stringable, UpdatedAtInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\CustomIdGenerator(UuidGenerator::class)]
    #[Groups(['user_contact'])]
    private Uuid $id;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    /**
     * @var array<string>
     */
    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column(nullable: true)]
    private null|string $password = null;

    #[ORM\Column(type: 'boolean')]
    private bool $isVerified = false;

    #[ORM\Column(type: 'boolean')]
    private bool $isEnabled = false;

    #[ORM\Column(length: 255)]
    private string $firstName;

    #[ORM\Column(length: 255)]
    private string $lastName;

    #[ORM\Column(nullable: true)]
    private null|int $age = null;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: EventOrganiser::class)]
    private Collection $eventOrganisers;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $avatar = null;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: ImageCollection::class)]
    private Collection $imageCollections;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: EventGroup::class)]
    private Collection $eventGroups;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: EventGroupMember::class)]
    private Collection $eventGroupMembers;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: EventRequest::class)]
    private Collection $eventRequests;

    #[ORM\Column(length: 255)]
    private null|string $timezone = RegionalEnum::REGIONAL_TIMEZONE->value;

    #[ORM\Column(length: 3, nullable: true)]
    private ?string $locale = RegionalEnum::REGIONAL_LOCALE->value;

    #[ORM\Column(length: 3, nullable: true)]
    private ?string $currency = RegionalEnum::REGIONAL_CURRECNY->value;

    #[ORM\Column(length: 3, nullable: true)]
    private ?string $country = RegionalEnum::REGIONAL_REGION->value;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: EventInvitation::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $receivedEventInvitations;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: EventInvitation::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $createdEventInvitations;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: EventInvitation::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $createdEmailEventInvitations;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private CarbonImmutable $createdAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private CarbonImmutable $updatedAt;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: Event::class)]
    private Collection $authoredEvents;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: EventDiscussionComment::class)]
    private Collection $eventDiscussionComments;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: EventDiscussionCommentVote::class)]
    private Collection $eventDiscussionCommentVotes;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: Conversation::class)]
    private Collection $authoredConversations;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: ConversationParticipant::class)]
    private Collection $conversationUsers;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: EventCancellation::class)]
    private Collection $eventCancellations;

    /**
     * @var Collection<int, SocialAuth>
     */
    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: SocialAuth::class, cascade: ['persist'])]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private Collection $socialAuths;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: EventOrganiserInvitation::class)]
    private Collection $eventOrganiserInvitations;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: PollAnswer::class)]
    private Collection $pollAnswers;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: Poll::class)]
    private Collection $polls;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: PhoneNumber::class, cascade: ['persist'])]
    private Collection $phoneNumbers;

    #[ORM\ManyToOne(cascade: ['persist'])]
    private null|PhoneNumber $phoneNumber = null;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: EventGroupInvitation::class)]
    private Collection $eventGroupInvitations;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: EventGroupJoinRequest::class)]
    private Collection $eventGroupJoinRequests;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: UserContact::class)]
    private Collection $contacts;

    public function __construct()
    {
        $this->eventRequests = new ArrayCollection();
        $this->eventOrganisers = new ArrayCollection();
        $this->imageCollections = new ArrayCollection();
        $this->eventGroups = new ArrayCollection();
        $this->eventGroupMembers = new ArrayCollection();
        $this->receivedEventInvitations = new ArrayCollection();
        $this->createdEventInvitations = new ArrayCollection();
        $this->createdEmailEventInvitations = new ArrayCollection();
        $this->createdAt = new CarbonImmutable();
        $this->updatedAt = new CarbonImmutable();
        $this->authoredEvents = new ArrayCollection();
        $this->eventDiscussionComments = new ArrayCollection();
        $this->eventDiscussionCommentVotes = new ArrayCollection();
        $this->authoredConversations = new ArrayCollection();
        $this->conversationUsers = new ArrayCollection();
        $this->eventCancellations = new ArrayCollection();
        $this->socialAuths = new ArrayCollection();
        $this->eventOrganiserInvitations = new ArrayCollection();
        $this->pollAnswers = new ArrayCollection();
        $this->polls = new ArrayCollection();
        $this->phoneNumbers = new ArrayCollection();
        $this->eventGroupInvitations = new ArrayCollection();
        $this->eventGroupJoinRequests = new ArrayCollection();
        $this->contacts = new ArrayCollection();
    }

    public function __toString(): string
    {
        return (string) $this->getEmail();
    }

    public function getId(): null|Uuid
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getFullName(): string
    {
        return $this->getFirstName() . ' ' . $this->getLastName();
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param array<string> $roles
     * @return $this
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): null|string
    {
        return $this->password;
    }

    public function setPassword(null|string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getAge(): ?int
    {
        return $this->age;
    }

    public function setAge(int $age): static
    {
        $this->age = $age;

        return $this;
    }

    /**
     * @return Collection<int, EventOrganiser>
     */
    public function getEventOrganisers(): Collection
    {
        return $this->eventOrganisers;
    }

    public function addEventOrganiser(EventOrganiser $eventOrganiser): static
    {
        if (! $this->eventOrganisers->contains($eventOrganiser)) {
            $this->eventOrganisers->add($eventOrganiser);
            $eventOrganiser->setOwner($this);
        }

        return $this;
    }

    public function removeEventOrganiser(EventOrganiser $eventOrganiser): static
    {
        if ($this->eventOrganisers->removeElement($eventOrganiser)) {
            // set the owning side to null (unless already changed)
            if ($eventOrganiser->getOwner() === $this) {
                $eventOrganiser->setOwner(null);
            }
        }

        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(string $avatar): static
    {
        $this->avatar = $avatar;

        return $this;
    }

    /**
     * @return Collection<int, ImageCollection>
     */
    public function getImageCollections(): Collection
    {
        return $this->imageCollections;
    }

    public function addImageCollection(ImageCollection $imageCollection): static
    {
        if (! $this->imageCollections->contains($imageCollection)) {
            $this->imageCollections->add($imageCollection);
            $imageCollection->setOwner($this);
        }

        return $this;
    }

    public function removeImageCollection(ImageCollection $imageCollection): static
    {
        if ($this->imageCollections->removeElement($imageCollection)) {
            // set the owning side to null (unless already changed)
            if ($imageCollection->getOwner() === $this) {
                $imageCollection->setOwner(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, EventGroup>
     */
    public function getEventGroups(): Collection
    {
        return $this->eventGroups;
    }

    public function addEventGroup(EventGroup $eventGroup): static
    {
        if (! $this->eventGroups->contains($eventGroup)) {
            $this->eventGroups->add($eventGroup);
            $eventGroup->setOwner($this);
        }

        return $this;
    }

    public function removeEventGroup(EventGroup $eventGroup): static
    {
        if ($this->eventGroups->removeElement($eventGroup)) {
            // set the owning side to null (unless already changed)
            if ($eventGroup->getOwner() === $this) {
                $eventGroup->setOwner(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, EventGroupMember>
     */
    public function getEventGroupMembers(): Collection
    {
        return $this->eventGroupMembers;
    }

    public function addEventGroupMember(EventGroupMember $eventGroupMember): static
    {
        if (! $this->eventGroupMembers->contains($eventGroupMember)) {
            $this->eventGroupMembers->add($eventGroupMember);
            $eventGroupMember->setOwner($this);
        }

        return $this;
    }

    public function removeEventGroupMember(EventGroupMember $eventGroupMember): static
    {
        if ($this->eventGroupMembers->removeElement($eventGroupMember)) {
            // set the owning side to null (unless already changed)
            if ($eventGroupMember->getOwner() === $this) {
                $eventGroupMember->setOwner(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, EventRequest>
     */
    public function getEventRequests(): Collection
    {
        return $this->eventRequests;
    }

    public function addEventRequest(EventRequest $eventRequest): static
    {
        if (! $this->eventRequests->contains($eventRequest)) {
            $this->eventRequests->add($eventRequest);
            $eventRequest->setOwner($this);
        }

        return $this;
    }

    public function removeEventRequest(EventRequest $eventRequest): static
    {
        $this->eventRequests->removeElement($eventRequest);
        return $this;
    }

    public function getIsEnabled(): bool
    {
        return $this->isEnabled;
    }

    public function setIsEnabled(bool $isEnabled): void
    {
        $this->isEnabled = $isEnabled;
    }

    public function getTimezone(): ?string
    {
        return $this->timezone;
    }

    public function setTimezone(string $timezone): static
    {
        $this->timezone = $timezone;

        return $this;
    }

    public function getLocale(): ?string
    {
        return $this->locale;
    }

    public function setLocale(string $locale): static
    {
        $this->locale = $locale;

        return $this;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): static
    {
        $this->currency = $currency;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): static
    {
        $this->country = $country;

        return $this;
    }

    /**
     * @return Collection|EventInvitation[]
     */
    public function getReceivedEventInvitations(): Collection
    {
        return $this->receivedEventInvitations;
    }

    public function addReceivedEventInvitation(EventInvitation $eventInvitation): self
    {
        if (! $this->receivedEventInvitations->contains($eventInvitation)) {
            $this->receivedEventInvitations[] = $eventInvitation;
            $eventInvitation->setTarget($this);
        }

        return $this;
    }

    public function removeReceivedEventInvitation(EventInvitation $eventInvitation): self
    {
        $this->receivedEventInvitations->removeElement($eventInvitation);
        return $this;
    }

    /**
     * @return Collection|EventInvitation[]
     */
    public function getCreatedEventInvitations(): Collection
    {
        return $this->createdEventInvitations;
    }

    public function addCreatedEventInvitation(EventInvitation $eventInvitation): self
    {
        if (! $this->createdEventInvitations->contains($eventInvitation)) {
            $this->createdEventInvitations[] = $eventInvitation;
            $eventInvitation->setOwner($this);
        }

        return $this;
    }

    public function removeCreatedEventInvitation(EventInvitation $eventInvitation): self
    {
        $this->createdEventInvitations->removeElement($eventInvitation);
        return $this;
    }

    /**
     * @return Collection|EventInvitation[]
     */
    public function getCreatedEmailEventInvitations(): Collection
    {
        return $this->createdEmailEventInvitations;
    }

    public function addCreatedEmailEventInvitation(EventInvitation $eventInvitation): self
    {
        if (! $this->createdEmailEventInvitations->contains($eventInvitation)) {
            $this->createdEmailEventInvitations[] = $eventInvitation;
            $eventInvitation->setOwner($this);
        }

        return $this;
    }

    public function removeCreatedEmailEventInvitation(EventInvitation $eventInvitation): self
    {
        $this->createdEmailEventInvitations->removeElement($eventInvitation);
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

    public function getUpdatedAt(): CarbonImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(CarbonImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return Collection<int, Event>
     */
    public function getAuthoredEvents(): Collection
    {
        return $this->authoredEvents;
    }

    public function addAuthouredEvent(Event $authouredEvent): static
    {
        if (! $this->authoredEvents->contains($authouredEvent)) {
            $this->authoredEvents->add($authouredEvent);
            $authouredEvent->setOwner($this);
        }

        return $this;
    }

    public function removeAuthouredEvent(Event $authouredEvent): static
    {
        if ($this->authoredEvents->removeElement($authouredEvent)) {
            // set the owning side to null (unless already changed)
            if ($authouredEvent->getOwner() === $this) {
                $authouredEvent->setOwner(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, EventDiscussionComment>
     */
    public function getEventDiscussionComments(): Collection
    {
        return $this->eventDiscussionComments;
    }

    public function addEventDiscussionComment(EventDiscussionComment $eventDiscussionComment): static
    {
        if (! $this->eventDiscussionComments->contains($eventDiscussionComment)) {
            $this->eventDiscussionComments->add($eventDiscussionComment);
            $eventDiscussionComment->setOwner($this);
        }

        return $this;
    }

    public function removeEventDiscussionComment(EventDiscussionComment $eventDiscussionComment): static
    {
        if ($this->eventDiscussionComments->removeElement($eventDiscussionComment)) {
            // set the owning side to null (unless already changed)
            if ($eventDiscussionComment->getOwner() === $this) {
                $eventDiscussionComment->setOwner(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, EventDiscussionCommentVote>
     */
    public function getEventDiscussionCommentVotes(): Collection
    {
        return $this->eventDiscussionCommentVotes;
    }

    public function addEventDiscussionCommentVote(EventDiscussionCommentVote $eventDiscussionCommentVote): static
    {
        if (! $this->eventDiscussionCommentVotes->contains($eventDiscussionCommentVote)) {
            $this->eventDiscussionCommentVotes->add($eventDiscussionCommentVote);
            $eventDiscussionCommentVote->setOwner($this);
        }

        return $this;
    }

    public function removeEventDiscussionCommentVote(EventDiscussionCommentVote $eventDiscussionCommentVote): static
    {
        if ($this->eventDiscussionCommentVotes->removeElement($eventDiscussionCommentVote)) {
            // set the owning side to null (unless already changed)
            if ($eventDiscussionCommentVote->getOwner() === $this) {
                $eventDiscussionCommentVote->setOwner(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Conversation>
     */
    public function getAuthoredConversations(): Collection
    {
        return $this->authoredConversations;
    }

    public function addAuthoredConversation(Conversation $authoredConversation): static
    {
        if (! $this->authoredConversations->contains($authoredConversation)) {
            $this->authoredConversations->add($authoredConversation);
            $authoredConversation->setOwner($this);
        }

        return $this;
    }

    public function removeAuthoredConversation(Conversation $authoredConversation): static
    {
        if ($this->authoredConversations->removeElement($authoredConversation)) {
            // set the owning side to null (unless already changed)
            if ($authoredConversation->getOwner() === $this) {
                $authoredConversation->setOwner(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ConversationParticipant>
     */
    public function getConversationUsers(): Collection
    {
        return $this->conversationUsers;
    }

    public function addConversationUser(ConversationParticipant $conversationUser): static
    {
        if (! $this->conversationUsers->contains($conversationUser)) {
            $this->conversationUsers->add($conversationUser);
            $conversationUser->setOwner($this);
        }

        return $this;
    }

    public function removeConversationUser(ConversationParticipant $conversationUser): static
    {
        if ($this->conversationUsers->removeElement($conversationUser)) {
            // set the owning side to null (unless already changed)
            if ($conversationUser->getOwner() === $this) {
                $conversationUser->setOwner(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, EventCancellation>
     */
    public function getEventCancellations(): Collection
    {
        return $this->eventCancellations;
    }

    public function addEventCancellation(EventCancellation $eventCancellation): static
    {
        if (! $this->eventCancellations->contains($eventCancellation)) {
            $this->eventCancellations->add($eventCancellation);
            $eventCancellation->setOwner($this);
        }

        return $this;
    }

    public function removeEventCancellation(EventCancellation $eventCancellation): static
    {
        if ($this->eventCancellations->removeElement($eventCancellation)) {
            // set the owning side to null (unless already changed)
            if ($eventCancellation->getOwner() === $this) {
                $eventCancellation->setOwner(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, SocialAuth>
     */
    public function getSocialAuths(): Collection
    {
        return $this->socialAuths;
    }

    public function addSocialAuth(SocialAuth $socialAuth): self
    {
        if (! $this->socialAuths->contains($socialAuth)) {
            $this->socialAuths[] = $socialAuth;
            $socialAuth->setOwner($this);
        }

        return $this;
    }

    public function removeSocialAuth(SocialAuth $socialAuth): self
    {
        // set the owning side to null (unless already changed)
        if ($this->socialAuths->removeElement($socialAuth) && $socialAuth->getOwner() === $this) {
            $socialAuth->setOwner(null);
        }

        return $this;
    }

    /**
     * @return Collection<int, EventOrganiserInvitation>
     */
    public function getEventOrganiserInvitations(): Collection
    {
        return $this->eventOrganiserInvitations;
    }

    public function addEventOrganiserInvitation(EventOrganiserInvitation $eventOrganiserInvitation): static
    {
        if (! $this->eventOrganiserInvitations->contains($eventOrganiserInvitation)) {
            $this->eventOrganiserInvitations->add($eventOrganiserInvitation);
            $eventOrganiserInvitation->setOwner($this);
        }

        return $this;
    }

    public function removeEventOrganiserInvitation(EventOrganiserInvitation $eventOrganiserInvitation): static
    {
        if ($this->eventOrganiserInvitations->removeElement($eventOrganiserInvitation)) {
            // set the owning side to null (unless already changed)
            if ($eventOrganiserInvitation->getOwner() === $this) {
                $eventOrganiserInvitation->setOwner(null);
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
            $pollAnswer->setOwner($this);
        }

        return $this;
    }

    public function removePollAnswer(PollAnswer $pollAnswer): static
    {
        if ($this->pollAnswers->removeElement($pollAnswer)) {
            // set the owning side to null (unless already changed)
            if ($pollAnswer->getOwner() === $this) {
                $pollAnswer->setOwner(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Poll>
     */
    public function getPolls(): Collection
    {
        return $this->polls;
    }

    public function addPoll(Poll $poll): static
    {
        if (! $this->polls->contains($poll)) {
            $this->polls->add($poll);
            $poll->setOwner($this);
        }

        return $this;
    }

    public function removePoll(Poll $poll): static
    {
        if ($this->polls->removeElement($poll)) {
            // set the owning side to null (unless already changed)
            if ($poll->getOwner() === $this) {
                $poll->setOwner(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, PhoneNumber>
     */
    public function getPhoneNumbers(): Collection
    {
        return $this->phoneNumbers;
    }

    public function addPhoneNumber(PhoneNumber $phoneNumber): static
    {
        if (! $this->phoneNumbers->contains($phoneNumber)) {
            $this->phoneNumbers->add($phoneNumber);
            $phoneNumber->setOwner($this);
        }

        return $this;
    }

    public function removePhoneNumber(PhoneNumber $phoneNumber): static
    {
        if ($this->phoneNumbers->removeElement($phoneNumber)) {
            // set the owning side to null (unless already changed)
            if ($phoneNumber->getOwner() === $this) {
                $phoneNumber->setOwner(null);
            }
        }

        return $this;
    }

    public function getPhoneNumber(): ?PhoneNumber
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(?PhoneNumber $phoneNumber): static
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function groupMemberships(self $user): Collection
    {
        return $this->eventGroupMembers->filter(fn (EventGroupMember $eventGroupMember) => $eventGroupMember->getOwner() === $user && ! $eventGroupMember->isGroupAdmin());
    }

    public function groupsYouManage(self $user): Collection
    {
        return $this->eventGroupMembers->filter(fn (EventGroupMember $eventGroupMember) => $eventGroupMember->getOwner() === $user && $eventGroupMember->isGroupAdmin());
    }

    /**
     * @return Collection<int, EventGroupInvitation>
     */
    public function getEventGroupInvitations(): Collection
    {
        return $this->eventGroupInvitations;
    }

    public function addEventGroupInvitation(EventGroupInvitation $eventGroupInvitation): static
    {
        if (! $this->eventGroupInvitations->contains($eventGroupInvitation)) {
            $this->eventGroupInvitations->add($eventGroupInvitation);
            $eventGroupInvitation->setOwner($this);
        }

        return $this;
    }

    public function removeEventGroupInvitation(EventGroupInvitation $eventGroupInvitation): static
    {
        if ($this->eventGroupInvitations->removeElement($eventGroupInvitation)) {
            // set the owning side to null (unless already changed)
            if ($eventGroupInvitation->getOwner() === $this) {
                $eventGroupInvitation->setOwner(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, EventGroupJoinRequest>
     */
    public function getEventGroupJoinRequests(): Collection
    {
        return $this->eventGroupJoinRequests;
    }

    public function addEventGroupJoinRequest(EventGroupJoinRequest $eventGroupJoinRequest): static
    {
        if (! $this->eventGroupJoinRequests->contains($eventGroupJoinRequest)) {
            $this->eventGroupJoinRequests->add($eventGroupJoinRequest);
            $eventGroupJoinRequest->setOwner($this);
        }

        return $this;
    }

    public function removeEventGroupJoinRequest(EventGroupJoinRequest $eventGroupJoinRequest): static
    {
        if ($this->eventGroupJoinRequests->removeElement($eventGroupJoinRequest)) {
            // set the owning side to null (unless already changed)
            if ($eventGroupJoinRequest->getOwner() === $this) {
                $eventGroupJoinRequest->setOwner(null);
            }
        }

        return $this;
    }

    public function getUserEventGroupRequests(): Collection
    {
        $user = $this;
        return $this->eventGroupJoinRequests->filter(fn (EventGroupJoinRequest $eventGroupJoinRequest) => $eventGroupJoinRequest->getOwner() === $user);
    }

    public function getUserUnansweredEventGroupInvitations(): Collection
    {
        $user = $this;
        return $this->eventGroupInvitations->filter(fn (EventGroupInvitation $eventGroupInvitation) => $eventGroupInvitation->getOwner() === $user && $eventGroupInvitation->getApprovedAt() === null);
    }

    /**
     * @return Collection<int, EventGroup|null>
     */
    public function getUserManagedGroups(): Collection
    {
        return $this->getEventGroupMembers()->filter(fn (EventGroupMember $eventGroupMember) => $eventGroupMember->getRoles()->exists(fn (int $key, EventGroupRole $eventGroupRole) => $eventGroupRole->getTitle() === EventGroupRoleEnum::ROLE_GROUP_MAINTAINER))->map(fn (EventGroupMember $eventGroupMember) => $eventGroupMember->getEventGroup());
    }

    /**
     * @return Collection<int, EventGroup|null>
     */
    public function getUserGroupMemberships(): Collection
    {
        return $this->getEventGroupMembers()->filter(fn (EventGroupMember $eventGroupMember) => $eventGroupMember->getRoles()->exists(fn (int $key, EventGroupRole $eventGroupRole) => $eventGroupRole->getTitle() === EventGroupRoleEnum::ROLE_GROUP_MEMBER))->map(fn (EventGroupMember $eventGroupMember) => $eventGroupMember->getEventGroup());
    }

    /**
     * @return Collection<int, UserContact>
     */
    public function getContacts(): Collection
    {
        return $this->contacts;
    }

    public function addContact(UserContact $contact): static
    {
        if (! $this->contacts->contains($contact)) {
            $this->contacts->add($contact);
            $contact->setOwner($this);
        }

        return $this;
    }

    public function removeContact(UserContact $contact): static
    {
        if ($this->contacts->removeElement($contact)) {
            // set the owning side to null (unless already changed)
            if ($contact->getOwner() === $this) {
                $contact->setOwner(null);
            }
        }

        return $this;
    }
}

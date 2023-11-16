<?php

namespace App\Entity;

use App\Entity\Event\EventInvitation;
use App\Entity\Event\EventOrganiser;
use App\Entity\EventGroup\EventGroup;
use App\Entity\EventGroup\EventGroupMember;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\CustomIdGenerator(UuidGenerator::class)]
    private Uuid $id;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column]
    private null|string $password = null;

    #[ORM\Column(type: 'boolean')]
    private $isVerified = false;

    #[ORM\Column(type: 'boolean')]
    private $isEnabled = false;

    #[ORM\Column(length: 255)]
    private string $firstName;

    #[ORM\Column(length: 255)]
    private string $lastName;

    #[ORM\Column(nullable: true)]
    private null|int $age = null;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: EventOrganiser::class)]
    private Collection $eventOrganisers;

    #[ORM\Column(type: Types::TEXT,nullable: true)]
    private ?string $avatar = null;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: ImageCollection::class)]
    private Collection $imageCollections;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: EventGroup::class)]
    private Collection $eventGroups;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: EventGroupMember::class)]
    private Collection $eventGroupMembers;

    #[ORM\Column(length: 255)]
    private ?string $timezone = 'UTC';

    #[ORM\Column(length: 3, nullable: true)]
    private ?string $locale = 'en';

    #[ORM\Column(length: 3, nullable: true)]
    private ?string $currency = 'CZK';

    #[ORM\Column(length: 3, nullable: true)]
    private ?string $country = null;

    #[ORM\OneToMany(mappedBy: "owner", targetEntity: EventInvitation::class)]
    private Collection $eventInvitations;

    public function __construct()
    {
        $this->eventOrganisers = new ArrayCollection();
        $this->imageCollections = new ArrayCollection();
        $this->eventGroups = new ArrayCollection();
        $this->eventGroupMembers = new ArrayCollection();
        $this->eventInvitations = new ArrayCollection();
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

    public function getFullName() : string
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

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
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
        if (!$this->eventOrganisers->contains($eventOrganiser)) {
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

    public function __toString(): string
    {
        return $this->getEmail();
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
        if (!$this->imageCollections->contains($imageCollection)) {
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
        if (!$this->eventGroups->contains($eventGroup)) {
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
        if (!$this->eventGroupMembers->contains($eventGroupMember)) {
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
    public function getEventInvitations(): Collection
    {
        return $this->eventInvitations;
    }

    public function addEventInvitation(EventInvitation $eventInvitation): self
    {
        if (!$this->eventInvitations->contains($eventInvitation)) {
            $this->eventInvitations[] = $eventInvitation;
            $eventInvitation->setOwner($this);
        }

        return $this;
    }

    public function removeEventInvitation(EventInvitation $eventInvitation): self
    {
        if ($this->eventInvitations->removeElement($eventInvitation)) {
            // set the owning side to null (unless already changed)
            if ($eventInvitation->getOwner() === $this) {
                $eventInvitation->setOwner(null);
            }
        }

        return $this;
    }


}

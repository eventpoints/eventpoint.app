<?php

declare(strict_types=1);

namespace App\Entity\Ticketing;

use App\Entity\User\User;
use App\Enum\MerchantTypeEnum;
use App\Enum\RefundPolicyTypeEnum;
use App\Enum\SellerTypeEnum;
use App\Repository\Ticketing\TicketMerchantProfileRepository;
use Carbon\CarbonImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: TicketMerchantProfileRepository::class)]
#[ORM\HasLifecycleCallbacks]
class TicketMerchantProfile
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\CustomIdGenerator(UuidGenerator::class)]
    private Uuid $id;

    // Collected by our form — Stripe does not ask for these
    #[ORM\Column(length: 30, enumType: SellerTypeEnum::class, nullable: true)]
    private ?SellerTypeEnum $sellerType = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $supportEmail = null;

    #[ORM\Column(length: 30, enumType: RefundPolicyTypeEnum::class, nullable: true)]
    private ?RefundPolicyTypeEnum $refundPolicyType = null;

    #[ORM\Column(nullable: true)]
    private ?int $refundPolicyDaysBefore = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $refundPolicyText = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $eventCancellationPolicyText = null;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $termsAccepted = false;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $lawfulEventsCert = false;

    // Collected by Stripe during Connect onboarding — stored here for reference only
    #[ORM\Column(length: 30, enumType: MerchantTypeEnum::class, nullable: true)]
    private ?MerchantTypeEnum $merchantType = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $displayName = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $legalName = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $contactEmail = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $contactPhone = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $contactDialCode = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $addressLine1 = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $addressLine2 = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $city = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $postcode = null;

    #[ORM\Column(length: 2, nullable: true)]
    private ?string $country = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $businessRegistrationNumber = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $businessRegisterName = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $vatId = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $stripeAccountId = null;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $stripeOnboardingComplete = false;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private CarbonImmutable $createdAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?CarbonImmutable $updatedAt = null;

    public function __construct(
        #[ORM\OneToOne(inversedBy: 'ticketMerchantProfile')]
        #[ORM\JoinColumn(nullable: false)]
        private User $owner
    ) {
        $this->createdAt = CarbonImmutable::now();
    }

    #[ORM\PreUpdate]
    public function onPreUpdate(): void
    {
        $this->updatedAt = CarbonImmutable::now();
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getOwner(): User
    {
        return $this->owner;
    }

    public function getSellerType(): ?SellerTypeEnum
    {
        return $this->sellerType;
    }

    public function setSellerType(?SellerTypeEnum $sellerType): static
    {
        $this->sellerType = $sellerType;
        return $this;
    }

    public function getSupportEmail(): ?string
    {
        return $this->supportEmail;
    }

    public function setSupportEmail(?string $supportEmail): static
    {
        $this->supportEmail = $supportEmail;
        return $this;
    }

    public function getRefundPolicyType(): ?RefundPolicyTypeEnum
    {
        return $this->refundPolicyType;
    }

    public function setRefundPolicyType(?RefundPolicyTypeEnum $refundPolicyType): static
    {
        $this->refundPolicyType = $refundPolicyType;
        return $this;
    }

    public function getRefundPolicyDaysBefore(): ?int
    {
        return $this->refundPolicyDaysBefore;
    }

    public function setRefundPolicyDaysBefore(?int $refundPolicyDaysBefore): static
    {
        $this->refundPolicyDaysBefore = $refundPolicyDaysBefore;
        return $this;
    }

    public function getRefundPolicyText(): ?string
    {
        return $this->refundPolicyText;
    }

    public function setRefundPolicyText(?string $refundPolicyText): static
    {
        $this->refundPolicyText = $refundPolicyText;
        return $this;
    }

    public function getEventCancellationPolicyText(): ?string
    {
        return $this->eventCancellationPolicyText;
    }

    public function setEventCancellationPolicyText(?string $eventCancellationPolicyText): static
    {
        $this->eventCancellationPolicyText = $eventCancellationPolicyText;
        return $this;
    }

    public function isTermsAccepted(): bool
    {
        return $this->termsAccepted;
    }

    public function setTermsAccepted(bool $termsAccepted): static
    {
        $this->termsAccepted = $termsAccepted;
        return $this;
    }

    public function isLawfulEventsCert(): bool
    {
        return $this->lawfulEventsCert;
    }

    public function setLawfulEventsCert(bool $lawfulEventsCert): static
    {
        $this->lawfulEventsCert = $lawfulEventsCert;
        return $this;
    }

    public function getMerchantType(): ?MerchantTypeEnum
    {
        return $this->merchantType;
    }

    public function setMerchantType(?MerchantTypeEnum $merchantType): static
    {
        $this->merchantType = $merchantType;
        return $this;
    }

    public function getDisplayName(): ?string
    {
        return $this->displayName;
    }

    public function setDisplayName(?string $displayName): static
    {
        $this->displayName = $displayName;
        return $this;
    }

    public function getLegalName(): ?string
    {
        return $this->legalName;
    }

    public function setLegalName(?string $legalName): static
    {
        $this->legalName = $legalName;
        return $this;
    }

    public function getContactEmail(): ?string
    {
        return $this->contactEmail;
    }

    public function setContactEmail(?string $contactEmail): static
    {
        $this->contactEmail = $contactEmail;
        return $this;
    }

    public function getContactPhone(): ?string
    {
        return $this->contactPhone;
    }

    public function setContactPhone(?string $contactPhone): static
    {
        $this->contactPhone = $contactPhone;
        return $this;
    }

    public function getContactDialCode(): ?string
    {
        return $this->contactDialCode;
    }

    public function setContactDialCode(?string $contactDialCode): static
    {
        $this->contactDialCode = $contactDialCode;
        return $this;
    }

    public function getAddressLine1(): ?string
    {
        return $this->addressLine1;
    }

    public function setAddressLine1(?string $addressLine1): static
    {
        $this->addressLine1 = $addressLine1;
        return $this;
    }

    public function getAddressLine2(): ?string
    {
        return $this->addressLine2;
    }

    public function setAddressLine2(?string $addressLine2): static
    {
        $this->addressLine2 = $addressLine2;
        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): static
    {
        $this->city = $city;
        return $this;
    }

    public function getPostcode(): ?string
    {
        return $this->postcode;
    }

    public function setPostcode(?string $postcode): static
    {
        $this->postcode = $postcode;
        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): static
    {
        $this->country = $country;
        return $this;
    }

    public function getBusinessRegistrationNumber(): ?string
    {
        return $this->businessRegistrationNumber;
    }

    public function setBusinessRegistrationNumber(?string $businessRegistrationNumber): static
    {
        $this->businessRegistrationNumber = $businessRegistrationNumber;
        return $this;
    }

    public function getBusinessRegisterName(): ?string
    {
        return $this->businessRegisterName;
    }

    public function setBusinessRegisterName(?string $businessRegisterName): static
    {
        $this->businessRegisterName = $businessRegisterName;
        return $this;
    }

    public function getVatId(): ?string
    {
        return $this->vatId;
    }

    public function setVatId(?string $vatId): static
    {
        $this->vatId = $vatId;
        return $this;
    }

    public function getStripeAccountId(): ?string
    {
        return $this->stripeAccountId;
    }

    public function setStripeAccountId(?string $stripeAccountId): static
    {
        $this->stripeAccountId = $stripeAccountId;
        return $this;
    }

    public function isStripeOnboardingComplete(): bool
    {
        return $this->stripeOnboardingComplete;
    }

    public function setStripeOnboardingComplete(bool $stripeOnboardingComplete): static
    {
        $this->stripeOnboardingComplete = $stripeOnboardingComplete;
        return $this;
    }

    public function getCreatedAt(): CarbonImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?CarbonImmutable
    {
        return $this->updatedAt;
    }
}

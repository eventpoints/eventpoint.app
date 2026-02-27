<?php

declare(strict_types=1);

namespace App\Service\Ticketing;

use App\Entity\Ticketing\TicketMerchantProfile;
use App\Enum\SellerTypeEnum;
use App\Repository\Ticketing\TicketMerchantProfileRepository;
use Stripe\Account;
use Stripe\AccountLink;
use Stripe\Stripe;

final readonly class StripeConnectService
{
    public function __construct(
        private string $stripeSecretKey,
        private TicketMerchantProfileRepository $profileRepository,
    ) {
    }

    public function createOrRetrieveAccount(TicketMerchantProfile $profile): string
    {
        Stripe::setApiKey($this->stripeSecretKey);

        if ($profile->getStripeAccountId() !== null) {
            return $profile->getStripeAccountId();
        }

        $user = $profile->getOwner();

        $businessType = $profile->getSellerType() === SellerTypeEnum::PRIVATE_INDIVIDUAL ? 'individual' : 'company';

        $params = [
            'type' => 'express',
            'capabilities' => [
                'card_payments' => [
                    'requested' => true,
                ],
                'transfers' => [
                    'requested' => true,
                ],
            ],
            'email' => $profile->getSupportEmail() ?? $user->getUserIdentifier(),
            'business_type' => $businessType,
            'business_profile' => [
                'support_email' => $profile->getSupportEmail(),
            ],
        ];

        if ($businessType === 'individual') {
            $params['individual'] = array_filter([
                'first_name' => $user->getFirstName(),
                'last_name' => $user->getLastName(),
                'email' => $user->getUserIdentifier(),
            ]);
        }

        if ($user->getCountry() !== null) {
            $params['country'] = strtoupper($user->getCountry());
        }

        $account = Account::create($params);

        $profile->setStripeAccountId($account->id);
        $this->profileRepository->save($profile, true);

        return $account->id;
    }

    public function createAccountLink(string $accountId, string $returnUrl, string $refreshUrl): string
    {
        Stripe::setApiKey($this->stripeSecretKey);

        $link = AccountLink::create([
            'account' => $accountId,
            'refresh_url' => $refreshUrl,
            'return_url' => $returnUrl,
            'type' => 'account_onboarding',
        ]);

        return $link->url;
    }

    public function syncAccountStatus(TicketMerchantProfile $profile): void
    {
        if ($profile->getStripeAccountId() === null) {
            return;
        }

        Stripe::setApiKey($this->stripeSecretKey);

        $account = Account::retrieve($profile->getStripeAccountId());

        $complete = $account->details_submitted && $account->charges_enabled;
        $profile->setStripeOnboardingComplete($complete);
        $this->profileRepository->save($profile, true);
    }
}

<?php

declare(strict_types=1);

namespace App\Service\Ticketing;

use App\Entity\User\User;

final class TicketMerchantGate
{
    public function isReadyToSell(User $user): bool
    {
        return $this->getMissingSteps($user) === [];
    }

    /**
     * @return list<string>
     */
    public function getMissingSteps(User $user): array
    {
        $missing = [];

        $profile = $user->getTicketMerchantProfile();

        if ($profile === null) {
            $missing[] = 'profile';
            $missing[] = 'stripe';
            return $missing;
        }

        if (! $profile->isTermsAccepted() || ! $profile->isLawfulEventsCert()) {
            $missing[] = 'profile';
        }

        if (! $profile->isStripeOnboardingComplete()) {
            $missing[] = 'stripe';
        }

        return $missing;
    }
}

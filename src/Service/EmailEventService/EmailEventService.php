<?php

declare(strict_types=1);

namespace App\Service\EmailEventService;

use App\Entity\User\User;
use App\Repository\Event\EventInvitationRepository;

final readonly class EmailEventService
{
    public function __construct(
        private EventInvitationRepository $eventInvitationRepository,
    ) {
    }

    /**
     * When a user registers, find any pending email invitations for their email
     * and link them to the user.
     */
    public function process(User $user): void
    {
        $eventInvitations = $this->eventInvitationRepository->findByTargetEmail(email: $user->getEmail());

        foreach ($eventInvitations as $invitation) {
            // Link the invitation to the user
            $invitation->setTargetUser($user);
            $this->eventInvitationRepository->save($invitation, true);
        }
    }
}

<?php

declare(strict_types=1);

namespace App\Service\EmailEventService;

use App\Entity\User;
use App\Factory\Event\EventInvitationFactory;
use App\Repository\Event\EventInvitationRepository;
use App\Repository\EventEmailInvitationRepository;

final readonly class EmailEventService
{
    public function __construct(
        private EventEmailInvitationRepository $eventEmailInvitationRepository,
        private EventInvitationRepository $eventInvitationRepository,
        private EventInvitationFactory $eventInvitationFactory
    ) {
    }

    public function process(User $user): void
    {
        $eventEmailInvitations = $this->eventEmailInvitationRepository->findByEmail(email: $user->getEmail());
        foreach ($eventEmailInvitations as $eventEmailInvitation) {
            $eventInvitation = $this->eventInvitationFactory->create(owner: $eventEmailInvitation->getOwner(), target: $user, event: $eventEmailInvitation->getEvent(), createdAt: $eventEmailInvitation->getCreatedAt());
            $this->eventInvitationRepository->save($eventInvitation, true);
            $this->eventEmailInvitationRepository->remove($eventEmailInvitation, true);
        }
    }
}

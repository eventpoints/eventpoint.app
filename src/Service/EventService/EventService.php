<?php

declare(strict_types=1);

namespace App\Service\EventService;

use App\Entity\Event\Event;
use App\Entity\User;
use App\Factory\Event\EventEmailInvitationFactory;
use App\Factory\Event\EventInvitationFactory;
use App\Repository\UserRepository;
use App\Service\EmailService\EmailService;

class EventService
{
    public function __construct(
        private readonly EventInvitationFactory      $eventInvitationFactory,
        private readonly EventEmailInvitationFactory $eventEmailInvitationFactory,
        private readonly UserRepository              $userRepository,
    ) {
    }

    public function process(Event $event, string $email): void
    {
        $user = $this->userRepository->findOneBy([
            'email' => $email,
        ]);

        if ($user instanceof User) {
            $this->sendInvitation(user: $user, event: $event);
        } else {
            $this->sendEmailInvitation(email: $email, event: $event);
        }
    }

    public function sendInvitation(User $user, Event $event): void
    {
        $invitation = $this->eventInvitationFactory->create(owner: $user, event: $event);
        $event->addEventInvitation($invitation);
    }

    public function sendEmailInvitation(string $email, Event $event): void
    {
        $emailInvitation = $this->eventEmailInvitationFactory->create(email: $email);
        $event->addEmailInvitation($emailInvitation);
        //        $this->emailService->sendInviteToUserWithoutAccount(recipientEmailAddress: $email);
    }
}

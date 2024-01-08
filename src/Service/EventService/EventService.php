<?php

declare(strict_types=1);

namespace App\Service\EventService;

use App\Entity\Event\Event;
use App\Entity\User;
use App\Factory\Event\EventEmailInvitationFactory;
use App\Factory\Event\EventInvitationFactory;
use App\Repository\UserRepository;
use App\Service\EmailService\EmailService;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class EventService
{
    public function __construct(
        private readonly EventInvitationFactory      $eventInvitationFactory,
        private readonly EventEmailInvitationFactory $eventEmailInvitationFactory,
        private readonly UserRepository              $userRepository,
        private readonly EmailService                $emailService,
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
            $this->sendEmailInvitationToUserWithoutAccount(email: $email, event: $event);
        }
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function sendInvitation(User $user, Event $event): void
    {
        if ($event->hasRequestedToAttend($user)) {
            // TODO: accept user's request to join
            return;
        }

        if ($event->getIsAttending($user)) {
            // TODO: send invitation for user with an existing account
            return;
        }

        $invitation = $this->eventInvitationFactory->create(owner: $user, event: $event);
        $this->emailService->sendInviteToUserWithAccount(
            recipientEmailAddress: $user->getEmail(),
            context: [
                'event' => $event,
                'user' => $user,
            ]
        );
        $event->addEventInvitation($invitation);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function sendEmailInvitationToUserWithoutAccount(string $email, Event $event): void
    {
        $emailInvitation = $this->eventEmailInvitationFactory->create(email: $email);
        $event->addEmailInvitation($emailInvitation);
        $this->emailService->sendInviteToUserWithoutAccount(
            recipientEmailAddress: $email,
            context: [
                'event' => $event,
            ]
        );
    }
}

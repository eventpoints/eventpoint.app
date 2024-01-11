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
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class EventService
{
    public function __construct(
        private readonly EventInvitationFactory      $eventInvitationFactory,
        private readonly EventEmailInvitationFactory $eventEmailInvitationFactory,
        private readonly UserRepository              $userRepository,
        private readonly EmailService                $emailService,
        private readonly UrlGeneratorInterface $urlGenerator,
    ) {
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function process(Event $event, string $email, User $currentUser): void
    {
        $user = $this->userRepository->findOneBy([
            'email' => $email,
        ]);

        if ($user instanceof User) {
            $this->sendInvitation(user: $user, event: $event, currentUser: $currentUser);
        } else {
            $this->sendEmailInvitationToUserWithoutAccount(email: $email, event: $event, currentUser: $currentUser);
        }
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function sendInvitation(User $user, Event $event, User $currentUser): void
    {
        if ($event->hasRequestedToAttend($user)) {
            // TODO: accept user's request to join
            return;
        }

        if ($event->getIsAttending($user)) {
            // TODO: send invitation for user with an existing account
            return;
        }

        $invitation = $this->eventInvitationFactory->create(owner: $currentUser, target: $user, event: $event);
        $this->emailService->sendInviteToUserWithAccount(
            recipientEmailAddress: $user->getEmail(),
            context: [
                'event' => $event,
                'target' => $user,
                'owner' => $currentUser,
            ]
        );
        $event->addEventInvitation($invitation);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function sendEmailInvitationToUserWithoutAccount(string $email, Event $event, User $currentUser): void
    {
        $emailInvitation = $this->eventEmailInvitationFactory->create(email: $email, owner: $currentUser);
        $event->addEmailInvitation($emailInvitation);
        $link = $this->urlGenerator->generate(name: 'show_event', parameters: [
            'id' => $event->getId(),
            'token' => $emailInvitation->getToken(),
        ]);
        $this->emailService->sendInviteToUserWithoutAccount(
            recipientEmailAddress: $email,
            context: [
                'event' => $event,
                'owner' => $currentUser,
                'link' => $link,
            ]
        );
    }
}

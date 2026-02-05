<?php

declare(strict_types=1);

namespace App\Service\EventService;

use App\Entity\Event\Event;
use App\Entity\Event\EventInvitation;
use App\Entity\User\Email;
use App\Entity\User\User;
use App\Enum\FlashEnum;
use App\Factory\Event\EventInvitationFactory;
use App\Repository\Event\EventInvitationRepository;
use App\Repository\Event\EventParticipantRepository;
use App\Repository\User\UserRepository;
use App\Service\EmailService\EmailService;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\FlashBagAwareSessionInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class EventService
{
    public function __construct(
        private EventInvitationFactory $eventInvitationFactory,
        private UserRepository $userRepository,
        private EmailService $emailService,
        private UrlGeneratorInterface $urlGenerator,
        private EventInvitationRepository $eventInvitationRepository,
        private EventParticipantRepository $eventParticipantRepository,
        private TranslatorInterface $translator,
    ) {
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function process(Event $event, Email $email, User $currentUser, RequestStack $requestStack): void
    {
        $session = $requestStack->getSession();

        if (! $session instanceof FlashBagAwareSessionInterface) {
            return;
        }

        $flashBag = $session->getFlashBag();

        $user = $this->userRepository->findOneBy([
            'email' => $email->getId(),
        ]);

        if ($user instanceof User) {
            $this->sendInvitation(user: $user, event: $event, currentUser: $currentUser, flashBag: $flashBag);
        } else {
            $this->sendEmailInvitationToUserWithoutAccount(email: $email, event: $event, currentUser: $currentUser, flashBag: $flashBag);
        }
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function sendInvitation(User $user, Event $event, User $currentUser, FlashBagInterface $flashBag): void
    {
        if ($this->canUserBeInvited(event: $event, user: $user, flashBag: $flashBag)) {
            $invitation = $this->eventInvitationFactory->createUserInvitation(owner: $currentUser, target: $user, event: $event);
            $this->emailService->sendInviteToUserWithAccount(
                email: $user->getEmail(),
                context: [
                    'event' => $event,
                    'target' => $user,
                    'owner' => $currentUser,
                ]
            );
            $event->addEventInvitation($invitation);
        }
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function sendEmailInvitationToUserWithoutAccount(Email $email, Event $event, User $currentUser, FlashBagInterface $flashBag): void
    {
        if ($this->canEmailBeInvited(event: $event, email: $email, flashBag: $flashBag)) {
            $emailInvitation = $this->eventInvitationFactory->createEmailInvitation(owner: $currentUser, email: $email, event: $event);
            $event->addEventInvitation($emailInvitation);
            $link = $this->urlGenerator->generate(name: 'show_event', parameters: [
                'id' => $event->getId(),
                'token' => $emailInvitation->getToken(),
            ], referenceType: UrlGeneratorInterface::ABSOLUTE_URL);
            $this->emailService->sendInviteToUserWithoutAccount(
                email: $email,
                context: [
                    'event' => $event,
                    'owner' => $currentUser,
                    'link' => $link,
                ]
            );
        }
    }

    public function canUserBeInvited(Event $event, User $user, FlashBagInterface $flashBag): bool
    {
        if ($event->getIsAttending(user: $user) || $event->getIsOrganiser(user: $user)) {
            $flashBag->add(FlashEnum::MESSAGE->value, $this->translator->trans('user-already-going'));
            return false;
        }

        if ($event->hasRequestedToAttend($user)) {
            $request = $event->getRequestToAttend($user);
            $this->acceptInvitation($request);
            $flashBag->add(FlashEnum::MESSAGE->value, $this->translator->trans('user-request-accepted'));
            return false;
        }
        return true;
    }

    public function canEmailBeInvited(Event $event, Email $email, FlashBagInterface $flashBag): bool
    {
        if ($event->hasEmailBeenInvited($email->getAddress())) {
            $flashBag->add(FlashEnum::MESSAGE->value, $this->translator->trans('email-already-invited'));
            return false;
        }

        return true;
    }

    public function acceptInvitation(EventInvitation $invitation): void
    {
        $this->eventInvitationFactory->accept($invitation);
        $this->eventInvitationRepository->save($invitation, true);
    }

    public function declineInvitation(EventInvitation $invitation): void
    {
        $this->eventInvitationFactory->decline($invitation);
        $this->eventInvitationRepository->save($invitation, true);
    }

    /**
     * @deprecated Use acceptInvitation() instead
     */
    public function convertRequestToParticipation(EventInvitation $eventRequest): void
    {
        $this->acceptInvitation($eventRequest);
    }
}

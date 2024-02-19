<?php

declare(strict_types=1);

namespace App\Service\EventService;

use App\Entity\Email;
use App\Entity\Event\Event;
use App\Entity\Event\EventEmailInvitation;
use App\Entity\Event\EventRequest;
use App\Entity\User;
use App\Enum\FlashEnum;
use App\Factory\Event\EventEmailInvitationFactory;
use App\Factory\Event\EventInvitationFactory;
use App\Factory\Event\EventRequestFactory;
use App\Repository\Event\EventParticipantRepository;
use App\Repository\Event\EventRequestRepository;
use App\Repository\EventEmailInvitationRepository;
use App\Repository\UserRepository;
use App\Service\EmailService\EmailService;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\FlashBagAwareSessionInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class EventService
{
    public function __construct(
        private readonly EventInvitationFactory         $eventInvitationFactory,
        private readonly EventEmailInvitationFactory    $eventEmailInvitationFactory,
        private readonly UserRepository                 $userRepository,
        private readonly EmailService                   $emailService,
        private readonly UrlGeneratorInterface          $urlGenerator,
        private readonly EventRequestFactory            $eventRequestFactory,
        private readonly EventRequestRepository         $eventRequestRepository,
        private readonly EventParticipantRepository     $eventParticipantRepository,
        private readonly EventEmailInvitationRepository $eventEmailInvitationRepository,
        private readonly TranslatorInterface            $translator,
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
            'email' => $email->getAddress(),
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
            $invitation = $this->eventInvitationFactory->create(owner: $currentUser, target: $user, event: $event);
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
            $emailInvitation = $this->eventEmailInvitationFactory->create(email: $email, owner: $currentUser);
            $event->addEmailInvitation($emailInvitation);
            $link = $this->urlGenerator->generate(name: 'show_event', parameters: [
                'id' => $event->getId(),
                'token' => $emailInvitation->getToken(),
            ], referenceType: UrlGeneratorInterface::ABSOLUTE_URL);
            $this->emailService->sendInviteToUserWithoutAccount(
                email: $email->getAddress(),
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
            $this->convertRequestToParticipation(eventRequest: $request);
            $flashBag->add(FlashEnum::MESSAGE->value, $this->translator->trans('user-request-accepted'));
            return false;
        }
        return true;
    }

    public function canEmailBeInvited(Event $event, Email $email, FlashBagInterface $flashBag): bool
    {
        $invitation = $this->eventEmailInvitationRepository->findOneBy([
            'email' => $email,
            'event' => $event,
        ]);

        if ($invitation instanceof EventEmailInvitation) {
            $flashBag->add(FlashEnum::MESSAGE->value, $this->translator->trans('email-already-invited'));
            return false;
        }

        return true;
    }

    public function convertRequestToParticipation(EventRequest $eventRequest): void
    {
        $participant = $this->eventRequestFactory->toEventParticipant(eventRequest: $eventRequest);
        $this->eventParticipantRepository->save($participant, true);
        $this->eventRequestRepository->remove($eventRequest, true);
    }
}

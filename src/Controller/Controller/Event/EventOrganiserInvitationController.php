<?php

declare(strict_types=1);

namespace App\Controller\Controller\Event;

use App\Entity\Event\Event;
use App\Entity\Event\EventOrganiserInvitation;
use App\Enum\FlashEnum;
use App\Factory\Event\EventOrganiserInvitationFactory;
use App\Form\Form\Event\EventOrganiserInviationFormType;
use App\Entity\User\User;
use App\Repository\Event\EventOrganiserInvitationRepository;
use App\Security\Voter\EventVoter;
use App\Service\EmailService\EmailService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Contracts\Translation\TranslatorInterface;

class EventOrganiserInvitationController extends AbstractController
{
    public function __construct(
        private readonly EventOrganiserInvitationRepository $eventOrganiserInvitationRepository,
        private readonly EventOrganiserInvitationFactory $eventOrganiserInvitationFactory,
        private readonly TranslatorInterface $translator,
        private readonly EmailService $emailService,
    ) {
    }

    #[Route(path: '/event/{id}/invite/organiser', name: 'invite_event_organiser')]
    public function invite(Event $event, Request $request, #[CurrentUser] User $currentUser): Response
    {
        $eventOrganiserInvitation = $this->eventOrganiserInvitationFactory->create(event: $event);
        $eventOrganiserInvitationForm = $this->createForm(EventOrganiserInviationFormType::class);
        $eventOrganiserInvitationForm->handleRequest($request);
        if ($eventOrganiserInvitationForm->isSubmitted() && $eventOrganiserInvitationForm->isValid()) {
            $owner = $eventOrganiserInvitationForm->get('owner')->getData();
            $role = $eventOrganiserInvitationForm->get('role')->getData();
            $eventOrganiserInvitation->setRole($role);

            if ($event->isAlreadyInvitedOrganiser($owner)) {
                $this->addFlash(FlashEnum::MESSAGE->value, $this->translator->trans('user-already-invited'));
                return $this->redirectToRoute('invite_event_organiser', [
                    'id' => $event->getId(),
                ]);
            }

            $eventOrganiserInvitation->setOwner($owner);

            $event->addEventOrganiserInvitation($eventOrganiserInvitation);
            $this->eventOrganiserInvitationRepository->save($eventOrganiserInvitation, true);

            $invitedUserEmail = $eventOrganiserInvitation->getOwner()?->getEmail();
            if ($invitedUserEmail !== null) {
                $this->emailService->sendEventOrgniserInvitationEmail($invitedUserEmail, [
                    'target' => $eventOrganiserInvitation->getOwner(),
                    'owner' => $currentUser,
                    'event' => $event,
                    'token' => $eventOrganiserInvitation->getToken(),
                ]);
            }

            $this->addFlash(FlashEnum::MESSAGE->value, $this->translator->trans('invitation-sent'));
            return $this->redirectToRoute('manage_event_organisers', [
                'id' => $event->getId(),
            ]);
        }

        return $this->render('events/organisers/invitation.html.twig', [
            'event' => $event,
            'eventOrganiserInvitationForm' => $eventOrganiserInvitationForm,
        ]);
    }

    #[
        Route(path: '/event/{id}/invite/organiser/remove', name: 'remove_invite_event_organiser', methods: [Request::METHOD_GET])]
    public function delete(EventOrganiserInvitation $eventOrganiserInvitation): Response
    {
        $event = $eventOrganiserInvitation->getEvent();
        $this->isGranted(EventVoter::CANCEL_EVENT, $event);
        $this->eventOrganiserInvitationRepository->remove($eventOrganiserInvitation, true);
        $this->addFlash(FlashEnum::MESSAGE->value, $this->translator->trans('invitation-remove'));
        return $this->redirectToRoute('manage_event_organisers', [
            'id' => $event->getId(),
        ]);
    }
}

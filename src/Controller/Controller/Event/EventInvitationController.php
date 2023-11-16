<?php

namespace App\Controller\Controller\Event;

use App\Entity\Event\Event;
use App\Entity\Event\EventInvitation;
use App\Entity\User;
use App\Factory\Event\EventInvitationFactory;
use App\Form\Form\EmailFormType;
use App\Form\Form\InvitationResponseFormType;
use App\Repository\Event\EventInvitationRepository;
use App\Repository\Event\EventRepository;
use App\Repository\UserRepository;
use App\Service\EventService\EventService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/event/invitations')]
class EventInvitationController extends AbstractController
{
    public function __construct(
        private readonly EventRepository           $eventRepository,
        private readonly EventService              $eventService,
        private readonly EventInvitationFactory    $eventInvitationFactory,
        private readonly EventInvitationRepository $eventInvitationRepository,
        private readonly TranslatorInterface       $translator
    )
    {
    }

    #[Route('/create/{event}', name: 'create_event_invitation', methods: [Request::METHOD_GET, Request::METHOD_POST])]
    public function create(Event $event, Request $request): Response
    {
        $eventInvitationForm = $this->createForm(EmailFormType::class);
        $eventInvitationForm->handleRequest($request);
        if ($eventInvitationForm->isSubmitted() && $eventInvitationForm->isValid()) {
            $email = $eventInvitationForm->get('email')->getData();

            $this->eventService->process(event: $event, email: $email);

            $this->eventRepository->save($event, true);
            $this->addFlash('message', $this->translator->trans('invitation-sent'));
            return $this->redirectToRoute('show_event', ['id' => $event->getId()]);
        }

        return $this->render('events/invitation/create.html.twig', [
            'event' => $event,
            'eventInvitationForm' => $eventInvitationForm
        ]);

    }

    #[Route('/{id}/response', name: 'invitation_response', methods: [Request::METHOD_GET, Request::METHOD_POST])]
    public function respond(EventInvitation $eventInvitation, Request $request): Response
    {
        $eventInvitationForm = $this->createForm(InvitationResponseFormType::class);
        $eventInvitationForm->handleRequest($request);
        if ($eventInvitationForm->isSubmitted() && $eventInvitationForm->isValid()) {
            $accept = $eventInvitationForm->get('accept');
            $decline = $eventInvitationForm->get('decline');
            if ($accept->isClicked()) {
                $eventRejection = $this->eventInvitationFactory->toEventParticipant($eventInvitation);
                $eventInvitation->getEvent()->addEventParticipant($eventRejection);
                $this->eventRepository->save($eventInvitation->getEvent(), true);
                $this->eventInvitationRepository->remove($eventInvitation, true);
                return $this->render('events/invitation/accepted.html.twig', [
                    'invitation' => $eventInvitation
                ]);
            }

            if ($decline->isClicked()) {
                $eventRejection = $this->eventInvitationFactory->toEventRejection($eventInvitation);
                $eventInvitation->getEvent()->addEventRejection($eventRejection);
                $this->eventRepository->save($eventInvitation->getEvent(), true);
                $this->eventInvitationRepository->remove($eventInvitation, true);
                return $this->render('events/invitation/declined.html.twig', [
                    'invitation' => $eventInvitation
                ]);
            }
        }

        return $this->render('events/invitation/respond.html.twig', [
            'invitation' => $eventInvitation,
            'eventInvitationForm' => $eventInvitationForm
        ]);

    }


}
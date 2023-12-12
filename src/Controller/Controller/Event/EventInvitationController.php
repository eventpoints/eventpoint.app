<?php

declare(strict_types=1);

namespace App\Controller\Controller\Event;

use App\Entity\Event\Event;
use App\Form\Form\EmailFormType;
use App\Repository\Event\EventRepository;
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
        private readonly EventRepository     $eventRepository,
        private readonly EventService        $eventService,
        private readonly TranslatorInterface $translator
    ) {
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
            return $this->redirectToRoute('show_event', [
                'id' => $event->getId(),
            ]);
        }

        return $this->render('events/invitation/create.html.twig', [
            'event' => $event,
            'eventInvitationForm' => $eventInvitationForm,
        ]);
    }
}

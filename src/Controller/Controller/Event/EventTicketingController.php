<?php

namespace App\Controller\Controller\Event;

use App\Entity\Event\Event;
use App\Entity\Event\EventMoment;
use App\Entity\Event\EventTicketOption;
use App\Enum\EventMomentTypeEnum;
use App\Form\Form\Event\EventTicketOptionFormType;
use App\Repository\Event\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EventTicketingController extends AbstractController
{
    public function __construct(
        private readonly EventRepository $eventRepository
    ) {
    }

    #[Route(path: '/events/ticket-options/{id}', name: 'event_tickets')]
    public function index(Event $event): Response
    {
        return $this->render('events/tickets/index.html.twig', [
            'event' => $event,
        ]);
    }

    #[Route(path: '/events/ticket-option/create/{id}', name: 'create_event_ticket')]
    public function create(Event $event, Request $request): Response
    {
        $eventTicketOption = new EventTicketOption();
        $eventTicketForm = $this->createForm(EventTicketOptionFormType::class, $eventTicketOption);

        $eventTicketForm->handleRequest($request);
        if ($eventTicketForm->isSubmitted() && $eventTicketForm->isValid()) {
            $event->addTicketOption($eventTicketOption);

            $moment = new EventMoment(event: $event, type: EventMomentTypeEnum::TICKET_OPTION_ADDED, oldValue: null, newValue: $eventTicketOption->getTitle());
            $event->addEventMoment($moment);

            $this->eventRepository->save($event, true);
            return $this->redirectToRoute('event_tickets', [
                'id' => $event->getId(),
            ]);
        }

        return $this->render('events/tickets/create.html.twig', [
            'event' => $event,
            'eventTicketForm' => $eventTicketForm,
        ]);
    }
}

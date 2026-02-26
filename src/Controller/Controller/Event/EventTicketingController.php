<?php

declare(strict_types=1);

namespace App\Controller\Controller\Event;

use App\Entity\Event\Event;
use App\Entity\Event\EventMoment;
use App\Entity\Event\EventTicketOption;
use App\Enum\EventMomentTypeEnum;
use App\Form\Form\Event\EventTicketOptionFormType;
use App\Repository\Event\EventRepository;
use App\Repository\Event\EventTicketOptionRepository;
use App\Service\Ticketing\TicketMerchantGate;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

#[IsGranted('ROLE_USER')]
class EventTicketingController extends AbstractController
{
    public function __construct(
        private readonly EventRepository $eventRepository,
        private readonly EventTicketOptionRepository $ticketOptionRepository,
        private readonly TicketMerchantGate $merchantGate,
        private readonly TranslatorInterface $translator,
    ) {
    }

    #[Route(path: '/events/ticket-options/{id}', name: 'event_tickets')]
    public function index(Event $event): Response
    {
        /** @var \App\Entity\User\User $user */
        $user = $this->getUser();

        return $this->render('events/tickets/index.html.twig', [
            'event' => $event,
            'merchantGateMissingSteps' => $this->merchantGate->getMissingSteps($user),
        ]);
    }

    #[Route(path: '/events/ticket-option/create/{id}', name: 'create_event_ticket')]
    public function create(Event $event, Request $request): Response
    {
        /** @var \App\Entity\User\User $user */
        $user = $this->getUser();

        if (!$this->merchantGate->isReadyToSell($user)) {
            return $this->redirectToRoute('event_tickets', ['id' => $event->getId()]);
        }

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

    #[Route(path: '/events/ticket-option/delete/{id}', name: 'delete_event_ticket', methods: ['DELETE', 'POST'])]
    public function delete(EventTicketOption $ticketOption): Response
    {
        $event = $ticketOption->getEvent();

        /** @var \App\Entity\User\User $user */
        $user = $this->getUser();

        if (!$event->getIsOrganiser($user)) {
            throw $this->createAccessDeniedException();
        }

        $event->removeTicketOption($ticketOption);
        $this->ticketOptionRepository->remove($ticketOption, true);

        $this->addFlash('success', $this->translator->trans('ticketing.ticket_option.deleted'));
        return $this->redirectToRoute('event_tickets', ['id' => $event->getId()]);
    }

    #[Route(path: '/events/ticket-option/toggle/{id}', name: 'toggle_event_ticket', methods: ['POST'])]
    public function toggle(EventTicketOption $ticketOption): Response
    {
        $event = $ticketOption->getEvent();

        /** @var \App\Entity\User\User $user */
        $user = $this->getUser();

        if (!$event->getIsOrganiser($user)) {
            throw $this->createAccessDeniedException();
        }

        $ticketOption->setIsEnabled(!$ticketOption->isEnabled());
        $this->ticketOptionRepository->save($ticketOption, true);

        return $this->redirectToRoute('event_tickets', ['id' => $event->getId()]);
    }
}

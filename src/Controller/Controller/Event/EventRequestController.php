<?php

declare(strict_types=1);

namespace App\Controller\Controller\Event;

use App\Entity\Event\Event;
use App\Entity\Event\EventRequest;
use App\Entity\User;
use App\Enum\FlashEnum;
use App\Factory\Event\EventRequestFactory;
use App\Repository\Event\EventRepository;
use App\Repository\Event\EventRequestRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class EventRequestController extends AbstractController
{
    public function __construct(
        private readonly EventRequestFactory $eventRequestFactory,
        private readonly EventRequestRepository $eventRequestRepository,
        private readonly EventRepository     $eventRepository
    ) {
    }

    #[Route(path: '/events/rsvp/request/{id}', name: 'event_rsvp_request', methods: [Request::METHOD_GET])]
    public function create(Event $event, #[CurrentUser] User $currentUser): Response
    {
        if ($event->getIsAttending($currentUser)) {
            $this->addFlash(FlashEnum::MESSAGE->value, 'already-attending');
            return $this->redirectToRoute('show_event', [
                'id' => $event->getId(),
            ]);
        }

        if ($event->hasRequestedToAttend($currentUser)) {
            $this->addFlash(FlashEnum::MESSAGE->value, 'request-already-sent');
            return $this->redirectToRoute('show_event', [
                'id' => $event->getId(),
            ]);
        }

        $eventRequest = $this->eventRequestFactory->create(event: $event, owner: $currentUser);
        $event->addEventRequest($eventRequest);
        $this->eventRepository->save($event, true);
        $this->addFlash(FlashEnum::MESSAGE->value, 'request-sent');
        return $this->redirectToRoute('show_event', [
            'id' => $event->getId(),
        ]);
    }

    #[Route(path: '/events/rsvp/cancel/{id}', name: 'cancel_event_rsvp_request', methods: [Request::METHOD_GET])]
    public function remove(EventRequest $eventRequest, #[CurrentUser] User $currentUser): Response
    {
        $event = $eventRequest->getEvent();
        if (! $event->getIsAttending($currentUser) && ! $event->hasRequestedToAttend($currentUser)) {
            $this->addFlash(FlashEnum::MESSAGE->value, 'can-not-cancel');
            return $this->redirectToRoute('show_event', [
                'id' => $event->getId(),
            ]);
        }

        $this->eventRequestRepository->remove($eventRequest, true);
        $this->addFlash(FlashEnum::MESSAGE->value, 'request-canceled');
        return $this->redirectToRoute('show_event', [
            'id' => $event->getId(),
        ]);
    }
}

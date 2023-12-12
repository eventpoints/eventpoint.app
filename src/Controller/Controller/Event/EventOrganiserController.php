<?php

declare(strict_types=1);

namespace App\Controller\Controller\Event;

use App\Entity\Event\Event;
use App\Entity\Event\EventOrganiser;
use App\Repository\Event\EventRepository;
use App\Security\Voter\EventOrganiserVoter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EventOrganiserController extends AbstractController
{
    public function __construct(
        private readonly EventRepository $eventRepository
    ) {
    }

    #[Route(path: '/event/{id}/manage/organisers', name: 'manage_event_organisers')]
    public function index(Event $event): Response
    {
        return $this->render('events/organisers/index.html.twig', [
            'event' => $event,
        ]);
    }

    #[Route(path: '/event/{event}/remove/organiser/{id}', name: 'remove_event_organiser')]
    public function remove(Event $event, EventOrganiser $eventOrganiser): Response
    {
        $this->isGranted(EventOrganiserVoter::REMOVE_EVENT_ORGANISER, $eventOrganiser);
        if ($eventOrganiser->getEvent() === $event) {
            $this->eventRepository->save($event, true);
        }
        return $this->redirectToRoute('manage_event_organisers', [
            'id' => $event->getId(),
        ]);
    }
}

<?php

declare(strict_types=1);

namespace App\Controller\Controller\User;

use App\Entity\User;
use App\Repository\Event\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/user')]
class EventController extends AbstractController
{
    public function __construct(
        private readonly EventRepository $eventRepository
    ) {
    }

    #[Route(path: '/events', name: 'user_events', methods: [Request::METHOD_GET, Request::METHOD_POST])]
    public function create(#[CurrentUser] User $currentUser): Response
    {
        $events = $this->eventRepository->findUpcomingByUser($currentUser);
        return $this->render('user/events.html.twig', [
            'events' => $events,
        ]);
    }
}

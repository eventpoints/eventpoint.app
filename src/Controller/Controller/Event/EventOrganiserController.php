<?php

declare(strict_types=1);

namespace App\Controller\Controller\Event;

use App\Entity\Event\Event;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/event/{id}/organisers')]

class EventOrganiserController extends AbstractController
{
    #[Route(path: '/', name: 'event_organisers')]
    public function index(Event $event): Response
    {
        return $this->render('events/organisers/index.html.twig', [
            'event' => $event,
        ]);
    }
}

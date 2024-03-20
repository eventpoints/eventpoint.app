<?php

declare(strict_types=1);

namespace App\Controller\Controller\User;

use App\Entity\User\User;
use App\Repository\Event\EventRequestRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/user')]
class RequestController extends AbstractController
{
    public function __construct(
        private readonly EventRequestRepository $eventRequestRepository
    ) {
    }

    #[Route(path: '/event/requests', name: 'user_event_requests')]
    public function index(#[CurrentUser] User $currentUser): Response
    {
        $eventRequests = $this->eventRequestRepository->findBy([
            'owner' => $currentUser,
        ]);

        return $this->render('user/requests.html.twig', [
            'eventRequests' => $eventRequests,
        ]);
    }
}

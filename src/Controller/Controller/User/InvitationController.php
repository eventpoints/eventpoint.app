<?php

declare(strict_types=1);

namespace App\Controller\Controller\User;

use App\Entity\User\User;
use App\Repository\Event\EventInvitationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/user')]
class InvitationController extends AbstractController
{
    public function __construct(
        private readonly EventInvitationRepository $eventInvitationRepository
    ) {
    }

    #[Route(path: '/event/invitations', name: 'user_event_invitations')]
    public function index(#[CurrentUser] User $currentUser): Response
    {
        $eventInvitations = $this->eventInvitationRepository->findByTarget(user: $currentUser);

        return $this->render('user/invitations.html.twig', [
            'eventInvitations' => $eventInvitations,
        ]);
    }
}

<?php

namespace App\Controller\Controller\User;

use App\Entity\User;
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
    )
    {
    }

    #[Route(path: '/invitations', name: 'user_invitations')]
    public function index(#[CurrentUser] User $currentUser): Response
    {
        $invitations = $this->eventInvitationRepository->findBy(['owner' => $currentUser]);

        return $this->render('user/invitations.html.twig',[
            'invitations' => $invitations
        ]);
    }


}
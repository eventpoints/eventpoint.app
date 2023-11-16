<?php

declare(strict_types=1);

namespace App\Controller\Controller\User;

use App\Entity\User;
use App\Repository\Event\EventGroupRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/user')]
class GroupController extends AbstractController
{
    public function __construct(
        private readonly EventGroupRepository $eventGroupRepository
    ) {
    }

    #[Route(path: '/groups', name: 'user_groups', methods: [Request::METHOD_GET, Request::METHOD_POST])]
    public function create(Request $request, #[CurrentUser] User $currentUser): Response
    {
        $groups = $this->eventGroupRepository->findByUser($currentUser);
        return $this->render('user/groups.html.twig', [
            'groups' => $groups,
        ]);
    }
}

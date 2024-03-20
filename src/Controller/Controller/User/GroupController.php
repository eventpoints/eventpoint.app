<?php

declare(strict_types=1);

namespace App\Controller\Controller\User;

use App\Entity\User\User;
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

    #[Route(path: '/groups/memberships', name: 'user_group_memberships', methods: [Request::METHOD_GET, Request::METHOD_POST])]
    public function showUserGroupMemberships(Request $request, #[CurrentUser] User $currentUser): Response
    {
        return $this->render('user/group-memberships.html.twig');
    }

    #[Route(path: '/groups/managed', name: 'user_group_managed', methods: [Request::METHOD_GET, Request::METHOD_POST])]
    public function showUserManagedGroups(Request $request, #[CurrentUser] User $currentUser): Response
    {
        return $this->render('user/managed-groups.html.twig');
    }

    #[Route(path: '/groups/invitations', name: 'user_group_invitations', methods: [Request::METHOD_GET, Request::METHOD_POST])]
    public function showUserGroupInvitations(Request $request, #[CurrentUser] User $currentUser): Response
    {
        $groups = $this->eventGroupRepository->findByGroupsManaged($currentUser);
        return $this->render('user/group-invitations.html.twig', [
            'groups' => $groups,
        ]);
    }

    #[Route(path: '/groups/join/requests', name: 'user_group_join_requests', methods: [Request::METHOD_GET, Request::METHOD_POST])]
    public function showUserGroupJoinRequests(Request $request, #[CurrentUser] User $currentUser): Response
    {
        $groups = $this->eventGroupRepository->findByGroupsManaged($currentUser);
        return $this->render('user/group-requests.html.twig', [
            'groups' => $groups,
        ]);
    }
}

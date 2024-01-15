<?php

declare(strict_types=1);

namespace App\Controller\Controller;

use App\Entity\User;
use App\Repository\UserContactRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class AutocompleteController extends AbstractController
{
    public function __construct(
        private readonly UserRepository        $userRepository,
        private readonly UserContactRepository $userContactRepository,
    ) {
    }

    #[Route(path: '/user-search', name: 'autocomplete_user_search')]
    public function index(Request $request): JsonResponse
    {
        $query = $request->get('query');
        $users = $this->userRepository->findByEmail($query);
        return $this->json($users);
    }

    #[Route(path: '/user/contacts/', name: 'autocomplete_user_contacts_search')]
    public function findContact(Request $request, #[CurrentUser] User $currentUser): JsonResponse
    {
        $keyword = $request->get('keyword');
        $contacts = $this->userContactRepository->findByOwnerAndQuery(user: $currentUser, emailAddress: $keyword);
        $template = $this->renderView('autocomplete/user_contract.html.twig', [
            'contacts' => $contacts,
        ]);
        return $this->json(data: [
            'contacts' => $contacts,
            'template' => $template,
        ], context: [
            'groups' => 'user_contact',
        ]);
    }
}

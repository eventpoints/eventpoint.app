<?php

namespace App\Controller\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AutocompleteController extends AbstractController
{


    public function __construct(
        private readonly UserRepository $userRepository
    )
    {
    }

    #[Route(path: '/user-search', name: 'autocomplete_user_search')]
    public function index(Request $request): JsonResponse
    {
        $query = $request->get('query');
        $users = $this->userRepository->findByEmail($query);
        return $this->json($users);
    }


}
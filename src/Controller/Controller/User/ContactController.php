<?php

declare(strict_types=1);

namespace App\Controller\Controller\User;

use App\Entity\User;
use App\Repository\UserContactRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class ContactController extends AbstractController
{
    public function __construct(
        private readonly UserContactRepository $userContactRepository,
    ) {
    }

    #[Route(path: '/user/contacts', name: 'user_contacts')]
    public function index(#[CurrentUser] User $currentUser): Response
    {
        $contacts = $this->userContactRepository->findByOwner(user: $currentUser);

        return $this->render('user/contacts.html.twig', [
            'contacts' => $contacts,
        ]);
    }
}

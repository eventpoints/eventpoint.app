<?php

declare(strict_types=1);

namespace App\Controller\Controller\User;

use App\Entity\User;
use App\Repository\UserContactRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class ContactController extends AbstractController
{
    public function __construct(
        private readonly UserContactRepository $userContactRepository,
        private readonly PaginatorInterface $paginator,
    ) {
    }

    #[Route(path: '/user/contacts', name: 'user_contacts')]
    public function index(Request $request, #[CurrentUser] User $currentUser): Response
    {
        $contactsQuery = $this->userContactRepository->findByOwner(user: $currentUser, isQuery: true);
        $contactsPagination = $this->paginator->paginate(target: $contactsQuery, page: $request->query->getInt('page', 1), limit: 2);
        return $this->render('user/contacts.html.twig', [
            'contactsPagination' => $contactsPagination,
        ]);
    }
}

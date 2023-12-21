<?php

declare(strict_types=1);

namespace App\Controller\Controller\User;

use App\Entity\User;
use App\Repository\ConversationRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class ConversationController extends AbstractController
{
    public function __construct(
        private readonly PaginatorInterface     $paginator,
        private readonly ConversationRepository $conversationRepository
    ) {
    }

    #[Route(path: '/conversations', name: 'user_conversations')]
    public function index(#[CurrentUser] User $currentUser, Request $request): Response
    {
        $conversationQuery = $this->conversationRepository->findByUser(user: $currentUser, isQuery: true);
        $conversationPagination = $this->paginator->paginate(target: $conversationQuery, page: $request->query->getInt('conversation', 1), limit: 30, options: [
            'pageParameterName' => 'conversation',
        ]);

        return $this->render('user/conversations.html.twig', [
            'conversationPagination' => $conversationPagination,
        ]);
    }
}

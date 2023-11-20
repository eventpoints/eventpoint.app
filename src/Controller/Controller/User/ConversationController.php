<?php

declare(strict_types=1);

namespace App\Controller\Controller\User;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class ConversationController extends AbstractController
{
    #[Route(path: '/conversations', name: 'user_conversations')]
    public function index(#[CurrentUser] User $currentUser): Response
    {
        return $this->render('user/conversations.html.twig');
    }

}

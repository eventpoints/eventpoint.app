<?php

declare(strict_types=1);

namespace App\Controller\Controller\Conversation;

use App\Entity\Conversation;
use App\Entity\User;
use App\Factory\Conversation\ConversationFactory;
use App\Factory\Conversation\ConversationParticipantFactory;
use App\Repository\ConversationRepository;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class ConversationController extends AbstractController
{
    public function __construct(
        private readonly ConversationFactory               $conversationFactory,
        private readonly ConversationParticipantFactory    $conversationParticipantFactory,
        private readonly ConversationRepository            $conversationRepository
    ) {
    }

    #[Route('/show/{id}', name: 'show_conversation', methods: [Request::METHOD_GET])]
    public function show(Conversation $conversation, #[CurrentUser] User $currentUser): Response
    {
        return $this->render('conversation/show.html.twig', [
            'conversation' => $conversation,
        ]);
    }

    /**
     * @throws NonUniqueResultException
     */
    #[Route('/create/{id}', name: 'create_direct_conversation', methods: [Request::METHOD_GET])]
    public function create(User $user, #[CurrentUser] User $currentUser): Response
    {
        $conversation = $this->conversationRepository->findByCurrentUserOrTarget($currentUser);

        if ($conversation instanceof Conversation) {
            return $this->redirectToRoute('show_conversation', [
                'id' => $conversation->getId(),
            ]);
        }

        $conversation = $this->conversationFactory->create(owner: $currentUser);
        $conversationParticipantOwner = $this->conversationParticipantFactory->create(owner: $currentUser, conversation: $conversation);
        $conversationParticipantTarget = $this->conversationParticipantFactory->create(owner: $user, conversation: $conversation);
        $conversation->addConversationParticipant($conversationParticipantOwner);
        $conversation->addConversationParticipant($conversationParticipantTarget);

        $this->conversationRepository->save($conversation, true);
        return $this->redirectToRoute('show_conversation', [
            'id' => $conversation->getId(),
        ]);
    }
}

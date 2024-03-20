<?php

declare(strict_types=1);

namespace App\Controller\Controller\Conversation;

use App\Entity\Conversation\Conversation;
use App\Entity\Conversation\ConversationParticipant;
use App\Entity\Conversation\Message;
use App\Entity\User\User;
use App\Exception\ShouldNotHappenException;
use App\Factory\Conversation\ConversationFactory;
use App\Factory\Conversation\ConversationParticipantFactory;
use App\Form\Form\Conversation\MessageFormType;
use App\Repository\Conversation\ConversationRepository;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class ConversationController extends AbstractController
{
    public function __construct(
        private readonly ConversationFactory $conversationFactory,
        private readonly ConversationParticipantFactory $conversationParticipantFactory,
        private readonly ConversationRepository $conversationRepository,
    ) {
    }

    /**
     * @throws ShouldNotHappenException
     */
    #[Route('/conversation/show/{id}', name: 'show_conversation', methods: [Request::METHOD_GET, Request::METHOD_POST])]
    public function show(Conversation $conversation, #[CurrentUser] User $currentUser, Request $request): Response
    {
        $message = new Message();
        $conversationParticipant = $conversation->getConversationParticipants()->findFirst(fn (int $key, ConversationParticipant $conversationParticipant) => $conversationParticipant->getOwner() === $currentUser);

        if (! $conversationParticipant instanceof ConversationParticipant) {
            throw new ShouldNotHappenException('conversation participant must exist at this point');
        }

        $message->setConversation($conversation);
        $message->setConversationParticipant($conversationParticipant);
        $messageForm = $this->createForm(MessageFormType::class, $message);
        $messageForm->handleRequest($request);
        if ($messageForm->isSubmitted() && $messageForm->isValid()) {
            $conversation->addMessage($message);
            $this->conversationRepository->save($conversation, true);
            return $this->redirectToRoute('show_conversation', [
                'id' => $conversation->getId(),
            ]);
        }

        return $this->render('conversation/show.html.twig', [
            'conversation' => $conversation,
            'messageForm' => $messageForm,
        ]);
    }

    /**
     * @throws NonUniqueResultException
     */
    #[Route('/conversation/create/{id}', name: 'create_direct_conversation', methods: [Request::METHOD_GET])]
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

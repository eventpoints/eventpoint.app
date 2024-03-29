<?php

declare(strict_types=1);

namespace App\Controller\Controller\Group;

use App\Entity\EventGroup\EventGroupDiscussion;
use App\Entity\User\User;
use App\Enum\FlashEnum;
use App\Factory\EventGroup\EventGroupDiscussionCommentFactory;
use App\Form\Form\EventGroup\EventGroupDiscussionCommentFormType;
use App\Repository\EventGroup\EventGroupDiscussionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('group/discussions/comments')]
class EventGroupDiscussionCommentController extends AbstractController
{
    public function __construct(
        private readonly EventGroupDiscussionRepository $discussionRepository,
        private readonly EventGroupDiscussionCommentFactory $eventGroupDiscussionCommentFactory,
        private readonly TranslatorInterface $translator
    ) {
    }

    #[Route('/create/{id}', name: 'create_group_discussion_comment', methods: [Request::METHOD_GET, Request::METHOD_POST])]
    public function create(EventGroupDiscussion $eventGroupDiscussion, Request $request, #[CurrentUser] User $currentUser): Response
    {
        $discussionComment = $this->eventGroupDiscussionCommentFactory->create(owner: $currentUser);
        $eventDiscussionCommentForm = $this->createForm(EventGroupDiscussionCommentFormType::class, $discussionComment);
        $eventDiscussionCommentForm->handleRequest($request);
        if ($eventDiscussionCommentForm->isSubmitted() && $eventDiscussionCommentForm->isValid()) {
            $eventGroupDiscussion->addComment($discussionComment);
            $this->discussionRepository->save($eventGroupDiscussion, true);
            $this->addFlash(FlashEnum::MESSAGE->value, $this->translator->trans('changes-saved'));
            return $this->redirectToRoute('show_group_discussion', [
                'id' => $eventGroupDiscussion->getId(),
            ]);
        }

        return $this->render('groups/discussion/create.html.twig', [
            'eventDiscussionForm' => $eventDiscussionCommentForm,
        ]);
    }
}

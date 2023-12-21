<?php

declare(strict_types=1);

namespace App\Controller\Controller\Group;

use App\Entity\EventGroup\EventGroup;
use App\Entity\EventGroupDiscussion;
use App\Entity\User;
use App\Enum\FlashEnum;
use App\Factory\EventGroup\EventGroupDiscusionFactroy;
use App\Factory\EventGroup\EventGroupDiscussionCommentFactory;
use App\Form\Form\EventDiscussionCommentFormType;
use App\Form\Form\EventDiscussionFormType;
use App\Repository\EventDiscussionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('group/discussions')]
class EventGroupDiscussionController extends AbstractController
{
    public function __construct(
        private readonly EventGroupDiscusionFactroy         $eventGroupDiscusionFactroy,
        private readonly EventGroupDiscussionCommentFactory $eventGroupDiscussionCommentFactory,
        private readonly EventDiscussionRepository          $discussionRepository,
        private readonly TranslatorInterface                $translator
    ) {
    }

    #[Route('/show/{id}', name: 'show_group_discussion', methods: [Request::METHOD_GET, Request::METHOD_POST])]
    public function show(EventGroupDiscussion $eventGroupDiscussion, Request $request, #[CurrentUser] User $currentUser): Response
    {
        $comment = $this->eventGroupDiscussionCommentFactory->create(discussion: $eventGroupDiscussion, owner: $currentUser);
        $eventDiscussionCommentForm = $this->createForm(EventDiscussionCommentFormType::class, $comment);
        $eventDiscussionCommentForm->handleRequest($request);
        if ($eventDiscussionCommentForm->isSubmitted() && $eventDiscussionCommentForm->isValid()) {
            $eventGroupDiscussion->addComment($comment);
            $this->discussionRepository->save($eventGroupDiscussion, true);
            $this->addFlash(FlashEnum::MESSAGE->value, $this->translator->trans('changes-saved'));
            return $this->redirectToRoute('show_group_discussion', [
                'id' => $eventGroupDiscussion->getId(),
            ]);
        }

        return $this->render('events/group/discussion/show.html.twig', [
            'eventGroupDiscussion' => $eventGroupDiscussion,
            'eventDiscussionCommentForm' => $eventDiscussionCommentForm,
        ]);
    }

    #[Route('/create/{id}', name: 'create_group_discussion', methods: [Request::METHOD_GET, Request::METHOD_POST])]
    public function create(EventGroup $eventGroup, Request $request, #[CurrentUser] User $currentUser): Response
    {
        $discussion = $this->eventGroupDiscusionFactroy->create(owner: $currentUser, eventGroup: $eventGroup);
        $eventDiscussionForm = $this->createForm(EventDiscussionFormType::class, $discussion);
        $eventDiscussionForm->handleRequest($request);
        if ($eventDiscussionForm->isSubmitted() && $eventDiscussionForm->isValid()) {
            $this->discussionRepository->save($discussion, true);
            $this->addFlash(FlashEnum::MESSAGE->value, $this->translator->trans('changes-saved'));
            return $this->redirectToRoute('event_group_discussion', [
                'id' => $eventGroup->getId(),
            ]);
        }

        return $this->render('events/group/discussion/create.html.twig', [
            'eventDiscussionForm' => $eventDiscussionForm,
        ]);
    }
}

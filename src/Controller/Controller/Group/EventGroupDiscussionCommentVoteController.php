<?php

declare(strict_types=1);

namespace App\Controller\Controller\Group;

use App\Entity\EventGroup\EventGroupDiscussionComment;
use App\Entity\EventGroup\EventGroupDiscussionCommentVote;
use App\Entity\User\User;
use App\Enum\VoteEnum;
use App\Factory\EventGroup\EventGroupDiscussionCommentVoteFactory;
use App\Repository\EventGroup\EventGroupDiscussionCommentVoteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('group/discussions/comments/vote')]
class EventGroupDiscussionCommentVoteController extends AbstractController
{
    public function __construct(
        private readonly EventGroupDiscussionCommentVoteFactory $eventGroupDiscussionCommentVoteFactory,
        private readonly EventGroupDiscussionCommentVoteRepository $eventDiscussionCommentVoteRepository
    ) {
    }

    #[Route('/create/{id}/{type}', name: 'create_discussion_comment_vote', methods: [Request::METHOD_GET, Request::METHOD_POST])]
    public function create(
        EventGroupDiscussionComment $eventDiscussionComment,
        string $type,
        #[CurrentUser]
        User $currentUser
    ): Response {
        $voteType = VoteEnum::from($type);
        $vote = $this->eventDiscussionCommentVoteRepository->findOneBy([
            'discussionComment' => $eventDiscussionComment,
            'owner' => $currentUser,
        ]);

        if ($vote instanceof EventGroupDiscussionCommentVote) {
            if ($voteType === $vote->getType()) {
                $this->eventDiscussionCommentVoteRepository->remove($vote, true);
            } else {
                $vote->setType($voteType);
                $this->eventDiscussionCommentVoteRepository->save($vote, true);
            }
        } else {
            $vote = $this->eventGroupDiscussionCommentVoteFactory->create(
                eventDiscussionComment: $eventDiscussionComment,
                voteEnum: $voteType,
                owner: $currentUser
            );
            $this->eventDiscussionCommentVoteRepository->save($vote, true);
        }

        return $this->redirectToRoute('show_group_discussion', [
            'id' => $eventDiscussionComment->getDiscussion()->getId(),
        ]);
    }
}

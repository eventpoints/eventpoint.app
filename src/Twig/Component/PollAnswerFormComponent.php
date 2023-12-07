<?php

declare(strict_types=1);

namespace App\Twig\Component;

use App\Entity\Poll\Poll;
use App\Entity\User;
use App\Enum\FlashEnum;
use App\Factory\Poll\PollAnswerFactory;
use App\Repository\PollAnswerRepository;
use App\Repository\PollOptionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent('poll_answer_form')]
class PollAnswerFormComponent extends AbstractController
{
    use DefaultActionTrait;

    #[LiveProp(writable: true)]
    public null|Poll $poll = null;

    public function __construct(
        private readonly PollAnswerRepository $pollAnswerRepository,
        private readonly PollAnswerFactory    $pollAnswerFactory,
        private readonly PollOptionRepository $pollOptionRepository
    ) {
    }

    #[LiveAction]
    public function submit(#[LiveArg] string $id): void
    {
        $pollOption = $this->pollOptionRepository->find($id);
        $currentUser = $this->getUser();
        if (! $currentUser instanceof User) {
            return;
        }
        $pollAnswer = $this->pollAnswerFactory->create(poll: $this->poll, owner: $currentUser, pollOption: $pollOption);
        $this->pollAnswerRepository->save($pollAnswer, true);
        $this->addFlash(FlashEnum::MESSAGE->value, 'poll-answered');
    }
}

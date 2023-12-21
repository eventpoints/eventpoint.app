<?php

declare(strict_types=1);

namespace App\Controller\Controller\Group;

use App\Entity\EventGroup\EventGroup;
use App\Entity\User;
use App\Enum\FlashEnum;
use App\Factory\Poll\PollFactory;
use App\Factory\Poll\PollOptionFactory;
use App\Form\Form\Poll\PollFormType;
use App\Repository\PollRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class PollController extends AbstractController
{
    public function __construct(
        private readonly PollFactory $pollFactory,
        private readonly PollOptionFactory $pollOptionFactory,
        private readonly PollRepository $pollRepository
    ) {
    }

    #[Route('/groups/poll/create/{id}', name: 'create_group_event_poll', methods: ['GET', 'POST'])]
    public function create(EventGroup $eventGroup, Request $request, #[CurrentUser] User $currentUser): Response
    {
        $poll = $this->pollFactory->create(eventGroup: $eventGroup, owner: $currentUser);
        $this->pollOptionFactory->createThreeEmptyOptions($poll);
        $pollForm = $this->createForm(PollFormType::class, $poll);
        $pollForm->handleRequest($request);
        if ($pollForm->isSubmitted() && $pollForm->isValid()) {
            $this->pollRepository->save($poll, true);
            $this->addFlash(FlashEnum::MESSAGE->value, 'poll-created');
            return $this->redirectToRoute('event_group_show', [
                'id' => $eventGroup->getId(),
            ]);
        }

        return $this->render('events/group/poll/create.html.twig', [
            'poll' => $poll,
            'pollForm' => '$pollForm',
            'eventGroup' => $eventGroup,
        ]);
    }
}

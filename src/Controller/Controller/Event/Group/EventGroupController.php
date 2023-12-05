<?php

declare(strict_types=1);

namespace App\Controller\Controller\Event\Group;

use App\Entity\EventGroup\EventGroup;
use App\Entity\EventGroup\EventGroupMember;
use App\Entity\User;
use App\Enum\EventGroupRoleEnum;
use App\Enum\FlashEnum;
use App\Factory\EventGroup\EventGroupFactory;
use App\Factory\EventGroup\EventGroupMemberFactory;
use App\Form\Form\EventGroupFormType;
use App\Repository\Event\EventGroupRepository;
use App\Repository\Event\EventRepository;
use App\Repository\EventDiscussionCommentRepository;
use App\Repository\EventDiscussionRepository;
use App\Repository\EventGroupRoleRepository;
use App\Service\EventGroupAnalyzer\EventGroupAnalyzer;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/groups')]
class EventGroupController extends AbstractController
{
    public function __construct(
        private readonly EventGroupRepository      $eventGroupRepository,
        private readonly EventRepository      $eventRepository,
        private readonly EventGroupFactory         $eventGroupFactory,
        private readonly EventGroupMemberFactory   $eventGroupMemberFactory,
        private readonly EventGroupRoleRepository  $eventGroupRoleRepository,
        private readonly EventDiscussionRepository $eventDiscussionRepository,
        private readonly EventDiscussionCommentRepository $eventDiscussionCommentRepository,
        private readonly PaginatorInterface        $paginator,
        private readonly TranslatorInterface       $translator,
        private readonly EventGroupAnalyzer        $eventGroupAnalyzer,
    ) {
    }

    /**
     * @throws \Exception
     */

    #[Route('/show/{id}', name: 'event_group_show', methods: ['GET', 'POST'])]
    public function show(EventGroup $eventGroup): Response
    {
        $events = $this->eventRepository->findByGroup($eventGroup);
        $discussions = $this->eventDiscussionRepository->findByGroup($eventGroup);
        $discussionComments = $this->eventDiscussionCommentRepository->findByGroup($eventGroup);

        $unorderedPosts = new ArrayCollection([...$events, ...$discussions, ...$discussionComments]);
        $posts = $unorderedPosts->matching(Criteria::create()->orderBy([
            'createdAt' => Criteria::DESC,
        ]));

        return $this->render('events/group/show.html.twig', [
            'eventGroup' => $eventGroup,
            'posts' => $posts,
        ]);
    }

    #[Route('/events/{id}', name: 'event_group_events', methods: [Request::METHOD_GET, Request::METHOD_POST])]
    public function events(EventGroup $eventGroup, Request $request): Response
    {
        return $this->render('events/group/events.html.twig', [
            'eventGroup' => $eventGroup,
        ]);
    }

    #[Route('/members/{id}', name: 'event_group_members', methods: [Request::METHOD_GET, Request::METHOD_POST])]
    public function members(EventGroup $eventGroup, Request $request): Response
    {
        return $this->render('events/group/members.html.twig', [
            'eventGroup' => $eventGroup,
        ]);
    }

    #[Route('/discussion/{id}', name: 'event_group_discussion', methods: [Request::METHOD_GET, Request::METHOD_POST])]
    public function discussion(EventGroup $eventGroup, Request $request): Response
    {
        $discussionsQuery = $this->eventDiscussionRepository->findByGroup($eventGroup, true);
        $discussionPagination = $this->paginator->paginate(
            target: $discussionsQuery,
            page: $request->query->getInt('page', 1),
            limit: 2
        );

        return $this->render('events/group/discussion.html.twig', [
            'eventGroup' => $eventGroup,
            'discussionPagination' => $discussionPagination,
        ]);
    }

    #[Route('/create', name: 'create_event_group', methods: [Request::METHOD_GET, Request::METHOD_POST])]
    public function create(Request $request, #[CurrentUser] User $currentUser): Response
    {
        $eventGroup = $this->eventGroupFactory->create(owner: $currentUser);
        $eventGroupForm = $this->createForm(EventGroupFormType::class, $eventGroup);
        $eventGroupForm->handleRequest($request);
        if ($eventGroupForm->isSubmitted() && $eventGroupForm->isValid()) {
            $eventGroupMember = $this->eventGroupMemberFactory->create(owner: $currentUser, eventGroup: $eventGroup, isApproved: true);
            $eventGroupMaintainerRole = $this->eventGroupRoleRepository->findOneBy([
                'title' => EventGroupRoleEnum::ROLE_GROUP_MAINTAINER,
            ]);
            $eventGroupCreatorRole = $this->eventGroupRoleRepository->findOneBy([
                'title' => EventGroupRoleEnum::ROLE_GROUP_CREATOR,
            ]);

            $eventGroupMember->addRole($eventGroupCreatorRole);
            $eventGroupMember->addRole($eventGroupMaintainerRole);
            $eventGroup->addEventGroupMember($eventGroupMember);
            $this->eventGroupRepository->save(entity: $eventGroup, flush: true);

            $this->addFlash('message', $this->translator->trans(''));
            return $this->redirectToRoute('event_group_show', [
                'id' => $eventGroup->getId(),
            ]);
        }

        return $this->render('events/group/create.html.twig', [
            'eventGroupForm' => $eventGroupForm,
        ]);
    }

    #[Route('/join/{id}', name: 'join_event_group', methods: [Request::METHOD_GET, Request::METHOD_POST])]
    public function joinEventGroup(Request $request, EventGroup $eventGroup, #[CurrentUser] User $currentUser): Response
    {
        if ($eventGroup->getIsMember($currentUser)) {
            $this->addFlash(FlashEnum::MESSAGE->value, $this->translator->trans('already-member'));
            return $this->redirectToRoute('event_group_show', [
                'id' => $eventGroup->getId(),
            ]);
        }

        $eventGroupMember = $this->eventGroupMemberFactory->create(owner: $currentUser, eventGroup: $eventGroup, isApproved: true);
        $eventGroup->addEventGroupMember($eventGroupMember);
        $this->eventGroupRepository->save($eventGroup, true);
        $this->addFlash(FlashEnum::MESSAGE->value, $this->translator->trans('group-joined'));

        return $this->redirectToRoute('event_group_show', [
            'id' => $eventGroup->getId(),
        ]);
    }

    #[Route('/leave/{id}', name: 'leave_event_group', methods: [Request::METHOD_GET, Request::METHOD_POST])]
    public function leaveEventGroup(Request $request, EventGroup $eventGroup, #[CurrentUser] User $currentUser): Response
    {
        $eventGroupMember = $eventGroup->getMember($currentUser);
        if (! $eventGroupMember instanceof EventGroupMember) {
            $this->addFlash(FlashEnum::MESSAGE->value, $this->translator->trans('not-group-memeber'));
            return $this->redirectToRoute('event_group_show', [
                'id' => $eventGroup->getId(),
            ]);
        }

        $eventGroup->removeEventGroupMember($eventGroupMember);
        $this->eventGroupRepository->save($eventGroup, true);
        $this->addFlash(FlashEnum::MESSAGE->value, $this->translator->trans('group-left'));
        return $this->redirectToRoute('event_group_show', [
            'id' => $eventGroup->getId(),
        ]);
    }

    #[Route('/graph/{id}', name: 'event_group_activity_graph', methods: [Request::METHOD_GET])]
    public function groupActivityGraph(EventGroup $eventGroup): Response
    {
        $publishedEvents = $this->eventRepository->findPublishEvents($eventGroup);
        $eventGroupAnalysis = $this->eventGroupAnalyzer->analyze($publishedEvents);

        return $this->render('events/group/activity-graph.html.twig', [
            'eventGroupAnalysis' => $eventGroupAnalysis,
        ]);
    }
}

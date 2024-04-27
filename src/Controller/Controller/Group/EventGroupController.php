<?php

declare(strict_types=1);

namespace App\Controller\Controller\Group;

use App\DataTransferObject\EventGroupFilterDto;
use App\Entity\EventGroup\EventGroup;
use App\Entity\EventGroup\EventGroupJoinRequest;
use App\Entity\EventGroup\EventGroupMember;
use App\Entity\User\User;
use App\Enum\EventGroupRoleEnum;
use App\Enum\FlashEnum;
use App\Factory\EventGroup\EventGroupJoinRequestFactory;
use App\Factory\EventGroup\EventGroupMemberFactory;
use App\Form\Filter\EventGroupFilterType;
use App\Form\Form\EventGroup\EventGroupSettingsFormType;
use App\Repository\Event\EventGroupRepository;
use App\Repository\Event\EventRepository;
use App\Repository\EventGroup\EventGroupDiscussionCommentRepository;
use App\Repository\EventGroup\EventGroupDiscussionRepository;
use App\Repository\EventGroup\EventGroupInvitationRepository;
use App\Repository\EventGroup\EventGroupJoinRequestRepository;
use App\Repository\EventGroup\EventGroupMemberRepository;
use App\Repository\EventGroup\EventGroupRoleRepository;
use App\Repository\Poll\PollRepository;
use App\Security\Voter\EventGroupVoter;
use App\Service\EventGroupAnalyzer\EventActivityAnalyzer;
use App\Service\ImageUploadService\ImageService;
use Carbon\CarbonImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/groups')]
class EventGroupController extends AbstractController
{
    public function __construct(
        private readonly EventGroupRepository $eventGroupRepository,
        private readonly EventGroupJoinRequestRepository $eventGroupJoinRequestRepository,
        private readonly EventGroupInvitationRepository $eventGroupInvitationRepository,
        private readonly EventGroupMemberRepository $eventGroupMemberRepository,
        private readonly EventGroupJoinRequestFactory $eventGroupJoinRequestFactory,
        private readonly EventRepository $eventRepository,
        private readonly EventGroupMemberFactory $eventGroupMemberFactory,
        private readonly EventGroupRoleRepository $eventGroupRoleRepository,
        private readonly PollRepository $pollRepository,
        private readonly EventGroupDiscussionRepository $eventDiscussionRepository,
        private readonly EventGroupDiscussionCommentRepository $eventDiscussionCommentRepository,
        private readonly PaginatorInterface $paginator,
        private readonly TranslatorInterface $translator,
        private readonly EventActivityAnalyzer $eventGroupAnalyzer,
        private readonly ImageService $imageUploadService
    ) {
    }

    #[Route('/show/{id}', name: 'event_group_show', methods: ['GET', 'POST'])]
    public function show(EventGroup $eventGroup): Response
    {
        $events = $this->eventRepository->findByGroup($eventGroup);
        $discussions = $this->eventDiscussionRepository->findByGroup($eventGroup);
        $discussionComments = $this->eventDiscussionCommentRepository->findByGroup($eventGroup);
        $polls = $this->pollRepository->findByGroup($eventGroup);

        $unorderedPosts = new ArrayCollection([...$events, ...$discussions, ...$discussionComments, ...$polls]);
        $posts = $unorderedPosts->matching(Criteria::create()->orderBy([
            'createdAt' => Criteria::DESC,
        ]));

        return $this->render('groups/show.html.twig', [
            'eventGroup' => $eventGroup,
            'posts' => $posts,
        ]);
    }

    /**
     * @throws \Exception
     */
    #[Route('/', name: 'event_groups', methods: ['GET', 'POST'])]
    public function index(Request $request): Response
    {
        $eventFilterDto = new EventGroupFilterDto();
        $eventGroupFilter = $this->createForm(EventGroupFilterType::class, $eventFilterDto);
        $eventGroups = $this->eventGroupRepository->findByGroupFilter($eventFilterDto, true);
        $eventGroupPagination = $this->paginator->paginate(target: $eventGroups, page: $request->query->getInt('groups-page', 1), limit: 3, options: [
            'pageParameterName' => 'groups-page',
        ]);

        $eventGroupFilter->handleRequest($request);
        if ($eventGroupFilter->isSubmitted() && $eventGroupFilter->isValid()) {
            $eventGroups = $this->eventGroupRepository->findByGroupFilter($eventFilterDto, true);
            $eventGroupPagination = $this->paginator->paginate(target: $eventGroups, page: $request->query->getInt('groups-page', 1), limit: 3, options: [
                'pageParameterName' => 'groups-page',
            ]);

            return $this->render('groups/index.html.twig', [
                'eventGroupPagination' => $eventGroupPagination,
                'eventGroupFilter' => $eventGroupFilter,
            ]);
        }

        return $this->render('groups/index.html.twig', [
            'eventGroupPagination' => $eventGroupPagination,
            'eventGroupFilter' => $eventGroupFilter,
        ]);
    }

    #[Route('/create/group', name: 'create_event_group', methods: [Request::METHOD_GET, Request::METHOD_POST])]
    public function create(): Response
    {
        return $this->render('/groups/create.html.twig');
    }

    #[Route('/events/{id}', name: 'event_group_events', methods: [Request::METHOD_GET, Request::METHOD_POST])]
    public function events(EventGroup $eventGroup, Request $request): Response
    {
        return $this->render('groups/events.html.twig', [
            'eventGroup' => $eventGroup,
        ]);
    }

    #[Route('/members/{id}', name: 'event_group_members', methods: [Request::METHOD_GET, Request::METHOD_POST])]
    public function members(EventGroup $eventGroup, Request $request): Response
    {
        $groupMembersQuery = $this->eventGroupMemberRepository->findByGroup(eventGroup: $eventGroup, isQuery: true);
        $eventGroupMembersPagination = $this->paginator->paginate(
            target: $groupMembersQuery,
            page: $request->query->getInt('members-page', 1),
            limit: 2,
            options: [
                'pageParameterName' => 'members-page',
            ]
        );

        $groupJoinRequestQuery = $this->eventGroupJoinRequestRepository->findByGroup(eventGroup: $eventGroup, isQuery: true);
        $eventGroupJoinRequestPagination = $this->paginator->paginate(
            target: $groupJoinRequestQuery,
            page: $request->query->getInt('join-request-page', 1),
            limit: 2,
            options: [
                'pageParameterName' => 'join-request-page',
            ]
        );

        $groupInvitationsQuery = $this->eventGroupInvitationRepository->findByGroup(eventGroup: $eventGroup, isQuery: true);
        $eventGroupInvitationsPagination = $this->paginator->paginate(
            target: $groupInvitationsQuery,
            page: $request->query->getInt('invitations-page', 1),
            limit: 2,
            options: [
                'pageParameterName' => 'invitations-page',
            ]
        );

        return $this->render('groups/members.html.twig', [
            'eventGroupMembersPagination' => $eventGroupMembersPagination,
            'eventGroupJoinRequestPagination' => $eventGroupJoinRequestPagination,
            'eventGroupInvitationsPagination' => $eventGroupInvitationsPagination,
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

        return $this->render('groups/discussion.html.twig', [
            'eventGroup' => $eventGroup,
            'discussionPagination' => $discussionPagination,
        ]);
    }

    #[Route('/request/cancel/join/{id}', name: 'cancel_event_group_request_join', methods: [Request::METHOD_GET, Request::METHOD_POST])]
    public function cancelRequestJoinEventGroup(EventGroupJoinRequest $eventGroupJoinRequest, #[CurrentUser] User $currentUser): Response
    {
        $eventGroup = $eventGroupJoinRequest->getEventGroup();
        $this->eventGroupJoinRequestRepository->remove($eventGroupJoinRequest, true);
        $this->addFlash(FlashEnum::MESSAGE->value, $this->translator->trans('request-canceled'));
        return $this->redirectToRoute('event_group_show', [
            'id' => $eventGroup->getId(),
        ]);
    }

    #[Route('/request/join/{id}', name: 'request_join_event_group', methods: [Request::METHOD_GET, Request::METHOD_POST])]
    public function requestJoinEventGroup(Request $request, EventGroup $eventGroup, #[CurrentUser] User $currentUser): Response
    {
        if ($eventGroup->getIsMember($currentUser)) {
            $this->addFlash(FlashEnum::MESSAGE->value, $this->translator->trans('already-member'));
            return $this->redirectToRoute('event_group_show', [
                'id' => $eventGroup->getId(),
            ]);
        }

        $eventGroupRequest = $this->eventGroupJoinRequestRepository->findOneBy([
            'eventGroup' => $eventGroup,
            'owner' => $currentUser,
        ]);

        if ($eventGroupRequest instanceof EventGroupJoinRequest) {
            $this->addFlash(FlashEnum::MESSAGE->value, $this->translator->trans('request-already-sent'));
            return $this->redirectToRoute('event_group_show', [
                'id' => $eventGroup->getId(),
            ]);
        }

        $eventGroupRequest = $this->eventGroupJoinRequestFactory->create(eventGroup: $eventGroup, owner: $currentUser);
        $this->eventGroupJoinRequestRepository->save($eventGroupRequest, true);
        $this->addFlash(FlashEnum::MESSAGE->value, $this->translator->trans('request-sent'));
        return $this->redirectToRoute('event_group_show', [
            'id' => $eventGroup->getId(),
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

        $eventGroupMember = $this->eventGroupMemberFactory->create(owner: $currentUser, eventGroup: $eventGroup, approvedAt: new CarbonImmutable());
        $memberRole = $this->eventGroupRoleRepository->findOneBy([
            'title' => EventGroupRoleEnum::ROLE_GROUP_MEMBER,
        ]);
        $eventGroupMember->addRole($memberRole);
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
        $eventGroupMember = $this->eventGroupMemberRepository->findByOwner(user: $currentUser, group: $eventGroup);
        if (! $eventGroupMember instanceof EventGroupMember) {
            $this->addFlash(FlashEnum::MESSAGE->value, $this->translator->trans('not-group-memeber'));
            return $this->redirectToRoute('event_group_show', [
                'id' => $eventGroup->getId(),
            ]);
        }

        $this->eventGroupMemberRepository->remove($eventGroupMember, true);
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

        return $this->render('groups/activity-graph.html.twig', [
            'eventGroupAnalysis' => $eventGroupAnalysis,
        ]);
    }

    #[Route('/settings/{id}', name: 'event_group_settings', methods: [Request::METHOD_GET, Request::METHOD_POST])]
    public function settings(EventGroup $eventGroup, Request $request): Response
    {
        $this->isGranted(EventGroupVoter::EDIT_GROUP, $eventGroup);

        $eventGroupSettingForm = $this->createForm(EventGroupSettingsFormType::class, $eventGroup);
        $eventGroupSettingForm->handleRequest($request);
        if ($eventGroupSettingForm->isSubmitted() && $eventGroupSettingForm->isValid()) {
            $image = $eventGroupSettingForm->get('image')->getData();
            if (! empty($image)) {
                $eventGroup->setBase64Image($this->imageUploadService->processAvatar($image)->getEncoded());
            }
            $this->eventGroupRepository->save(entity: $eventGroup, flush: true);

            $this->addFlash('message', $this->translator->trans(''));
            return $this->redirectToRoute('event_group_settings', [
                'id' => $eventGroup->getId(),
            ]);
        }

        return $this->render('groups/settings.twig', [
            'eventGroupSettingForm' => $eventGroupSettingForm,
            'eventGroup' => $eventGroup,
        ]);
    }
}

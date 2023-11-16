<?php

namespace App\Controller\Controller\Event;

use App\Entity\Event\Event;
use App\Entity\EventGroup\EventGroup;
use App\Entity\User;
use App\Enum\EventGroupRoleEnum;
use App\Factory\EventGroup\EventGroupMemberFactory;
use App\Form\Form\EventGroupFormType;
use App\Repository\Event\EventGroupRepository;
use App\Repository\EventGroupRoleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/groups')]
class EventGroupController extends AbstractController
{


    public function __construct(
        private readonly EventGroupRepository $eventGroupRepository,
        private readonly EventGroupMemberFactory $eventGroupMemberFactory,
        private readonly EventGroupRoleRepository $eventGroupRoleRepository
    )
    {
    }

    #[Route('/show/{id}', name: 'event_group_show', methods: [Request::METHOD_GET, Request::METHOD_POST])]
    public function show(EventGroup $eventGroup, Request $request): Response
    {
        return $this->render('events/group/show.html.twig', [
            'eventGroup' => $eventGroup
        ]);
    }

    #[Route('/events/{id}', name: 'event_group_events', methods: [Request::METHOD_GET, Request::METHOD_POST])]
    public function events(EventGroup $eventGroup, Request $request): Response
    {
        return $this->render('events/group/events.html.twig', [
            'eventGroup' => $eventGroup
        ]);
    }

    #[Route('/members/{id}', name: 'event_group_members', methods: [Request::METHOD_GET, Request::METHOD_POST])]
    public function members(EventGroup $eventGroup, Request $request): Response
    {
        return $this->render('events/group/members.html.twig', [
            'eventGroup' => $eventGroup
        ]);
    }

    #[Route('/discussion/{id}', name: 'event_group_discussion', methods: [Request::METHOD_GET, Request::METHOD_POST])]
    public function discussion(EventGroup $eventGroup, Request $request): Response
    {
        return $this->render('events/group/discussion.html.twig', [
            'eventGroup' => $eventGroup
        ]);
    }

    #[Route('/create', name: 'create_event_group', methods: [Request::METHOD_GET, Request::METHOD_POST])]
    public function create(Request $request, #[CurrentUser] User $currentUser): Response
    {
        $eventGroup = new EventGroup();
        $eventGroup->setOwner($currentUser);
        $eventGroupForm = $this->createForm(EventGroupFormType::class, $eventGroup);
        $eventGroupForm->handleRequest($request);
        if ($eventGroupForm->isSubmitted() && $eventGroupForm->isValid()) {

            /**
             * @var array<int, Event> $events
             */
            $events = $eventGroupForm->get('events')->getData();
            foreach ($events as $event){
                $event->setEventGroup($eventGroup);
            }

            $eventGroupMaintainerRole = $this->eventGroupRoleRepository->findOneBy(['title' => EventGroupRoleEnum::ROLE_GROUP_MAINTAINER->name]);
            $eventGroupCreatorRole = $this->eventGroupRoleRepository->findOneBy(['title' => EventGroupRoleEnum::ROLE_GROUP_CREATOR->name]);
            $eventGroupMember = $this->eventGroupMemberFactory->create(owner: $currentUser,eventGroup: $eventGroup);
            $eventGroupMember->addRole($eventGroupMaintainerRole);
            $eventGroupMember->addRole($eventGroupCreatorRole);

            $eventGroup->addEventGroupMember($eventGroupMember);

            $this->eventGroupRepository->save(entity: $eventGroupForm->getData(), flush: true);
            $this->addFlash('message', 'event group created');
            return $this->redirectToRoute('show_event_group', ['id' => $eventGroup->getId()]);
        }

        return $this->render('events/group/create.html.twig', [
            'eventGroupForm' => $eventGroupForm
        ]);
    }

}
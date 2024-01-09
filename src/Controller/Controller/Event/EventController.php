<?php

declare(strict_types=1);

namespace App\Controller\Controller\Event;

use App\DataTransferObject\EventFilterDto;
use App\Entity\Event\Event;
use App\Entity\Event\EventEmailInvitation;
use App\Entity\User;
use App\Enum\EventOrganiserRoleEnum;
use App\Enum\FlashEnum;
use App\Factory\Event\EventFactory;
use App\Factory\Event\EventOrganiserFactory;
use App\Factory\EventCancellationFactory;
use App\Factory\ImageCollectionFactory;
use App\Factory\ImageFactory;
use App\Form\Filter\EventFilterType;
use App\Form\Form\EventCancellationFormType;
use App\Form\Form\EventFormType;
use App\Form\Form\ImageFormType;
use App\Repository\Event\EventGroupRepository;
use App\Repository\Event\EventRepository;
use App\Repository\Event\EventRoleRepository;
use App\Repository\EventEmailInvitationRepository;
use App\Repository\ImageCollectionRepository;
use App\Security\Voter\EventVoter;
use App\Service\ImageUploadService\ImageService;
use Carbon\CarbonImmutable;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Contracts\Translation\TranslatorInterface;

class EventController extends AbstractController
{
    public function __construct(
        private readonly EventRepository           $eventRepository,
        private readonly ImageService              $imageUploadService,
        private readonly ImageFactory              $imageFactory,
        private readonly ImageCollectionFactory    $imageCollectionFactory,
        private readonly ImageCollectionRepository $imageCollectionRepository,
        private readonly PaginatorInterface        $paginator,
        private readonly EventOrganiserFactory     $eventCrewMemberFactory,
        private readonly EventRoleRepository       $eventRoleRepository,
        private readonly EventFactory              $eventFactory,
        private readonly EventCancellationFactory  $eventCancellationFactory,
        private readonly TranslatorInterface       $translator,
        private readonly EventGroupRepository      $eventGroupRepository,
        private readonly EventEmailInvitationRepository      $eventEmailInvitationRepository
    ) {
    }

    #[Route(path: '/', name: 'events')]
    public function index(Request $request): Response
    {
        $eventFilterDto = new EventFilterDto();
        $eventFilter = $this->createForm(EventFilterType::class, $eventFilterDto);
        $eventFilter->handleRequest($request);
        $events = $this->eventRepository->findByFilter(eventFilterDto: $eventFilterDto, isQuery: true);
        $groups = $this->eventGroupRepository->findByEventFilter(eventFilterDto: $eventFilterDto, isQuery: true);
        $eventPagination = $this->paginator->paginate(target: $events, page: $request->query->getInt('events', 1), limit: 30);
        $eventGroupPagination = $this->paginator->paginate(target: $groups, page: $request->query->getInt('groups', 1), limit: 30);

        if ($eventFilter->isSubmitted() && $eventFilter->isValid()) {
            $events = $this->eventRepository->findByFilter(eventFilterDto: $eventFilterDto, isQuery: true);
            $groups = $this->eventGroupRepository->findByEventFilter(eventFilterDto: $eventFilterDto, isQuery: true);
            $eventPagination = $this->paginator->paginate(target: $events, page: $request->query->getInt('page', 1), limit: 30);
            $eventGroupPagination = $this->paginator->paginate(target: $groups, page: $request->query->getInt('groups', 1), limit: 30);

            return $this->render('events/index.html.twig', [
                'period' => $eventFilterDto->getPeriod(),
                'eventFilter' => $eventFilter,
                'eventPagination' => $eventPagination,
                'eventGroupPagination' => $eventGroupPagination,
            ]);
        }

        return $this->render('events/index.html.twig', [
            'period' => $eventFilterDto->getPeriod(),
            'eventFilter' => $eventFilter,
            'eventPagination' => $eventPagination,
            'eventGroupPagination' => $eventGroupPagination,
        ]);
    }

    #[Route(path: '/events/create', name: 'create_event')]
    public function create(Request $request, #[CurrentUser] User $currentUser): Response
    {
        $eventGroupId = $request->get('eventGroup');
        $eventGroup = null;
        if (! empty($eventGroupId)) {
            $eventGroup = $this->eventGroupRepository->find($eventGroupId);
        }
        $event = $this->eventFactory->create(owner: $currentUser, eventGroup: $eventGroup);
        $eventForm = $this->createForm(EventFormType::class, $event);
        $eventForm->handleRequest($request);
        if ($eventForm->isSubmitted() && $eventForm->isValid()) {
            $image = $eventForm->get('image')->getData();

            /** @var Event $event */
            $event = $eventForm->getData();
            $event->setBase64Image(
                $this->imageUploadService->processPhoto($image)->getEncoded()
            );

            $adminRole = $this->eventRoleRepository->findOneBy([
                'title' => EventOrganiserRoleEnum::ROLE_EVENT_MANAGER,
            ]);
            $eventOrganiser = $this->eventCrewMemberFactory->create(owner: $currentUser, event: $event, roles: [$adminRole]);
            $event->addEventOrganiser($eventOrganiser);

            $this->eventRepository->save(entity: $eventForm->getData(), flush: true);
            return $this->redirectToRoute('show_event', [
                'id' => $event->getId(),
            ]);
        }

        return $this->render('events/create.html.twig', [
            'eventForm' => $eventForm->createView(),
        ]);
    }

    #[Route(path: '/events/{id}', name: 'show_event')]
    public function show(Request $request, Event $event, #[CurrentUser] null|User $currentUser): Response
    {
        $invitationToken = $request->get('token');
        $invitation = null;
        if ($invitationToken) {
            $invitation = $this->eventEmailInvitationRepository->findOneBy([
                'token' => $invitationToken,
            ]);
        }

        $imageForm = $this->createForm(ImageFormType::class);
        $imageForm->handleRequest($request);
        if ($imageForm->isSubmitted() && $imageForm->isValid()) {
            return $this->handleEventImageUploadForm(imageForm: $imageForm, currentUser: $currentUser, event: $event);
        }

        return $this->render('events/show.html.twig', [
            'imageForm' => $imageForm,
            'event' => $event,
            'invitation' => $invitation,
        ]);
    }

    #[Route(path: '/events/email-invitation/{token}', name: 'event_invitation')]
    public function emailInvitation(EventEmailInvitation $emailInvitation): Response
    {
        return $this->render('events/email-invitation.html.twig', [
            'emailInvitation' => $emailInvitation,
        ]);
    }

    #[Route(path: '/events/settings/{id}', name: 'event_settings')]
    public function settings(Event $event, Request $request, #[CurrentUser] User $currentUser): Response
    {
        $eventForm = $this->createForm(EventFormType::class, $event, [
            'event' => $event,
        ]);
        $eventForm->handleRequest($request);
        if ($eventForm->isSubmitted() && $eventForm->isValid()) {
            $image = $eventForm->get('image')->getData();
            if (! empty($image)) {
                /** @var Event $event */
                $event = $eventForm->getData();
                $event->setBase64Image(
                    $this->imageUploadService->processPhoto($image)->getEncoded()
                );
            }

            $this->eventRepository->save(entity: $eventForm->getData(), flush: true);
            return $this->redirectToRoute('event_settings', [
                'id' => $event->getId(),
            ]);
        }

        return $this->render('events/settings.html.twig', [
            'eventForm' => $eventForm,
            'event' => $event,
        ]);
    }

    #[Route(path: '/events/cancel/{id}', name: 'cancel_event')]
    public function cancelEvent(Event $event, Request $request, #[CurrentUser] User $currentUser): Response
    {
        $eventCancellation = $this->eventCancellationFactory->create(event: $event, owner: $currentUser);
        $eventCancellationForm = $this->createForm(EventCancellationFormType::class, $eventCancellation);
        $eventCancellationForm->handleRequest($request);
        if ($eventCancellationForm->isSubmitted() && $eventCancellationForm->isValid()) {
            if (CarbonImmutable::now()->diffInRealMinutes($event->getStartAt()) < 30) {
                $this->addFlash(FlashEnum::MESSAGE->value, $this->translator->trans('too-close-to-event-to-cancel'));
                return $this->redirectToRoute('show_event', [
                    'id' => $event->getId(),
                ]);
            }

            $event->setEventCancellation($eventCancellation);
            $this->eventRepository->save($event, true);
            $this->addFlash(FlashEnum::MESSAGE->value, $this->translator->trans('event-canceled'));
        }

        return $this->render('events/cancel.html.twig', [
            'eventCancellationForm' => $eventCancellationForm,
            'event' => $event,
        ]);
    }

    #[Route(path: '/events/publish/{id}', name: 'publish_event')]
    public function publish(Event $event, Request $request, #[CurrentUser] User $currentUser): Response
    {
        $this->isGranted(EventVoter::PUBLISH_EVENT, $event);

        $event->setIsPublished(true);
        $this->eventRepository->save($event, true);
        $this->addFlash(FlashEnum::MESSAGE->value, $this->translator->trans('event-published'));
        return $this->redirectToRoute('show_event', [
            'id' => $event->getId(),
        ]);
    }

    private function handleEventImageUploadForm(FormInterface $imageForm, null|User $currentUser, Event $event): Response
    {
        $UploadedImageFiles = $imageForm->get('images')->getData();
        $images = [];
        foreach ($UploadedImageFiles as $img) {
            $base64 = $this->imageUploadService->processPhoto($img);
            $image = $this->imageFactory->create(dataUrl: $base64->getEncoded());
            $images[] = $image;
        }
        $imageCollection = $this->imageCollectionFactory->create(images: $images, owner: $currentUser, event: $event);
        $this->imageCollectionRepository->save($imageCollection, true);

        $this->addFlash('message', 'images uploaded');
        return $this->redirectToRoute('show_event', [
            'id' => $event->getId(),
        ]);
    }
}

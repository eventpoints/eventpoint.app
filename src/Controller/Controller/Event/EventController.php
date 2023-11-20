<?php

declare(strict_types=1);

namespace App\Controller\Controller\Event;

use App\DataTransferObject\EventFilterDto;
use App\Entity\Event\Event;
use App\Entity\Event\EventEmailInvitation;
use App\Entity\User;
use App\Enum\EventRoleEnum;
use App\Factory\Event\EventFactory;
use App\Factory\Event\EventOrganiserFactory;
use App\Factory\ImageCollectionFactory;
use App\Factory\ImageFactory;
use App\Form\Filter\EventFilterType;
use App\Form\Form\EventFormType;
use App\Form\Form\ImageFormType;
use App\Repository\Event\EventRepository;
use App\Repository\Event\EventRoleRepository;
use App\Repository\ImageCollectionRepository;
use App\Service\ImageUploadService\ImageUploadService;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class EventController extends AbstractController
{
    public function __construct(
        private readonly EventRepository           $eventRepository,
        private readonly ImageUploadService        $imageUploadService,
        private readonly ImageFactory              $imageFactory,
        private readonly ImageCollectionFactory    $imageCollectionFactory,
        private readonly ImageCollectionRepository $imageCollectionRepository,
        private readonly PaginatorInterface        $paginator,
        private readonly EventOrganiserFactory     $eventCrewMemberFactory,
        private readonly EventRoleRepository       $eventRoleRepository,
        private readonly EventFactory       $eventFactory
    ) {
    }

    #[Route(path: '/', name: 'events')]
    public function index(Request $request): Response
    {
        $eventFilterDto = new EventFilterDto();
        $eventFilter = $this->createForm(EventFilterType::class, $eventFilterDto);
        $eventFilter->handleRequest($request);
        $events = $this->eventRepository->findByFilter(eventFilterDto: $eventFilterDto, isQuery: true);
        $eventPagination = $this->paginator->paginate(
            target: $events,
            page: $request->query->getInt('page', 1),
            limit: 3
        );

        if ($eventFilter->isSubmitted() && $eventFilter->isValid()) {
            $events = $this->eventRepository->findByFilter(eventFilterDto: $eventFilterDto, isQuery: true);
            $eventPagination = $this->paginator->paginate(
                target: $events,
                page: $request->query->getInt('page', 1),
                limit: 3
            );
        }

        return $this->render('events/index.html.twig', [
            'eventFilter' => $eventFilter,
            'eventPagination' => $eventPagination,
        ]);
    }

    #[Route(path: '/events/create', name: 'create_event')]
    public function create(Request $request, #[CurrentUser] User $currentUser): Response
    {
        $event = $this->eventFactory->create(owner: $currentUser);
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
                'name' => EventRoleEnum::ROLE_EVENT_MANAGER->value,
            ]);
            $eventOrganiser = $this->eventCrewMemberFactory->create(owner: $currentUser, event: $event, roles: [$adminRole]);
            $event->addEventOrganiser($eventOrganiser);

            $this->eventRepository->save(entity: $eventForm->getData(), flush: true);
            return $this->redirectToRoute('events');
        }

        return $this->render('events/create.html.twig', [
            'eventForm' => $eventForm->createView(),
        ]);
    }

    #[Route(path: '/events/{id}', name: 'show_event')]
    public function show(Request $request, Event $event, #[CurrentUser] null|User $currentUser): Response
    {
        $imageForm = $this->createForm(ImageFormType::class);
        $imageForm->handleRequest($request);
        if ($imageForm->isSubmitted() && $imageForm->isValid()) {
            $this->handleEventImageUploadForm(imageForm: $imageForm, currentUser: $currentUser, event: $event);
        }

        return $this->render('events/show.html.twig', [
            'imageForm' => $imageForm,
            'event' => $event,
        ]);
    }

    #[Route(path: '/events/email-invitation/{token}', name: 'event_invitation')]
    public function emailInvitation(EventEmailInvitation $emailInvitation): Response
    {
        return $this->render('events/email-invitation.html.twig', [
            'emailInvitation' => $emailInvitation,
        ]);
    }

    private function handleEventImageUploadForm(
        FormInterface $imageForm,
        User          $currentUser,
        Event         $event
    ): Response {
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

<?php

declare(strict_types=1);

namespace App\Controller\Controller\User;

use App\Entity\User;
use App\Repository\Event\EventRepository;
use App\Service\EventGroupAnalyzer\EventActivityAnalyzer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/profile')]
class ProfileController extends AbstractController
{
    public function __construct(
        private readonly EventRepository $eventRepository,
        private readonly EventActivityAnalyzer $eventActivityAnalyzer
    ) {
    }

    #[Route(path: '/{id}', name: 'show_profile')]
    public function index(User $user): Response
    {
        $events = $this->eventRepository->findAssociatedByUser($user);
        $userEventActivity = $this->eventActivityAnalyzer->analyze($events);

        return $this->render('profile/show.html.twig', [
            'userEventActivity' => $userEventActivity,
            'user' => $user,
        ]);
    }
}

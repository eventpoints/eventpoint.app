<?php

declare(strict_types=1);

namespace App\Twig\Component;

use App\Entity\Event\Event;
use App\Entity\User\User;
use App\Factory\Event\EventReviewFactory;
use App\Repository\Event\EventReviewRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent('event_rating_form_component')]
class EventReviewFormComponent extends AbstractController
{
    use DefaultActionTrait;

    #[LiveProp(writable: true)]
    public null|Event $event = null;

    #[LiveProp(writable: true)]
    public null|string $expectationRating = null;

    #[LiveProp(writable: true)]
    public null|string $venueRating = null;

    #[LiveProp(writable: true)]
    public null|string $contentRating = null;

    #[LiveProp(writable: true)]
    public null|string $hostRating = null;

    #[LiveProp(writable: true)]
    public null|string $guestRating = null;

    public function __construct(
        private readonly EventReviewRepository $eventReviewRepository,
        private readonly EventReviewFactory $eventReviewFactory
    ) {
    }

    #[LiveAction]
    public function setVenueRating(#[LiveArg] null|string $rating): void
    {
        $this->venueRating = $rating;
    }

    #[LiveAction]
    public function setContentRating(#[LiveArg] null|string $rating): void
    {
        $this->contentRating = $rating;
    }

    #[LiveAction]
    public function setHostRating(#[LiveArg] null|string $rating): void
    {
        $this->hostRating = $rating;
    }

    #[LiveAction]
    public function setGuestRating(#[LiveArg] null|string $rating): void
    {
        $this->guestRating = $rating;
    }

    #[LiveAction]
    public function setExpectationRating(#[LiveArg] null|string $rating, #[CurrentUser] User $currentUser): void
    {
        $this->expectationRating = $rating;
        $this->submit($currentUser);
    }

    private function submit(User $user): void
    {
        $eventReview = $this->eventReviewFactory->create(
            event: $this->event,
            owner: $user,
            contentRating: $this->contentRating,
            venueRating: $this->venueRating,
            hostRating: $this->hostRating,
            guestRating: $this->guestRating,
            expectationRating: $this->guestRating
        );
        $this->eventReviewRepository->save($eventReview, true);
    }
}

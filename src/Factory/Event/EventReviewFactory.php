<?php

declare(strict_types=1);

namespace App\Factory\Event;

use App\Entity\Event\Event;
use App\Entity\EventReview;
use App\Entity\User;

final class EventReviewFactory
{
    public function create(
        null|Event $event = null,
        null|User $owner = null,
        null|string $contentRating = null,
        null|string $venueRating = null,
        null|string $hostRating = null,
        null|string $guestRating = null,
        null|string $expectationRating = null,
    ): EventReview {
        $review = new EventReview();
        $review->setEvent($event);
        $review->setOwner($owner);
        $review->setContentRating($contentRating);
        $review->setVenueRating($venueRating);
        $review->setHostRating($hostRating);
        $review->setGuestRating($guestRating);
        $review->setExpectationRating($expectationRating);
        return $review;
    }
}

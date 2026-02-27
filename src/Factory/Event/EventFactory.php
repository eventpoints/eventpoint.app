<?php

declare(strict_types=1);

namespace App\Factory\Event;

use App\DataTransferObject\Event\EventDto;
use App\Entity\Event\Event;
use App\Entity\Event\EventInvitation;
use App\Entity\Event\EventParticipant;
use App\Entity\EventGroup\EventGroup;
use App\Entity\User\User;
use Carbon\CarbonImmutable;

final readonly class EventFactory
{
    public function __construct() {}


    public function create(
        null|string $title = null,
        null|string $address = null,
        null|CarbonImmutable $startAt = null,
        null|CarbonImmutable $endAt = null,
        null|string $latitude = null,
        null|string $longitude = null,
        null|string $description = null,
        null|bool $isPrivate = null,
        null|User $owner = null,
        null|EventGroup $eventGroup = null
    ): Event {
        $event = new Event();
        $event->setTitle($title);
        $event->setAddress($address);
        $event->setDescription($description);
        $event->setStartAt($startAt);
        $event->setEndAt($endAt);
        $event->setLatitude($latitude);
        $event->setLongitude($longitude);
        $event->setIsPrivate($isPrivate);
        $event->setOwner($owner);
        $event->setEventGroup($eventGroup);
        return $event;
    }

    /**
     * @param array<int,EventParticipant> $eventParticipants
     */
    public function addEventParticipants(array $eventParticipants, Event $event): void
    {
        foreach ($eventParticipants as $eventParticipant) {
            $eventParticipant->setEvent($event);
            $event->addEventParticipant($eventParticipant);
        }
    }

    /**
     * @param array<int,EventInvitation> $eventInvites
     */
    public function addEventInvitations(array $eventInvites, Event $event): void
    {
        foreach ($eventInvites as $eventInvite) {
            $eventInvite->setEvent($event);
            $event->addEventInvitation($eventInvite);
        }
    }

    public function createFromDTOs(
        EventDto $eventDto,
    ): Event {
        $event = new Event(
            title: $eventDto->getTitle(),
            startAt: $eventDto->getStartAt(),
            endAt: $eventDto->getEndAt(),
            description: $eventDto->getDescription(),
            latitude: $eventDto->getLatitude(),
            longitude: $eventDto->getLongitude(),
            isPrivate: $eventDto->isPrivate(),
            address: $eventDto->getAddress(),
            eventGroup: $eventDto->getEventGroup(),
        );

        foreach ($eventDto->getCategories() as $category) {
            $event->addCategory($category);
        }

        return $event;
    }
}

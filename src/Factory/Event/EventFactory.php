<?php

declare(strict_types=1);

namespace App\Factory\Event;

use App\Entity\Event\Event;
use App\Entity\Event\EventInvitation;
use App\Entity\Event\EventOrganiser;
use App\Entity\Event\EventParticipant;
use App\Entity\Event\EventRejection;
use App\Entity\Event\EventRequest;
use App\Entity\User;
use Carbon\CarbonImmutable;

final class EventFactory
{
    public function create(
        null|string            $title = null,
        null|string            $address = null,
        null|CarbonImmutable $startAt = null,
        null|CarbonImmutable $endAt = null,
        null|string            $base64Image = null,
        null|string            $latitude = null,
        null|string            $longitude = null,
        null|string            $description = null,
        null|bool              $isPrivate = null,
        null|User              $owner = null,
    ): Event {
        $event = new Event();
        $event->setTitle($title);
        $event->setAddress($address);
        $event->setDescription($description);
        $event->setStartAt($startAt);
        $event->setEndAt($endAt);
        $event->setBase64Image($base64Image);
        $event->setLatitude($latitude);
        $event->setLongitude($longitude);
        $event->setIsPrivate($isPrivate);
        $event->setOwner($owner);
        return $event;
    }

    /**
     * @param array<int,EventOrganiser> $eventCrewMembers
     */
    public function addEventOrganisers(array $eventCrewMembers, Event $event): void
    {
        foreach ($eventCrewMembers as $eventCrewMember) {
            $eventCrewMember->setEvent($event);
            $event->addEventOrganiser($eventCrewMember);
        }
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

    /**
     * @param array<int,EventRequest> $eventRequests
     */
    public function addEventRequests(array $eventRequests, Event $event): void
    {
        foreach ($eventRequests as $eventRequest) {
            $eventRequest->setEvent($event);
            $event->addEventRequest($eventRequest);
        }
    }

    /**
     * @param array<int,EventRejection> $eventRejections
     */
    public function addEventRejections(array $eventRejections, Event $event): void
    {
        foreach ($eventRejections as $eventRejection) {
            $eventRejection->setEvent($event);
            $event->addEventRejection($eventRejection);
        }
    }
}

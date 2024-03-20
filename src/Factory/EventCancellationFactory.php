<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Event\Event;
use App\Entity\Event\EventCancellation;
use App\Entity\User\User;
use App\Enum\EventCancellationReasonEnum;

final class EventCancellationFactory
{
    public function create(
        null|Event $event = null,
        null|EventCancellationReasonEnum $reason = null,
        null|string $notice = null,
        null|User $owner = null,
    ): EventCancellation {
        $cancellation = new EventCancellation();
        $cancellation->setEvent($event);
        $cancellation->setReason($reason);
        $cancellation->setNotice($notice);
        $cancellation->setOwner($owner);
        return $cancellation;
    }
}

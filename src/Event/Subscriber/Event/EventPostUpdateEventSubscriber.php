<?php

declare(strict_types=1);

namespace App\Event\Subscriber\Event;

use App\Entity\Event\Event;
use App\Entity\Event\EventMoment;
use App\Enum\EventMomentTypeEnum;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Doctrine\ORM\Events;

#[AsEntityListener(event: Events::postUpdate, method: 'postUpdate', entity: Event::class)]
class EventPostUpdateEventSubscriber
{
    public function postUpdate(Event $event, PostUpdateEventArgs $args): void
    {
        $entityChangeSet = $args->getObjectManager()->getUnitOfWork()->getEntityChangeSet($event);

        foreach ($entityChangeSet as $property => $changes) {
            $changeLogTypeEnum = $this->resolveType($property);
            if ($changeLogTypeEnum instanceof EventMomentTypeEnum) {
                $changeLog = new EventMoment(
                    event: $event,
                    type: $changeLogTypeEnum,
                    oldValue: $changes[0],
                    newValue: $changes[1]
                );
                $args->getObjectManager()->persist($changeLog);
            }
        }
        $args->getObjectManager()->flush();
    }

    private function resolveType(string $property): null|EventMomentTypeEnum
    {
        foreach (EventMomentTypeEnum::cases() as $eventChangeEnum) {
            if (str_contains($eventChangeEnum->value, $property)) {
                return $eventChangeEnum;
            }
        }

        return null;
    }
}

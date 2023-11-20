<?php

declare(strict_types=1);

namespace App\Event\Subscriber;

use App\Entity\Contract\UpdatedAtInterface;
use Carbon\CarbonImmutable;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

#[AsDoctrineListener(event: Events::preUpdate, priority: 500, connection: 'default')]
class UpdatedAtSubscriber
{
    public function preUpdate(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if ($entity instanceof UpdatedAtInterface) {
            $entity->setUpdatedAt(new CarbonImmutable());
        }
    }
}

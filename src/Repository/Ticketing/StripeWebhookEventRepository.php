<?php

declare(strict_types=1);

namespace App\Repository\Ticketing;

use App\Entity\Ticketing\StripeWebhookEvent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<StripeWebhookEvent>
 */
class StripeWebhookEventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StripeWebhookEvent::class);
    }

    public function save(StripeWebhookEvent $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findByStripeEventId(string $stripeEventId): ?StripeWebhookEvent
    {
        return $this->findOneBy(['stripeEventId' => $stripeEventId]);
    }
}

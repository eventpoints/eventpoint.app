<?php

declare(strict_types=1);

namespace App\Repository\Ticketing;

use App\Entity\Ticketing\TicketMerchantProfile;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TicketMerchantProfile>
 */
class TicketMerchantProfileRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TicketMerchantProfile::class);
    }

    public function save(TicketMerchantProfile $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}

<?php

declare(strict_types=1);

namespace App\Repository\Event;

use App\Entity\Event\ExternalEvent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ExternalEvent>
 *
 * @method ExternalEvent|null find($id, $lockMode = null, $lockVersion = null)
 * @method ExternalEvent|null findOneBy(array $criteria, array $orderBy = null)
 * @method ExternalEvent[]    findAll()
 * @method ExternalEvent[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExternalEventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ExternalEvent::class);
    }

    public function save(ExternalEvent $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ExternalEvent $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}

<?php

declare(strict_types=1);

namespace App\Repository\Event;

use App\Entity\Event\EventCancellation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EventCancellation>
 *
 * @method EventCancellation|null find($id, $lockMode = null, $lockVersion = null)
 * @method EventCancellation|null findOneBy(array $criteria, array $orderBy = null)
 * @method EventCancellation[]    findAll()
 * @method EventCancellation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventCancellationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EventCancellation::class);
    }

    //    /**
    //     * @return EventCancellation[] Returns an array of EventCancellation objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('e.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?EventCancellation
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}

<?php

namespace App\Repository\Event;

use App\Entity\Event\EventTicketOption;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EventTicketOption>
 *
 * @method EventTicketOption|null find($id, $lockMode = null, $lockVersion = null)
 * @method EventTicketOption|null findOneBy(array $criteria, array $orderBy = null)
 * @method EventTicketOption[]    findAll()
 * @method EventTicketOption[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventTicketOptionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EventTicketOption::class);
    }

    //    /**
    //     * @return EventTicket[] Returns an array of EventTicket objects
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

    //    public function findOneBySomeField($value): ?EventTicket
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}

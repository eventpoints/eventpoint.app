<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Event\EventEmailInvitation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EventEmailInvitation>
 *
 * @method EventEmailInvitation|null find($id, $lockMode = null, $lockVersion = null)
 * @method EventEmailInvitation|null findOneBy(array $criteria, array $orderBy = null)
 * @method EventEmailInvitation[]    findAll()
 * @method EventEmailInvitation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventEmailInvitationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EventEmailInvitation::class);
    }

    //    /**
    //     * @return EventEmailInvitation[] Returns an array of EventEmailInvitation objects
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

    //    public function findOneBySomeField($value): ?EventEmailInvitation
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}

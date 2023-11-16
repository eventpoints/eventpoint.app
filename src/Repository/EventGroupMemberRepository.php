<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\EventGroup\EventGroupMember;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EventGroupMember>
 *
 * @method EventGroupMember|null find($id, $lockMode = null, $lockVersion = null)
 * @method EventGroupMember|null findOneBy(array $criteria, array $orderBy = null)
 * @method EventGroupMember[]    findAll()
 * @method EventGroupMember[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventGroupMemberRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EventGroupMember::class);
    }

    //    /**
    //     * @return EventGroupMember[] Returns an array of EventGroupMember objects
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

    //    public function findOneBySomeField($value): ?EventGroupMember
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}

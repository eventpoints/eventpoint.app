<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\EventGroup\EventGroupRole;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EventGroupRole>
 *
 * @method EventGroupRole|null find($id, $lockMode = null, $lockVersion = null)
 * @method EventGroupRole|null findOneBy(array $criteria, array $orderBy = null)
 * @method EventGroupRole[]    findAll()
 * @method EventGroupRole[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventGroupRoleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EventGroupRole::class);
    }

    //    /**
    //     * @return EventGroupRole[] Returns an array of EventGroupRole objects
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

    //    public function findOneBySomeField($value): ?EventGroupRole
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}

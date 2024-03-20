<?php

declare(strict_types=1);

namespace App\Repository\User;

use App\Entity\User\SocialAuth;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SocialAuth>
 *
 * @method SocialAuth|null find($id, $lockMode = null, $lockVersion = null)
 * @method SocialAuth|null findOneBy(array $criteria, array $orderBy = null)
 * @method SocialAuth[]    findAll()
 * @method SocialAuth[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SocialAuthRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SocialAuth::class);
    }

    //    /**
    //     * @return SocialAuth[] Returns an array of SocialAuth objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?SocialAuth
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}

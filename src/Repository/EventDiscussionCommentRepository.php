<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\EventDiscussionComment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EventDiscussionComment>
 *
 * @method EventDiscussionComment|null find($id, $lockMode = null, $lockVersion = null)
 * @method EventDiscussionComment|null findOneBy(array $criteria, array $orderBy = null)
 * @method EventDiscussionComment[]    findAll()
 * @method EventDiscussionComment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventDiscussionCommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EventDiscussionComment::class);
    }

    //    /**
    //     * @return EventDiscussionComment[] Returns an array of EventDiscussionComment objects
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

    //    public function findOneBySomeField($value): ?EventDiscussionComment
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}

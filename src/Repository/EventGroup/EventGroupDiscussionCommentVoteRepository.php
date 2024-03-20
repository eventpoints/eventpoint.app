<?php

declare(strict_types=1);

namespace App\Repository\EventGroup;

use App\Entity\EventGroup\EventGroupDiscussionCommentVote;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EventGroupDiscussionCommentVote>
 *
 * @method EventGroupDiscussionCommentVote|null find($id, $lockMode = null, $lockVersion = null)
 * @method EventGroupDiscussionCommentVote|null findOneBy(array $criteria, array $orderBy = null)
 * @method EventGroupDiscussionCommentVote[]    findAll()
 * @method EventGroupDiscussionCommentVote[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventGroupDiscussionCommentVoteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EventGroupDiscussionCommentVote::class);
    }

    public function save(EventGroupDiscussionCommentVote $entity, bool $flush = false): void
    {
        $this->getEntityManager()
            ->persist($entity);

        if ($flush) {
            $this->getEntityManager()
                ->flush();
        }
    }

    public function remove(EventGroupDiscussionCommentVote $entity, bool $flush = false): void
    {
        $this->getEntityManager()
            ->remove($entity);

        if ($flush) {
            $this->getEntityManager()
                ->flush();
        }
    }
}

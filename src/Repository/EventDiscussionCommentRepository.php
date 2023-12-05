<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\EventDiscussionComment;
use App\Entity\EventGroup\EventGroup;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Query;
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

    /**
     * @return array<int, EventDiscussionComment>|Query
     */
    public function findByGroup(EventGroup $eventGroup, bool $isQuery = false): array|Query
    {
        $qb = $this->createQueryBuilder('event_discussion_comment');
        $qb->leftJoin('event_discussion_comment.discussion', 'discussion');
        $qb->andWhere(
            $qb->expr()->eq('discussion.eventGroup', ':group')
        )->setParameter('group', $eventGroup->getId(), 'uuid');
        $qb->orderBy('event_discussion_comment.createdAt', Criteria::DESC);

        if ($isQuery) {
            return $qb->getQuery();
        }

        return $qb->getQuery()->getResult();
    }
}

<?php

declare(strict_types=1);

namespace App\Repository\EventGroup;

use App\Entity\EventGroup\EventGroup;
use App\Entity\EventGroup\EventGroupDiscussionComment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EventGroupDiscussionComment>
 *
 * @method EventGroupDiscussionComment|null find($id, $lockMode = null, $lockVersion = null)
 * @method EventGroupDiscussionComment|null findOneBy(array $criteria, array $orderBy = null)
 * @method EventGroupDiscussionComment[]    findAll()
 * @method EventGroupDiscussionComment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventGroupDiscussionCommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EventGroupDiscussionComment::class);
    }

    /**
     * @return array<int, EventGroupDiscussionComment>|Query
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

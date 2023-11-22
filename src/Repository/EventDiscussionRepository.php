<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\EventGroup\EventGroup;
use App\Entity\EventGroupDiscussion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EventGroupDiscussion>
 *
 * @method EventGroupDiscussion|null find($id, $lockMode = null, $lockVersion = null)
 * @method EventGroupDiscussion|null findOneBy(array $criteria, array $orderBy = null)
 * @method EventGroupDiscussion[]    findAll()
 * @method EventGroupDiscussion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventDiscussionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EventGroupDiscussion::class);
    }

    public function save(EventGroupDiscussion $entity, bool $flush = false): void
    {
        $this->getEntityManager()
            ->persist($entity);

        if ($flush) {
            $this->getEntityManager()
                ->flush();
        }
    }

    public function remove(EventGroupDiscussion $entity, bool $flush = false): void
    {
        $this->getEntityManager()
            ->remove($entity);

        if ($flush) {
            $this->getEntityManager()
                ->flush();
        }
    }

    /**
     * @return array<int, EventGroup>|Query
     */
    public function findByGroup(EventGroup $eventGroup, bool $isQuery = false): array|Query
    {
        $qb = $this->createQueryBuilder('event_discussion');
        $qb->andWhere(
            $qb->expr()->eq('event_discussion.eventGroup', ':group')
        )->setParameter('group', $eventGroup->getId(), 'uuid');
        $qb->orderBy('event_discussion.createdAt', Criteria::DESC);

        if ($isQuery) {
            return $qb->getQuery();
        }

        return $qb->getQuery()->getResult();
    }
}

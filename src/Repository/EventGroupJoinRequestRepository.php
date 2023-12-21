<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\EventGroup\EventGroup;
use App\Entity\EventGroup\EventGroupJoinRequest;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EventGroupJoinRequest>
 *
 * @method EventGroupJoinRequest|null find($id, $lockMode = null, $lockVersion = null)
 * @method EventGroupJoinRequest|null findOneBy(array $criteria, array $orderBy = null)
 * @method EventGroupJoinRequest[]    findAll()
 * @method EventGroupJoinRequest[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventGroupJoinRequestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EventGroupJoinRequest::class);
    }

    public function save(EventGroupJoinRequest $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(EventGroupJoinRequest $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return Query|array<int, EventGroupJoinRequest>
     */
    public function findByGroup(EventGroup $eventGroup, QueryBuilder $qb = null, bool $isQuery = false): Query|array
    {
        if (! $qb instanceof QueryBuilder) {
            $qb = $this->createQueryBuilder('event_group_join_request');
        }
        $result = $qb;

        $qb->andWhere(
            $qb->expr()->eq('event_group_join_request.eventGroup', ':eventGroup')
        )->setParameter('eventGroup', $eventGroup);

        $qb->orderBy('event_group_join_request.createdAt', Criteria::DESC);

        if ($isQuery) {
            return $result->getQuery();
        }

        return $result->getQuery()->getResult();
    }
}

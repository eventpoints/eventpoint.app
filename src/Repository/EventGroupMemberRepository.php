<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\EventGroup\EventGroup;
use App\Entity\EventGroup\EventGroupMember;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
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

    public function save(EventGroupMember $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(EventGroupMember $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return Query|array<int, EventGroupMember>
     */
    public function findByGroup(EventGroup $eventGroup, QueryBuilder $qb = null, bool $isQuery = false): Query|array
    {
        if (!$qb instanceof QueryBuilder) {
            $qb = $this->createQueryBuilder('event_group_member');
        }
        $result = $qb;

        $qb->andWhere(
            $qb->expr()->eq('event_group_member.eventGroup', ':eventGroup')
        )->setParameter('eventGroup', $eventGroup);

        $qb->orderBy('event_group_member.createdAt', Criteria::DESC);

        if ($isQuery) {
            return $result->getQuery();
        }

        return $result->getQuery()->getResult();
    }

    public function findByOwner(User $user, EventGroup $group): null|EventGroupMember
    {
        $qb = $this->createQueryBuilder('event_group_member');

        $qb->andWhere(
            $qb->expr()->eq('event_group_member.eventGroup', ':eventGroup')
        )->setParameter('eventGroup', $group->getId(), 'uuid');

        $qb->andWhere(
            $qb->expr()->eq('event_group_member.owner', ':owner')
        )->setParameter('owner', $user->getId(), 'uuid');

        return $qb->getQuery()->getOneOrNullResult();
    }
}

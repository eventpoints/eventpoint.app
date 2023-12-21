<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\EventGroup\EventGroup;
use App\Entity\EventGroup\EventGroupInvitation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EventGroupInvitation>
 *
 * @method EventGroupInvitation|null find($id, $lockMode = null, $lockVersion = null)
 * @method EventGroupInvitation|null findOneBy(array $criteria, array $orderBy = null)
 * @method EventGroupInvitation[]    findAll()
 * @method EventGroupInvitation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventGroupInvitationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EventGroupInvitation::class);
    }

    /**
     * @return Query|array<int, EventGroupInvitation>
     */
    public function findByGroup(EventGroup $eventGroup, QueryBuilder $qb = null, bool $isQuery = false): Query|array
    {
        if (! $qb instanceof QueryBuilder) {
            $qb = $this->createQueryBuilder('event_group_invitation');
        }

        $result = $qb;

        $qb->andWhere(
            $qb->expr()->eq('event_group_invitation.eventGroup', ':eventGroup')
        )->setParameter('eventGroup', $eventGroup);

        $qb->orderBy('event_group_invitation.createdAt', Criteria::DESC);

        if ($isQuery) {
            return $result->getQuery();
        }

        return $result->getQuery()->getResult();
    }
}

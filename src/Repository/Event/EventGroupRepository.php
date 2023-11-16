<?php

declare(strict_types=1);

namespace App\Repository\Event;

use App\Entity\EventGroup\EventGroup;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EventGroup>
 *
 * @method EventGroup|null find($id, $lockMode = null, $lockVersion = null)
 * @method EventGroup|null findOneBy(array $criteria, array $orderBy = null)
 * @method EventGroup[]    findAll()
 * @method EventGroup[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventGroupRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EventGroup::class);
    }

    public function save(EventGroup $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(EventGroup $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return array<int, EventGroup>
     */
    public function findByUser(User $user): array
    {
        $qb = $this->createQueryBuilder('event_group');

        $qb->leftJoin('event_group.eventGroupMembers', 'eventGroupMember');
        $qb->andWhere(
            $qb->expr()->eq('eventGroupMember.owner', ':user')
        )->setParameter('user', $user);

        return $qb->getQuery()->getResult();
    }
}

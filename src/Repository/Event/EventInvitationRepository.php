<?php

declare(strict_types=1);

namespace App\Repository\Event;

use App\Entity\Event\Event;
use App\Entity\Event\EventInvitation;
use App\Entity\User;
use Carbon\CarbonImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EventInvitation>
 *
 * @method EventInvitation|null find($id, $lockMode = null, $lockVersion = null)
 * @method EventInvitation|null findOneBy(array $criteria, array $orderBy = null)
 * @method EventInvitation[]    findAll()
 * @method EventInvitation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventInvitationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EventInvitation::class);
    }

    public function save(EventInvitation $entity, bool $flush = false): void
    {
        $this->getEntityManager()
            ->persist($entity);

        if ($flush) {
            $this->getEntityManager()
                ->flush();
        }
    }

    public function remove(EventInvitation $entity, bool $flush = false): void
    {
        $this->getEntityManager()
            ->remove($entity);

        if ($flush) {
            $this->getEntityManager()
                ->flush();
        }
    }

    /**
     * @return array<int, EventInvitation>|Query
     */
    public function findByEvent(Event $event, bool $isQuery = false): array|Query
    {
        $qb = $this->createQueryBuilder('event_invitation');
        $qb->andWhere($qb->expr() ->eq('event_invitation.event', ':event'))
            ->setParameter('event', $event->getId(), 'uuid');

        $qb->orderBy('event_invitation.createdAt', Criteria::DESC);

        if ($isQuery) {
            return $qb->getQuery();
        }

        return $qb->getQuery()
            ->getResult();
    }

    /**
     * @return array<int, EventInvitation>|Query
     */
    public function findByTarget(User $user, bool $isQuery = false): array|Query
    {
        $qb = $this->createQueryBuilder('event_invitation');

        $qb->andWhere($qb->expr() ->eq('event_invitation.target', ':target_user'))
            ->setParameter('target_user', $user->getId(), 'uuid');

        $now = CarbonImmutable::now();
        $qb->leftJoin('event_invitation.event', 'event');

        $qb->andWhere(
            $qb->expr()->lte(':now', 'event.startAt')
        )->setParameter('now', $now, Types::DATETIME_IMMUTABLE);

        if ($isQuery) {
            return $qb->getQuery();
        }

        return $qb->getQuery()
            ->getResult();
    }
}

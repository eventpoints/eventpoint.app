<?php

declare(strict_types = 1);

namespace App\Repository\Event;

use App\Entity\Event\Event;
use App\Entity\Event\EventInvitation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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

    public function findByEvent(Event $event, bool $isQuery = false): mixed
    {
        $qb = $this->createQueryBuilder('ei');
        $qb->andWhere($qb->expr() ->eq('ei.event', ':event'))
            ->setParameter('event', $event->getId(), 'uuid');

        $qb->orderBy('ei.createdAt', 'ASC');

        if ($isQuery) {
            return $qb->getQuery();
        }

        return $qb->getQuery()
            ->getResult();
    }
}

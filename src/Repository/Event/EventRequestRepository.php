<?php

declare(strict_types = 1);

namespace App\Repository\Event;

use App\Entity\Event\Event;
use App\Entity\Event\EventRequest;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EventRequest>
 *
 * @method EventRequest|null find($id, $lockMode = null, $lockVersion = null)
 * @method EventRequest|null findOneBy(array $criteria, array $orderBy = null)
 * @method EventRequest[]    findAll()
 * @method EventRequest[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventRequestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EventRequest::class);
    }

    public function save(EventRequest $entity, bool $flush = false): void
    {
        $this->getEntityManager()
            ->persist($entity);

        if ($flush) {
            $this->getEntityManager()
                ->flush();
        }
    }

    public function remove(EventRequest $entity, bool $flush = false): void
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
        $qb = $this->createQueryBuilder('er');
        $qb->andWhere($qb->expr() ->eq('er.event', ':event'))
            ->setParameter('event', $event->getId(), 'uuid');

        $qb->orderBy('er.createdAt', 'ASC');

        if ($isQuery) {
            return $qb->getQuery();
        }

        return $qb->getQuery()
            ->getResult();
    }
}

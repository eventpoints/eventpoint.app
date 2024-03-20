<?php

declare(strict_types=1);

namespace App\Repository\Poll;

use App\Entity\EventGroup\EventGroup;
use App\Entity\Poll\Poll;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Poll>
 *
 * @method Poll|null find($id, $lockMode = null, $lockVersion = null)
 * @method Poll|null findOneBy(array $criteria, array $orderBy = null)
 * @method Poll[]    findAll()
 * @method Poll[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PollRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Poll::class);
    }

    public function save(Poll $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Poll $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return array<int, Poll>|Query
     */
    public function findByGroup(EventGroup $eventGroup, bool $isQuery = false): array|Query
    {
        $qb = $this->createQueryBuilder('poll');
        $qb->andWhere(
            $qb->expr()->eq('poll.eventGroup', ':group')
        )->setParameter('group', $eventGroup->getId(), 'uuid');
        $qb->orderBy('poll.createdAt', Criteria::DESC);

        if ($isQuery) {
            return $qb->getQuery();
        }

        return $qb->getQuery()->getResult();
    }
}

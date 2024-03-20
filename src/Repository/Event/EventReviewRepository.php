<?php

declare(strict_types=1);

namespace App\Repository\Event;

use App\Entity\Event\EventReview;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EventReview>
 *
 * @method EventReview|null find($id, $lockMode = null, $lockVersion = null)
 * @method EventReview|null findOneBy(array $criteria, array $orderBy = null)
 * @method EventReview[]    findAll()
 * @method EventReview[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventReviewRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EventReview::class);
    }

    public function save(EventReview $entity, bool $flush = false): void
    {
        $this->getEntityManager()
            ->persist($entity);

        if ($flush) {
            $this->getEntityManager()
                ->flush();
        }
    }

    public function remove(EventReview $entity, bool $flush = false): void
    {
        $this->getEntityManager()
            ->remove($entity);

        if ($flush) {
            $this->getEntityManager()
                ->flush();
        }
    }
}

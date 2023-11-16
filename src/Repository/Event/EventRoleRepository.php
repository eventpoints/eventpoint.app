<?php

declare(strict_types=1);

namespace App\Repository\Event;

use App\Entity\Event\EventRole;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EventRole>
 *
 * @method EventRole|null find($id, $lockMode = null, $lockVersion = null)
 * @method EventRole|null findOneBy(array $criteria, array $orderBy = null)
 * @method EventRole[]    findAll()
 * @method EventRole[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventRoleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EventRole::class);
    }

    public function save(EventRole $entity, bool $flush = false): void
    {
        $this->getEntityManager()
            ->persist($entity);

        if ($flush) {
            $this->getEntityManager()
                ->flush();
        }
    }

    public function remove(EventRole $entity, bool $flush = false): void
    {
        $this->getEntityManager()
            ->remove($entity);

        if ($flush) {
            $this->getEntityManager()
                ->flush();
        }
    }

    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }
}

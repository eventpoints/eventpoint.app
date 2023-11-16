<?php

namespace App\Repository\Event;

use App\Entity\Event\EventOrganiser;
use App\Entity\Event\EventInvitation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EventOrganiser>
 *
 * @method EventOrganiser|null find($id, $lockMode = null, $lockVersion = null)
 * @method EventOrganiser|null findOneBy(array $criteria, array $orderBy = null)
 * @method EventOrganiser[]    findAll()
 * @method EventOrganiser[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventOrganiserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EventOrganiser::class);
    }

    public function save(EventOrganiser $entity, bool $flush = false): void
    {
        $this->getEntityManager()
            ->persist($entity);

        if ($flush) {
            $this->getEntityManager()
                ->flush();
        }
    }

    public function remove(EventOrganiser $entity, bool $flush = false): void
    {
        $this->getEntityManager()
            ->remove($entity);

        if ($flush) {
            $this->getEntityManager()
                ->flush();
        }
    }

}

<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Event\EventEmailInvitation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EventEmailInvitation>
 *
 * @method EventEmailInvitation|null find($id, $lockMode = null, $lockVersion = null)
 * @method EventEmailInvitation|null findOneBy(array $criteria, array $orderBy = null)
 * @method EventEmailInvitation[]    findAll()
 * @method EventEmailInvitation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventEmailInvitationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EventEmailInvitation::class);
    }

    public function save(EventEmailInvitation $entity, bool $flush = false): void
    {
        $this->getEntityManager()
            ->persist($entity);

        if ($flush) {
            $this->getEntityManager()
                ->flush();
        }
    }

    public function remove(EventEmailInvitation $entity, bool $flush = false): void
    {
        $this->getEntityManager()
            ->remove($entity);

        if ($flush) {
            $this->getEntityManager()
                ->flush();
        }
    }
}

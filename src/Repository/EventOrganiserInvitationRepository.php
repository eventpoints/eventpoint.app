<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\EventOrganiserInvitation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EventOrganiserInvitation>
 *
 * @method EventOrganiserInvitation|null find($id, $lockMode = null, $lockVersion = null)
 * @method EventOrganiserInvitation|null findOneBy(array $criteria, array $orderBy = null)
 * @method EventOrganiserInvitation[]    findAll()
 * @method EventOrganiserInvitation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventOrganiserInvitationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EventOrganiserInvitation::class);
    }

    public function save(EventOrganiserInvitation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(EventOrganiserInvitation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}

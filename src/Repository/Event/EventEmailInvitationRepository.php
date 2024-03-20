<?php

declare(strict_types=1);

namespace App\Repository\Event;

use App\Entity\Event\Event;
use App\Entity\Event\EventEmailInvitation;
use App\Entity\User\Email;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Query;
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

    /**
     * @return array<int, EventEmailInvitation>|Query
     */
    public function findByEmail(Email $email, bool $isQuery = false): array|Query
    {
        $qb = $this->createQueryBuilder('event_email_invitation');

        $qb->leftJoin('event_email_invitation.email', 'email');
        $qb->andWhere(
            $qb->expr()->eq('email.address', ':emailAddress')
        )->setParameter('emailAddress', $email->getAddress());

        if ($isQuery) {
            return $qb->getQuery();
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @return array<int, EventEmailInvitation>|Query
     */
    public function findByEvent(Event $event, bool $isQuery = false): array|Query
    {
        $qb = $this->createQueryBuilder('event_email_invitation');

        $qb->andWhere(
            $qb->expr()->eq('event_email_invitation.event', ':event')
        )->setParameter('event', $event);

        $qb->orderBy('event_email_invitation.createdAt', Criteria::DESC);

        if ($isQuery) {
            return $qb->getQuery();
        }

        return $qb->getQuery()->getResult();
    }
}

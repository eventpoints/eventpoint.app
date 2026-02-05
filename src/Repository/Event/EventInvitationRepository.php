<?php

declare(strict_types=1);

namespace App\Repository\Event;

use App\Entity\Event\Event;
use App\Entity\Event\EventInvitation;
use App\Entity\User\Email;
use App\Entity\User\User;
use App\Enum\EventInvitationStatusEnum;
use App\Enum\EventInvitationTypeEnum;
use Carbon\CarbonImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

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

        $qb->andWhere($qb->expr() ->eq('event_invitation.targetUser', ':target_user'))
            ->setParameter('target_user', $user->getId(), 'uuid');

        // Only get pending invitations
        $qb->andWhere($qb->expr()->eq('event_invitation.status', ':status'))
            ->setParameter('status', EventInvitationStatusEnum::PENDING->value);

        // Only get invitations (not requests)
        $qb->andWhere($qb->expr()->eq('event_invitation.type', ':type'))
            ->setParameter('type', EventInvitationTypeEnum::INVITATION->value);

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

    /**
     * Find pending invitations or requests by event and type.
     *
     * @return array<int, EventInvitation>|Query
     */
    public function findPendingByEvent(Event $event, EventInvitationTypeEnum $type, bool $isQuery = false): array|Query
    {
        $qb = $this->createQueryBuilder('event_invitation');

        $qb->andWhere($qb->expr()->eq('event_invitation.event', ':event'))
            ->setParameter('event', $event->getId(), 'uuid');

        $qb->andWhere($qb->expr()->eq('event_invitation.type', ':type'))
            ->setParameter('type', $type->value);

        $qb->andWhere($qb->expr()->eq('event_invitation.status', ':status'))
            ->setParameter('status', EventInvitationStatusEnum::PENDING->value);

        $qb->orderBy('event_invitation.createdAt', Criteria::DESC);

        if ($isQuery) {
            return $qb->getQuery();
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Find pending invitations by target email address.
     *
     * @return array<int, EventInvitation>|Query
     */
    public function findByTargetEmail(Email $email, bool $isQuery = false): array|Query
    {
        $qb = $this->createQueryBuilder('event_invitation');

        $qb->leftJoin('event_invitation.targetEmail', 'target_email');
        $qb->andWhere(
            $qb->expr()->eq('target_email.address', ':emailAddress')
        )->setParameter('emailAddress', $email->getAddress());

        $qb->andWhere($qb->expr()->eq('event_invitation.status', ':status'))
            ->setParameter('status', EventInvitationStatusEnum::PENDING->value);

        $qb->andWhere($qb->expr()->eq('event_invitation.type', ':type'))
            ->setParameter('type', EventInvitationTypeEnum::INVITATION->value);

        if ($isQuery) {
            return $qb->getQuery();
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Find invitation by token.
     */
    public function findByToken(Uuid $token): ?EventInvitation
    {
        return $this->findOneBy(['token' => $token]);
    }

    /**
     * Find pending requests by user (owner).
     *
     * @return array<int, EventInvitation>|Query
     */
    public function findPendingRequestsByOwner(User $user, bool $isQuery = false): array|Query
    {
        $qb = $this->createQueryBuilder('event_invitation');

        $qb->andWhere($qb->expr()->eq('event_invitation.owner', ':owner'))
            ->setParameter('owner', $user->getId(), 'uuid');

        $qb->andWhere($qb->expr()->eq('event_invitation.type', ':type'))
            ->setParameter('type', EventInvitationTypeEnum::REQUEST->value);

        $qb->andWhere($qb->expr()->eq('event_invitation.status', ':status'))
            ->setParameter('status', EventInvitationStatusEnum::PENDING->value);

        $qb->orderBy('event_invitation.createdAt', Criteria::DESC);

        if ($isQuery) {
            return $qb->getQuery();
        }

        return $qb->getQuery()->getResult();
    }
}

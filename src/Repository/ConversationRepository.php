<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Conversation;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Conversation>
 *
 * @method Conversation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Conversation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Conversation[]    findAll()
 * @method Conversation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConversationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Conversation::class);
    }

    public function save(Conversation $entity, bool $flush = false): void
    {
        $this->getEntityManager()
            ->persist($entity);

        if ($flush) {
            $this->getEntityManager()
                ->flush();
        }
    }

    public function remove(Conversation $entity, bool $flush = false): void
    {
        $this->getEntityManager()
            ->remove($entity);

        if ($flush) {
            $this->getEntityManager()
                ->flush();
        }
    }

    public function findByCurrentUserOrTarget(User $user): null|Conversation
    {
        $qb = $this->createQueryBuilder('conversation');
        $qb->leftJoin('conversation.conversationParticipants', 'conversation_participant');
        $qb->andWhere(
            $qb->expr()->eq('conversation_participant.owner', ':user')
        )->setParameter('user', $user->getId(), 'uuid');

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * @return array<int, Conversation>|Query
     */
    public function findByUser(User $user, QueryBuilder $qb = null, bool $isQuery = false): Query|array
    {
        if (! $qb instanceof QueryBuilder) {
            $qb = $this->createQueryBuilder('conversation');
        }
        $result = $qb;

        $qb->leftJoin('conversation.conversationParticipants', 'conversationParticipant');
        $qb->andWhere(
            $qb->expr()->eq('conversationParticipant.owner', ':owner')
        )->setParameter('owner', $user);

        if ($isQuery) {
            return $result->getQuery();
        }

        return $result->getQuery()->getResult();
    }
}

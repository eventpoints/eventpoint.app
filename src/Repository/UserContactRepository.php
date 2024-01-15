<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\User;
use App\Entity\UserContact;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserContact>
 *
 * @method UserContact|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserContact|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserContact[]    findAll()
 * @method UserContact[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserContactRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserContact::class);
    }

    /**
     * @return Query|array<int, UserContact>
     */
    public function findByOwner(User $user, null|int $limit = 10, bool $isQuery = false): Query|array
    {
        $qb = $this->createQueryBuilder('user_contact');
        $qb->andWhere(
            $qb->expr()->eq('user_contact.owner', ':user')
        )->setParameter('user', $user->getId(), 'uuid');

        $qb->orderBy('user_contact.createdAt', Criteria::DESC);

        if ($isQuery) {
            return $qb->getQuery();
        }

        $qb->setMaxResults($limit);

        return $qb->getQuery()->getResult();
    }

    public function save(UserContact $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(UserContact $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return Query|array<int, UserContact>
     */
    public function findByOwnerAndQuery(User $user, string $emailAddress, int|null $limit = 10, bool $isQuery = false): Query|array
    {
        $qb = $this->createQueryBuilder('user_contact');
        $qb->leftJoin('user_contact.owner', 'owner');
        $qb->andWhere(
            $qb->expr()->eq('user_contact.owner', ':user')
        )->setParameter('user', $user->getId(), 'uuid');

        $qb->leftJoin('user_contact.email', 'email');
        $qb->andWhere(
            $qb->expr()->like('email.content', ':email')
        )->setParameter('email', '%' . $emailAddress . '%');

        $qb->orderBy('user_contact.createdAt', Criteria::DESC);
        $qb->setMaxResults($limit);

        if ($isQuery) {
            return $qb->getQuery();
        }

        return $qb->getQuery()->getResult();
    }
}

<?php

declare(strict_types=1);

namespace App\Repository\Poll;

use App\Entity\Poll\PollAnswer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PollAnswer>
 *
 * @method PollAnswer|null find($id, $lockMode = null, $lockVersion = null)
 * @method PollAnswer|null findOneBy(array $criteria, array $orderBy = null)
 * @method PollAnswer[]    findAll()
 * @method PollAnswer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PollAnswerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PollAnswer::class);
    }

    public function save(PollAnswer $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(PollAnswer $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}

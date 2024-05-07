<?php

declare(strict_types=1);

namespace App\Repository\Event;

use App\Entity\Event\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

/**
 * @extends ServiceEntityRepository<Category>
 *
 * @method Category|null find($id, $lockMode = null, $lockVersion = null)
 * @method Category|null findOneBy(array $criteria, array $orderBy = null)
 * @method Category[]    findAll()
 * @method Category[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findByUuid(Uuid $id): null|Category
    {
        $qb = $this->createQueryBuilder('category');

        $qb->andWhere(
            $qb->expr()->eq('category.id', ':id')
        )->setParameter('id', $id, 'uuid');

        return $qb->getQuery()->getOneOrNullResult();
    }
}

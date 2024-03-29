<?php

declare(strict_types=1);

namespace App\Repository\Image;

use App\Entity\Image\ImageCollection;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ImageCollection>
 *
 * @method ImageCollection|null find($id, $lockMode = null, $lockVersion = null)
 * @method ImageCollection|null findOneBy(array $criteria, array $orderBy = null)
 * @method ImageCollection[]    findAll()
 * @method ImageCollection[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ImageCollectionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ImageCollection::class);
    }

    public function save(ImageCollection $entity, bool $flush = false): void
    {
        $this->getEntityManager()
            ->persist($entity);

        if ($flush) {
            $this->getEntityManager()
                ->flush();
        }
    }

    public function remove(ImageCollection $entity, bool $flush = false): void
    {
        $this->getEntityManager()
            ->remove($entity);

        if ($flush) {
            $this->getEntityManager()
                ->flush();
        }
    }
}

<?php

declare(strict_types=1);

namespace App\Repository\EventGroup;

use App\Entity\EventGroup\EventGroupRole;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EventGroupRole>
 *
 * @method EventGroupRole|null find($id, $lockMode = null, $lockVersion = null)
 * @method EventGroupRole|null findOneBy(array $criteria, array $orderBy = null)
 * @method EventGroupRole[]    findAll()
 * @method EventGroupRole[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventGroupRoleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EventGroupRole::class);
    }
}

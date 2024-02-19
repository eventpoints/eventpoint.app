<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\PhoneNumber;
use App\Service\PhoneNumberService\PhoneNumberHelperService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PhoneNumber>
 *
 * @method PhoneNumber|null find($id, $lockMode = null, $lockVersion = null)
 * @method PhoneNumber|null findOneBy(array $criteria, array $orderBy = null)
 * @method PhoneNumber[]    findAll()
 * @method PhoneNumber[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PhoneNumberRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
        private readonly PhoneNumberHelperService $phoneNumberHelperService
    ) {
        parent::__construct($registry, PhoneNumber::class);
    }

    public function save(PhoneNumber $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(PhoneNumber $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findByFullNumber(string $phoneNumber): null|PhoneNumber
    {
        $code = $this->phoneNumberHelperService->getDialCode(phoneNumber: $phoneNumber);
        $number = $this->phoneNumberHelperService->getNumber(phoneNumber: $phoneNumber);

        $qb = $this->createQueryBuilder('phone_number');

        if ($code !== null) {
            $qb->andWhere(
                $qb->expr()->andX(
                    $qb->expr()->eq('phone_number.code', ':code'),
                    $qb->expr()->eq('phone_number.number', ':number')
                )
            )
                ->setParameter('code', $code)
                ->setParameter('number', $number);
        } else {
            $qb->andWhere(
                $qb->expr()->like('phone_number.number', ':number')
            )->setParameter('number', $phoneNumber);
        }

        return $qb->getQuery()->getOneOrNullResult();
    }
}

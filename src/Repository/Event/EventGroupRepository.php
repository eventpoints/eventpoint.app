<?php

declare(strict_types=1);

namespace App\Repository\Event;

use App\DataTransferObject\EventFilterDto;
use App\DataTransferObject\EventGroupFilterDto;
use App\Entity\Category;
use App\Entity\EventGroup\EventGroup;
use App\Entity\User;
use App\Enum\EventFilterDateRangeEnum;
use App\Enum\EventGroupRoleEnum;
use App\Service\ApplicationTimeService\ApplicationTimeService;
use Carbon\Carbon;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EventGroup>
 *
 * @method EventGroup|null find($id, $lockMode = null, $lockVersion = null)
 * @method EventGroup|null findOneBy(array $criteria, array $orderBy = null)
 * @method EventGroup[]    findAll()
 * @method EventGroup[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventGroupRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry                         $registry,
        private readonly ApplicationTimeService $applicationTimeService,
    ) {
        parent::__construct($registry, EventGroup::class);
    }

    public function save(EventGroup $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(EventGroup $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return array<int, EventGroup>
     */
    public function findAssociatedByUser(User $user): array
    {
        $qb = $this->createQueryBuilder('event_group');

        $qb->leftJoin('event_group.eventGroupMembers', 'eventGroupMember');
        $qb->andWhere(
            $qb->expr()->eq('eventGroupMember.owner', ':user')
        )->setParameter('user', $user);

        return $qb->getQuery()->getResult();
    }

    /**
     * @return Query|array<int, EventGroup>
     */
    public function findByEventFilter(EventFilterDto $eventFilterDto, bool $isQuery = false): Query|array
    {
        $qb = $this->createQueryBuilder('event_group');
        $qb->leftJoin('event_group.events', 'event');

        if ($eventFilterDto->getPeriod() instanceof EventFilterDateRangeEnum) {
            $this->findByPeriod(period: $eventFilterDto->getPeriod(), qb: $qb);
        }

        if (! empty($eventFilterDto->getKeyword())) {
            $this->findByName(keyword: $eventFilterDto->getKeyword(), qb: $qb);
        }

        $qb->andWhere(
            $qb->expr()->eq('event.isPublished', ':true')
        )->setParameter('true', true);

        $qb->andWhere(
            $qb->expr()->eq('event_group.isPrivate', ':false')
        )->setParameter('false', false);

        if ($isQuery) {
            return $qb->getQuery();
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @return Query|array<int, EventGroup>
     */
    public function findByPeriod(EventFilterDateRangeEnum $period, null|QueryBuilder $qb = null, bool $isQuery = false): Query|array
    {
        if (! $qb instanceof QueryBuilder) {
            $qb = $this->createQueryBuilder('event_group');
        }
        $result = $qb;

        $start = match ($period) {
            EventFilterDateRangeEnum::RECENTLY => $this->applicationTimeService->getNow()->subDays(7)->startOfDay()->toImmutable(),
            EventFilterDateRangeEnum::TOMORROW => $this->applicationTimeService->getNow()->addDay()->startOfDay()->toImmutable(),
            EventFilterDateRangeEnum::THIS_WEEK => $this->applicationTimeService->getNow()->previous(Carbon::MONDAY)->startOfDay()->toImmutable(),
            EventFilterDateRangeEnum::THIS_WEEKEND => $this->applicationTimeService->getNow()->next(Carbon::SATURDAY)->startOfDay()->toImmutable(),
            EventFilterDateRangeEnum::NEXT_WEEK => $this->applicationTimeService->getNow()->addWeek()->startOfDay()->toImmutable(),
            EventFilterDateRangeEnum::NEXT_MONTH => $this->applicationTimeService->getNow()->addMonth()->startOfDay()->toImmutable(),
            default => $this->applicationTimeService->getNow()->startOfDay()->toImmutable(),
        };

        $end = match ($period) {
            EventFilterDateRangeEnum::RECENTLY => $this->applicationTimeService->getNow()->endOfDay()->toImmutable(),
            EventFilterDateRangeEnum::TOMORROW => $this->applicationTimeService->getNow()->addDay()->endOfDay()->toImmutable(),
            EventFilterDateRangeEnum::THIS_WEEK => $this->applicationTimeService->getNow()->next(Carbon::FRIDAY)->endOfDay()->toImmutable(),
            EventFilterDateRangeEnum::THIS_WEEKEND => $this->applicationTimeService->getNow()->next(Carbon::SUNDAY)->endOfDay()->toImmutable(),
            EventFilterDateRangeEnum::NEXT_WEEK => $this->applicationTimeService->getNow()->addWeek()->endOfWeek()->endOfDay()->toImmutable(),
            EventFilterDateRangeEnum::NEXT_MONTH => $this->applicationTimeService->getNow()->addMonth()->endOfMonth()->endOfDay()->toImmutable(),
            default => $this->applicationTimeService->getNow()->endOfDay()->toImmutable(),
        };

        $qb->andWhere(
            $qb->expr()->gte('event.startAt', ':start')
        )->setParameter('start', $start, Types::DATETIME_IMMUTABLE);

        $qb->andWhere(
            $qb->expr()->lte('event.startAt', ':end')
        )->setParameter('end', $end, Types::DATETIME_IMMUTABLE);

        if ($isQuery) {
            return $result->getQuery();
        }

        return $result->getQuery()->getResult();
    }

    /**
     * @return Query|array<int, EventGroup>
     */
    public function findByName(string $keyword, QueryBuilder $qb = null, bool $isQuery = false): Query|array
    {
        if (! $qb instanceof QueryBuilder) {
            $qb = $this->createQueryBuilder('event_group');
        }
        $result = $qb;

        $qb->andWhere(
            $qb->expr()->like($qb->expr()->lower('event_group.name'), ':name')
        )->setParameter('name', '%' . strtolower($keyword) . '%');

        if ($isQuery) {
            return $result->getQuery();
        }

        return $result->getQuery()->getResult();
    }

    /**
     * @return Query|array<int, EventGroup>
     */
    public function findByGroupFilter(EventGroupFilterDto $eventFilterDto, bool $isQuery = false): Query|array
    {
        $qb = $this->createQueryBuilder('event_group');

        $qb->andWhere(
            $qb->expr()->eq('event_group.isPrivate', ':false')
        )->setParameter('false', false);

        if (! empty($eventFilterDto->getKeyword())) {
            $this->findByName(keyword: $eventFilterDto->getKeyword(), qb: $qb, isQuery: true);

            $qb->orWhere(
                $qb->expr()->like('event_group.purpose', ':keyword')
            )->setParameter('keyword', '%' . $eventFilterDto->getKeyword() . '%');
        }

        if ($eventFilterDto->getCategory() instanceof Category) {
            $this->findByCategory(category: $eventFilterDto->getCategory(), qb: $qb, isQuery: true);
        }

        if ($isQuery) {
            return $qb->getQuery();
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @return Query|array<int, EventGroup>
     */
    public function findByCategory(Category $category, QueryBuilder $qb = null, bool $isQuery = false): Query|array
    {
        if (! $qb instanceof QueryBuilder) {
            $qb = $this->createQueryBuilder('event_group');
        }
        $result = $qb;

        $qb->leftJoin('event_group.categories', 'category');
        $qb->andWhere(
            $qb->expr()->eq('category.id', ':category')
        )->setParameter('category', $category->getId(), 'uuid');

        if ($isQuery) {
            return $result->getQuery();
        }

        return $result->getQuery()->getResult();
    }

    /**
     * @return Query|array<int, EventGroup>
     */
    public function findByMembership(User $user, QueryBuilder $qb = null, bool $isQuery = false): Query|array
    {
        if (! $qb instanceof QueryBuilder) {
            $qb = $this->createQueryBuilder('event_group');
        }
        $result = $qb;

        $qb->leftJoin('event_group.eventGroupMembers', 'event_group_member');
        $qb->leftJoin('event_group_member.roles', 'role');

        $qb->andWhere(
            $qb->expr()->eq('role.title', ':role_member')
        )->setParameter('role_member', EventGroupRoleEnum::ROLE_GROUP_MEMBER);

        if ($isQuery) {
            return $qb->getQuery();
        }

        return $result->getQuery()->getResult();
    }

    /**
     * @return Query|array<int, EventGroup>
     */
    public function findByGroupsManaged(User $user, QueryBuilder $qb = null, bool $isQuery = false): Query|array
    {
        if (! $qb instanceof QueryBuilder) {
            $qb = $this->createQueryBuilder('event_group');
        }
        $result = $qb;

        $qb->leftJoin('event_group.eventGroupMembers', 'event_group_member');
        $qb->leftJoin('event_group_member.roles', 'role');

        $qb->andWhere(
            $qb->expr()->eq('event_group_member.owner', ':user')
        )->setParameter('user', $user);

        $qb->andWhere(
            $qb->expr()->eq('role.title', ':role_manager')
        )->setParameter('role_manager', EventGroupRoleEnum::ROLE_GROUP_MANAGER);

        $qb->andWhere(
            $qb->expr()->eq('role.title', ':role_creator')
        )->setParameter('role_creator', EventGroupRoleEnum::ROLE_GROUP_CREATOR);

        $qb->andWhere(
            $qb->expr()->eq('role.title', ':role_maintainer')
        )->setParameter('role_maintainer', EventGroupRoleEnum::ROLE_GROUP_MAINTAINER);

        if ($isQuery) {
            return $result->getQuery();
        }

        return $result->getQuery()->getResult();
    }
}

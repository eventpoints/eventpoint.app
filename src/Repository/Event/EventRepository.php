<?php

declare(strict_types=1);

namespace App\Repository\Event;

use App\DataTransferObject\EventFilterDto;
use App\Entity\Category;
use App\Entity\Event\Event;
use App\Entity\EventGroup\EventGroup;
use App\Entity\User;
use App\Enum\EventFilterDateRangeEnum;
use App\Service\ApplicationTimeService\ApplicationTimeService;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Event>
 *
 * @method Event|null find($id, $lockMode = null, $lockVersion = null)
 * @method Event|null findOneBy(array $criteria, array $orderBy = null)
 * @method Event[]    findAll()
 * @method Event[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventRepository extends ServiceEntityRepository
{
    public function __construct(
        private readonly ManagerRegistry        $registry,
        private readonly ApplicationTimeService $applicationTimeService,
    ) {
        parent::__construct($this->registry, Event::class);
    }

    public function save(Event $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Event $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return array<int, Event>
     */
    public function findByToday(): array
    {
        $qb = $this->createQueryBuilder('event');
        $qb->andWhere(
            $qb->expr()->gte('event.startAt', ':dateStart')
        )->setParameter('dateStart', $this->applicationTimeService->getNow()->setTime(0, 0, 0, 0)->toImmutable(), Types::DATETIME_IMMUTABLE);

        $qb->andWhere(
            $qb->expr()->lte('event.startAt', ':dateEnd')
        )->setParameter('dateEnd', $this->applicationTimeService->getNow()->setTime(24, 60, 60, 60)->toImmutable(), Types::DATETIME_IMMUTABLE);

        return $qb->getQuery()->getResult();
    }

    /**
     * @return array<int, Event>
     */
    public function findByPeriod(EventFilterDateRangeEnum $period, null|QueryBuilder $qb = null, bool $isQuery = false): Query|array
    {
        if (! $qb instanceof QueryBuilder) {
            $qb = $this->createQueryBuilder('asset');
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
     * @return Query|array<int, Event>
     */
    public function findByFilter(EventFilterDto $eventFilterDto, bool $isQuery = false): Query|array
    {
        $qb = $this->createQueryBuilder('event');
        if ($eventFilterDto->getPeriod() instanceof EventFilterDateRangeEnum) {
            $this->findByPeriod(period: $eventFilterDto->getPeriod(), qb: $qb);
        }

        if ($eventFilterDto->getCategory() instanceof Category) {
            $this->findByCategory(category: $eventFilterDto->getCategory(), qb: $qb);
        }

        if (! empty($eventFilterDto->getKeyword())) {
            $this->findByTitle(keyword: $eventFilterDto->getKeyword(), qb: $qb);
        }

        $qb->andWhere(
            $qb->expr()->eq('event.isPublished', ':true')
        )->setParameter('true', true);

        $qb->andWhere(
            $qb->expr()->eq('event.isPrivate', ':false')
        )->setParameter('false', false);

        $qb->orderBy('event.startAt', Criteria::ASC);

        if ($isQuery) {
            return $qb->getQuery();
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @return Query|array<int, Event>
     */
    public function findByCategory(Category $category, QueryBuilder $qb = null, bool $isQuery = false): Query|array
    {
        if (! $qb instanceof QueryBuilder) {
            $qb = $this->createQueryBuilder('event');
        }
        $result = $qb;

        $qb->leftJoin('event.categories', 'category');
        $qb->andWhere(
            $qb->expr()->eq('category.id', ':category')
        )->setParameter('category', $category->getId(), 'uuid');

        if ($isQuery) {
            return $result->getQuery();
        }

        return $result->getQuery()->getResult();
    }

    /**
     * @return Query|array<int, Event>
     */
    public function findByTitle(string $keyword, QueryBuilder $qb = null, bool $isQuery = false): Query|array
    {
        if (! $qb instanceof QueryBuilder) {
            $qb = $this->createQueryBuilder('event');
        }
        $result = $qb;

        $qb->andWhere(
            $qb->expr()->like($qb->expr()->lower('event.title'), ':title')
        )->setParameter('title', '%' . strtolower($keyword) . '%');

        $qb->leftJoin('event.eventGroup', 'event_group');
        $qb->orWhere(
            $qb->expr()->like($qb->expr()->lower('event_group.title'), ':title')
        )->setParameter('title', '%' . strtolower($keyword) . '%');

        if ($isQuery) {
            return $result->getQuery();
        }

        return $result->getQuery()->getResult();
    }

    /**
     * @return array<int, Event>
     */
    public function findUpcomingByUser(User $user): array
    {
        $qb = $this->createQueryBuilder('event');
        $now = CarbonImmutable::now();

        $qb->andWhere(
            $qb->expr()->gte('event.startAt', ':now')
        )->setParameter('now', $now->toDateTimeImmutable(), Types::DATETIME_IMMUTABLE);

        $qb->andWhere(
            $qb->expr()->lt('event.startAt', ':twoWeekLater')
        )->setParameter('twoWeekLater', $now->addWeeks(2)->toDateTimeImmutable(), Types::DATETIME_IMMUTABLE);

        $qb->leftJoin('event.eventParticipants', 'eventParticipant');
        $qb->andWhere(
            $qb->expr()->eq('eventParticipant.owner', ':user')
        )->setParameter('user', $user);

        return $qb->getQuery()->getResult();
    }

    /**
     * @return array<int, Event>
     */
    public function findOwnedByUser(User $user): array
    {
        $qb = $this->createQueryBuilder('event');

        $qb->andWhere(
            $qb->expr()->eq('event.owner', ':user')
        )->setParameter('user', $user);

        return $qb->getQuery()->getResult();
    }

    /**
     * @return array<int, Event>
     */
    public function findAssociatedByUser(User $user): array
    {
        $qb = $this->createQueryBuilder('event');

        $qb->orWhere(
            $qb->expr()->eq('event.owner', ':user')
        )->setParameter('user', $user);

        $qb->leftJoin('event.eventParticipants', 'participant');
        $qb->orWhere(
            $qb->expr()->eq('participant.owner', ':user')
        )->setParameter('user', $user);

        $qb->leftJoin('event.eventOrganisers', 'organiser');
        $qb->orWhere(
            $qb->expr()->eq('organiser.owner', ':user')
        )->setParameter('user', $user);

        $qb->leftJoin('event.eventInvitations', 'invitation');
        $qb->orWhere(
            $qb->expr()->eq('invitation.owner', ':user')
        )->setParameter('user', $user);

        return $qb->getQuery()->getResult();
    }

    /**
     * @return array<int, Event>
     */
    public function findPublishEvents(EventGroup $eventGroup): array
    {
        $qb = $this->createQueryBuilder('event');

        $qb->andWhere(
            $qb->expr()->eq('event.eventGroup', ':id')
        )->setParameter('id', $eventGroup->getId());

        $qb->andWhere(
            $qb->expr()->eq('event.isPublished', ':true')
        )->setParameter('true', true);

        return $qb->getQuery()->getResult();
    }

    /**
     * @return array<int, Event>|Query
     */
    public function findByGroup(EventGroup $eventGroup, bool $isQuery = false): array|Query
    {
        $qb = $this->createQueryBuilder('event');
        $qb->andWhere(
            $qb->expr()->eq('event.eventGroup', ':group')
        )->setParameter('group', $eventGroup->getId(), 'uuid');

        $qb->andWhere(
            $qb->expr()->eq('event.isPublished', ':true')
        )->setParameter('true', true);

        $qb->orderBy('event.createdAt', Criteria::DESC);

        if ($isQuery) {
            return $qb->getQuery();
        }

        return $qb->getQuery()->getResult();
    }
}

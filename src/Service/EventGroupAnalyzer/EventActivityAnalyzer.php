<?php

declare(strict_types=1);

namespace App\Service\EventGroupAnalyzer;

namespace App\Service\EventGroupAnalyzer;

use App\Entity\Event\Event;
use Carbon\CarbonImmutable;

final class EventActivityAnalyzer
{
    /**
     * @param array<int, Event> $events
     * @return array<string>
     */
    public function analyze(array $events): array
    {
        $dailyActivityData = $this->calculateDailyActivity($events);
        return $this->fillMissingDatesWithZeros($dailyActivityData);
    }

    /**
     * @param array<int,string> $activityData
     * @return array<string>
     */
    private function fillMissingDatesWithZeros(array $activityData): array
    {
        $startDate = CarbonImmutable::now()->subDays(20);
        $endDate = CarbonImmutable::now()->addDays(20);

        $allDates = [];
        $currentDate = $startDate;

        while ($currentDate <= $endDate) {
            $allDates[$currentDate->format('Y-m-d')] = 0;
            $currentDate = $currentDate->addDay();
        }

        $filledData = array_merge($allDates, $activityData);

        ksort($filledData);

        return $filledData;
    }

    /**
     * @param array<int, Event> $events
     * @return array<string>
     */
    private function calculateDailyActivity(array $events): array
    {
        $dailyActivityData = [];

        foreach ($events as $event) {
            $createdAt = $event->getStartAt();
            $formattedDate = $createdAt->format('Y-m-d');

            $this->incrementDailyCounter($dailyActivityData, $formattedDate);
        }

        return $dailyActivityData;
    }

    /**
     * @param array<string> $counter
     */
    private function incrementDailyCounter(array &$counter, string $formattedDate): void
    {
        if (! isset($counter[$formattedDate])) {
            $counter[$formattedDate] = 0;
        }

        $counter[$formattedDate]++;
    }
}

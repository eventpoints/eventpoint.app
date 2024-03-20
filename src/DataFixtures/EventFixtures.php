<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Event\Event;
use App\Repository\User\EmailRepository;
use App\Service\AvatarService\AvatarService;
use Carbon\CarbonImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class EventFixtures extends Fixture
{
    public function __construct(
        private readonly AvatarService $avatarService,
        private readonly EmailRepository $emailRepository
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $range = range(0, 20);

        foreach ($range as $r) {
            $event = $this->createFakeEvent();
            $manager->persist($event);
        }
        $manager->flush();
    }

    public function createFakeEvent(): Event
    {
        $email = $this->emailRepository->findOneBy([
            'address' => 'kerrialbeckettnewham@gmail.com',
        ]);
        $days = random_int(0, 7);

        $startAt = $this->getStartAt($days);
        $endAt = $this->getEndAt($days, $startAt);

        $faker = Factory::create();
        $event = new Event(
            title: $faker->text(50),
            startAt: $startAt,
            endAt: $endAt,
            description: $faker->text(),
            latitude: (string) $faker->latitude(),
            longitude: (string) $faker->longitude(),
            base64Image: $this->avatarService->createAvatar($faker->email()),
            isPrivate: false,
            eventGroup: null,
            address: $faker->address(),
            owner: $email->getOwner()
        );

        return $event;
    }

    private function getStartAt(int $days): CarbonImmutable
    {
        return CarbonImmutable::createFromTime(random_int(12, 21))->addDays($days);
    }

    private function getEndAt(int $days, CarbonImmutable $startAt): CarbonImmutable
    {
        $endTime = CarbonImmutable::createFromTime(random_int(12, 21), 0, 0);
        $endTime->addDays($days);

        $minEndTime = $startAt->copy()->addMinutes(30);
        if ($endTime->lte($minEndTime)) {
            $endTime = $minEndTime->copy()->addMinutes(random_int(1, 30));
        }

        return $endTime;
    }
}

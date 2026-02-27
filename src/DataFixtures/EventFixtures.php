<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Event\Event;
use App\Entity\User\User;
use App\Enum\EventParticipantRoleEnum;
use App\Enum\EventStatusEnum;
use App\Factory\Event\EventFactory;
use App\Factory\Event\EventParticpantFactory;
use Carbon\CarbonImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class EventFixtures extends Fixture implements DependentFixtureInterface
{
    private const array LOCATIONS = [
        [
            'address' => 'Prague, Czech Republic',
            'lat' => '50.0755',
            'lng' => '14.4378',
        ],
        [
            'address' => 'Brno, Czech Republic',
            'lat' => '49.1951',
            'lng' => '16.6068',
        ],
        [
            'address' => 'Ostrava, Czech Republic',
            'lat' => '49.8209',
            'lng' => '18.2625',
        ],
        [
            'address' => 'Plzeň, Czech Republic',
            'lat' => '49.7384',
            'lng' => '13.3736',
        ],
        [
            'address' => 'Liberec, Czech Republic',
            'lat' => '50.7663',
            'lng' => '15.0543',
        ],
        [
            'address' => 'Olomouc, Czech Republic',
            'lat' => '49.5938',
            'lng' => '17.2509',
        ],
    ];

    public function __construct(
        private readonly EventFactory $eventFactory,
        private readonly EventParticpantFactory $eventParticipantFactory,
    ) {
    }

    #[\Override]
    public function load(ObjectManager $manager): void
    {
        $user = $this->getReference('user-author', User::class);

        foreach (EventStatusEnum::cases() as $index => $status) {
            $location = self::LOCATIONS[$index % count(self::LOCATIONS)];
            $event = $this->createEvent($status, $user, $location);

            $participant = $this->eventParticipantFactory->create(
                owner: $user,
                event: $event,
                role: EventParticipantRoleEnum::ROLE_ORGANISER
            );
            $event->addEventParticipant($participant);

            $manager->persist($event);
        }

        $manager->flush();
    }

    #[\Override]
    public function getDependencies(): array
    {
        return [UserFixtures::class];
    }

    /**
     * @param array{address: string, lat: string, lng: string} $location
     */
    private function createEvent(EventStatusEnum $status, User $owner, array $location): Event
    {
        $now = CarbonImmutable::now();

        $event = $this->eventFactory->create(
            title: 'Event - ' . $status->label(),
            address: $location['address'],
            startAt: $now->addDays(7),
            endAt: $now->addDays(7)->addHours(3),
            latitude: $location['lat'],
            longitude: $location['lng'],
            description: 'Test event in ' . $status->label() . ' status.',
            isPrivate: false,
            owner: $owner,
        );

        $event->setStatus($status);

        return $event;
    }
}

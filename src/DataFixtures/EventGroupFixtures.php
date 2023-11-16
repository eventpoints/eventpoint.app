<?php

namespace App\DataFixtures;

use App\Entity\EventGroup\EventGroup;
use App\Factory\EventGroup\EventGroupFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class EventGroupFixtures extends Fixture implements DependentFixtureInterface
{

    public function __construct(
        private readonly EventGroupFactory $eventGroupFactory,
        private readonly UserFixtures      $userFixtures
    )
    {
    }

    public function load(ObjectManager $manager): void
    {
        foreach ($this->getEventGroupData() as $index => $eventGroup) {
            $owner = $this->getReference(UserFixtures::TEST_USER_FIRST_NAME . $this->userFixtures->getUserFixtureTypes()[$index] . '_REF');
            $eventGroup->setOwner($owner);
            $manager->persist($eventGroup);
        }
        $manager->flush();
    }

    /**
     * @return array<int, EventGroup>
     */
    public function getEventGroupData(): array
    {
        return [
            $this->eventGroupFactory->create(title: 'Prague 7 Basket Ball Group', owner: null),
            $this->eventGroupFactory->create(title: 'Swing Dance Group Prague', owner: null),
            $this->eventGroupFactory->create(title: 'Prague Car enthusiasts', owner: null),
            $this->eventGroupFactory->create(title: 'Social Programmers', owner: null),
            $this->eventGroupFactory->create(title: 'Film Gang group', owner: null),
            $this->eventGroupFactory->create(title: 'Open Mic group', owner: null),
        ];
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class
        ];
    }
}

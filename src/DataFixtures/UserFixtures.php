<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    final public const TEST_USER_FIRST_NAME = 'fixture-';

    final public const TEST_USER_LAST_NAME = '-tester';

    final public const TEST_USER_EMAIL = '@eventpoint.com';

    final public const TEST_USER_PASSWORD = '12345678';

    public function __construct(
        private readonly UserFactory $userFactory,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        foreach ($this->getUserFixtureTypes() as $name) {
            $user = $this->userFactory->create(
                firstName: self::TEST_USER_FIRST_NAME . $name,
                lastName: self::TEST_USER_LAST_NAME,
                email: $name . self::TEST_USER_EMAIL,
                password: self::TEST_USER_PASSWORD
            );

            $user->setIsEnabled(true);
            $manager->persist($user);
            $this->addReference(self::TEST_USER_FIRST_NAME . $name . '_REF', $user);
        }

        $manager->flush();
    }

    /**
     * @return string[]
     */
    public function getUserFixtureTypes(): array
    {
        return [
            'event-creator',
            'group-creator',
            'group-organiser',
            'group-member',
            'event-organiser',
            'event-participant',
        ];
    }
}

<?php

namespace App\DataFixtures;

use App\Entity\Security\User;
use App\Enum\Gender;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends BaseFixture
{
    public const ADMIN_REFERENCE = 'user-admin';
    public const AGENT_COUNT = 5;
    public const USER_COUNT = 10;

    public function __construct(private readonly UserPasswordHasherInterface $hasher)
    {
    }

    protected function loadData(ObjectManager $manager): void
    {
        // Password for every seeded account: "password"
        $admin = (new User())
            ->setEmail('admin@propertymaster.test')
            ->setName('Site Admin')
            ->setRoles(['ROLE_ADMIN'])
            ->setIsVerified(true)
            ->setGender(Gender::Other);
        $admin->setPassword($this->hasher->hashPassword($admin, 'password'));
        $manager->persist($admin);
        $this->addReference(self::ADMIN_REFERENCE, $admin);

        for ($i = 0; $i < self::AGENT_COUNT; ++$i) {
            $agent = (new User())
                ->setEmail(sprintf('agent%d@propertymaster.test', $i + 1))
                ->setName($this->faker->name())
                ->setIsAgent(true)
                ->setIsVerified(true)
                ->setGender($this->faker->randomElement(Gender::cases()))
                ->setPhone($this->faker->phoneNumber())
                ->setMobile($this->faker->phoneNumber())
                ->setCity($this->faker->city())
                ->setState('Punjab')
                ->setCountry('India');
            $agent->setPassword($this->hasher->hashPassword($agent, 'password'));
            $manager->persist($agent);
            $this->addReference('agent-'.$i, $agent);
        }

        for ($i = 0; $i < self::USER_COUNT; ++$i) {
            $user = (new User())
                ->setEmail(sprintf('user%d@propertymaster.test', $i + 1))
                ->setName($this->faker->name())
                ->setIsVerified($this->faker->boolean(80))
                ->setGender($this->faker->randomElement(Gender::cases()));
            $user->setPassword($this->hasher->hashPassword($user, 'password'));
            $manager->persist($user);
            $this->addReference('user-'.$i, $user);
        }

        $manager->flush();
    }
}

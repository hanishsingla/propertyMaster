<?php

namespace App\DataFixtures;

use App\Entity\Property\Property;
use Doctrine\Persistence\ObjectManager;

class PropertyFixtures extends BaseFixture
{
    public function loadData(ObjectManager $manager): void
    {
        // php bin/console doctrine:fixtures:load

        $this->createMany(Property::class, 50, function (Property $property, $count) use ($manager) {
            $property
                ->setPropertyTitle($this->faker->text(50))
                ->setPropertyDescription($this->faker->text(500))
                ->setPropertyType('farm')
                ->setPropertyCategory('rent')
                ->setPropertyCity('Chandigarh')
                ->setPropertyState('Punjab')
                ->setPropertyStatus('rent')
                ->setSquareType('feet')
                ->setPropertyDirection('East')
                ->setPropertyPrice($this->faker->numberBetween(100000, 500000000))
                ->setPropertyArea($this->faker->numberBetween(100, 500))
                ->setPropertyBathRooms($this->faker->numberBetween(1, 5))
                ->setPropertyBedRooms($this->faker->numberBetween(1, 5))
                ->setIsCreatedAt(new \DateTime())
                ->setOwnerId('ddadad')
            ;

            $manager->persist($property);
        });

        $manager->flush();
    }
}

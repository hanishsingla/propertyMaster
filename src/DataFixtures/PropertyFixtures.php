<?php

namespace App\DataFixtures;

use App\Entity\Property\Property;
use Doctrine\ORM\EntityManager;

class PropertyFixtures extends BaseFixture
{
    public function loadData(EntityManager $manager)
    {
        $this->createMany(Property::class, 10, function (Property $property, $count) {
            $property
                ->setPropertyTitle('Why Asteroids Taste Like Bacon')
                 ->setSquareType('why-asteroids-taste-like-bacon-'.$count)
                ->setPropertyType()
                ->setPropertyDescription()
                ->setPropertyCity('Chandigarh')
                ->setPropertyDirection('East')
                ->setPropertyArea($this->faker->numberBetween(100, 500))
                ->setPropertyBathRooms($this->faker->numberBetween(1, 5))
                ->setPropertyBedRooms($this->faker->numberBetween(1, 5))
            ;
        });

        $manager->flush();
    }
}

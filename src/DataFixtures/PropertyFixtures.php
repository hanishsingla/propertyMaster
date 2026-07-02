<?php

namespace App\DataFixtures;

use App\Entity\Property\FavouriteProperty;
use App\Entity\Property\Property;
use App\Entity\Property\PropertyImage;
use App\Entity\Security\User;
use App\Enum\AreaUnit;
use App\Enum\Direction;
use App\Enum\ListingType;
use App\Enum\PropertyCategory;
use App\Enum\PropertyStatus;
use App\Enum\PropertyType;
use App\Service\SlugGenerator;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class PropertyFixtures extends BaseFixture implements DependentFixtureInterface
{
    private const SAMPLE_IMAGES = [
        'image/property/Most-Beautiful-House-in-the-World.jpg',
        'image/property/North Carolina.jpg',
        'image/property/modern-residential-building.jpg',
        'image/property/villa-house-model-key-drawing-retro-desktop-real-estate-sale-concept.jpg',
        'image/property/land-plot-with-nature-landscape-location-pin.jpg',
        'image/property/img-Besoins.jpg',
    ];

    private const CITIES = ['Chandigarh', 'Mohali', 'Ludhiana', 'Amritsar', 'Jalandhar', 'Patiala'];

    public function __construct(private readonly SlugGenerator $slugGenerator)
    {
    }

    public function getDependencies(): array
    {
        return [UserFixtures::class];
    }

    protected function loadData(ObjectManager $manager): void
    {
        $agents = [];
        for ($i = 0; $i < UserFixtures::AGENT_COUNT; ++$i) {
            $agents[] = $this->getReference('agent-'.$i, User::class);
        }

        $properties = [];
        for ($i = 0; $i < 30; ++$i) {
            $category = $this->faker->randomElement(PropertyCategory::cases());
            $listingType = $this->faker->randomElement(ListingType::cases());
            $isLand = $category->isLand();

            $title = ucfirst($this->faker->words(3, true));
            $property = (new Property())
                ->setOwner($this->faker->randomElement($agents))
                ->setTitle($title)
                ->setSlug($this->slugGenerator->generate($title, sprintf('%08d', $i)))
                ->setDescription($this->faker->paragraphs(3, true))
                ->setListingType($listingType)
                ->setCategory($category)
                ->setType($this->faker->randomElement(PropertyType::cases()))
                ->setStatus($this->faker->randomElement([
                    PropertyStatus::Published,
                    PropertyStatus::Published,
                    PropertyStatus::Published,
                    PropertyStatus::Draft,
                ]))
                ->setPriceMinor($this->faker->numberBetween(500000, 500000000) * 100)
                ->setCurrency('INR')
                ->setArea($this->faker->numberBetween(500, 8000))
                ->setAreaUnit(AreaUnit::SquareFeet)
                ->setBedRooms($isLand ? null : $this->faker->numberBetween(1, 6))
                ->setBathRooms($isLand ? null : $this->faker->numberBetween(1, 5))
                ->setRooms($isLand ? null : $this->faker->numberBetween(2, 10))
                ->setDirection($this->faker->randomElement(Direction::cases()))
                ->setCity($this->faker->randomElement(self::CITIES))
                ->setState('Punjab')
                ->setCountry('India')
                ->setLatitude($this->faker->latitude(30.5, 31.7))
                ->setLongitude($this->faker->longitude(74.8, 76.8))
                ->setIsFeatured($this->faker->boolean(25));

            // 2-4 images per property; first is the cover.
            $imageCount = $this->faker->numberBetween(2, 4);
            $picks = (array) $this->faker->randomElements(self::SAMPLE_IMAGES, $imageCount);
            foreach (array_values($picks) as $order => $path) {
                $image = (new PropertyImage())
                    ->setPath($path)
                    ->setSortOrder($order)
                    ->setIsCover(0 === $order);
                $property->addImage($image);
            }

            $manager->persist($property);
            $properties[] = $property;
        }

        // Seed some favourites: each regular user favourites a few random properties.
        for ($u = 0; $u < UserFixtures::USER_COUNT; ++$u) {
            $user = $this->getReference('user-'.$u, User::class);
            $faves = (array) $this->faker->randomElements($properties, $this->faker->numberBetween(0, 4));
            foreach ($faves as $property) {
                $fav = (new FavouriteProperty())
                    ->setUser($user)
                    ->setProperty($property);
                $manager->persist($fav);
            }
        }

        $manager->flush();
    }
}

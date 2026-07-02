<?php

namespace App\Service;

use App\Api\Dto\PropertyInput;
use App\Entity\Property\Property;
use App\Entity\Security\User;
use App\Enum\AreaUnit;
use App\Enum\Direction;
use App\Enum\ListingType;
use App\Enum\PropertyCategory;
use App\Enum\PropertyStatus;
use App\Enum\PropertyType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PropertyService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly ValidatorInterface $validator,
        private readonly SlugGenerator $slugGenerator,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data, User $owner): Property
    {
        $input = $this->hydrateAndValidate($data);

        $property = new Property();
        $property->setOwner($owner);
        $property->setSlug($this->slugGenerator->generate((string) $input->title));
        $this->apply($property, $input);

        $this->em->persist($property);
        $this->em->flush();

        return $property;
    }

    /**
     * @param array<string, mixed> $data
     */
    public function update(Property $property, array $data): Property
    {
        $input = $this->hydrateAndValidate($data);
        $this->apply($property, $input);
        $this->em->flush();

        return $property;
    }

    public function softDelete(Property $property): void
    {
        $property->softDelete();
        $this->em->flush();
    }

    /**
     * Parse raw query params into a normalised filter array for the repository.
     *
     * @param array<string,mixed> $query
     *
     * @return array<string,mixed>
     */
    public function buildFilters(array $query): array
    {
        $filters = [];

        if (isset($query['listingType']) && $lt = ListingType::tryFrom((string) $query['listingType'])) {
            $filters['listingType'] = $lt;
        }
        if (isset($query['type']) && $t = PropertyType::tryFrom((string) $query['type'])) {
            $filters['type'] = $t;
        }
        if (isset($query['category']) && $c = PropertyCategory::tryFrom((string) $query['category'])) {
            $filters['category'] = $c;
        }
        if (!empty($query['city'])) {
            $filters['city'] = (string) $query['city'];
        }
        if (isset($query['minPrice']) && is_numeric($query['minPrice'])) {
            $filters['minPrice'] = (int) ($query['minPrice'] * 100);
        }
        if (isset($query['maxPrice']) && is_numeric($query['maxPrice'])) {
            $filters['maxPrice'] = (int) ($query['maxPrice'] * 100);
        }
        if (isset($query['bedRooms']) && is_numeric($query['bedRooms'])) {
            $filters['bedRooms'] = (int) $query['bedRooms'];
        }
        if (isset($query['isFeatured'])) {
            $filters['isFeatured'] = filter_var($query['isFeatured'], \FILTER_VALIDATE_BOOL);
        }
        if (!empty($query['q'])) {
            $filters['q'] = (string) $query['q'];
        }
        if (in_array($query['sort'] ?? null, ['newest', 'price_asc', 'price_desc'], true)) {
            $filters['sort'] = $query['sort'];
        }

        return $filters;
    }

    private function apply(Property $property, PropertyInput $input): void
    {
        $property
            ->setTitle((string) $input->title)
            ->setDescription((string) $input->description)
            ->setListingType($input->listingType)
            ->setCategory($input->category)
            ->setType($input->type)
            ->setStatus($input->status ?? $property->getStatus())
            ->setPriceMinor((int) $input->price * 100)
            ->setArea((int) $input->area)
            ->setAreaUnit($input->areaUnit ?? AreaUnit::SquareFeet)
            ->setBedRooms($input->bedRooms)
            ->setBathRooms($input->bathRooms)
            ->setRooms($input->rooms)
            ->setDirection($input->direction)
            ->setCity((string) $input->city)
            ->setState($input->state ?? 'Punjab')
            ->setCountry($input->country ?? 'India')
            ->setLatitude($input->latitude)
            ->setLongitude($input->longitude)
            ->setIsFeatured($input->isFeatured);
    }

    /**
     * @param array<string, mixed> $data
     */
    private function hydrateAndValidate(array $data): PropertyInput
    {
        $violations = new ConstraintViolationList();
        $input = new PropertyInput();

        $input->title = $this->str($data, 'title');
        $input->description = $this->str($data, 'description');
        $input->city = $this->str($data, 'city');
        $input->state = $this->str($data, 'state');
        $input->country = $this->str($data, 'country');
        $input->price = $this->int($data, 'price');
        $input->area = $this->int($data, 'area');
        $input->bedRooms = $this->int($data, 'bedRooms');
        $input->bathRooms = $this->int($data, 'bathRooms');
        $input->rooms = $this->int($data, 'rooms');
        $input->latitude = isset($data['latitude']) ? (float) $data['latitude'] : null;
        $input->longitude = isset($data['longitude']) ? (float) $data['longitude'] : null;
        $input->isFeatured = (bool) ($data['isFeatured'] ?? false);

        $input->listingType = $this->enum(ListingType::class, $data, 'listingType', $violations);
        $input->category = $this->enum(PropertyCategory::class, $data, 'category', $violations);
        $input->type = $this->enum(PropertyType::class, $data, 'type', $violations);
        $input->status = $this->enum(PropertyStatus::class, $data, 'status', $violations);
        $input->areaUnit = $this->enum(AreaUnit::class, $data, 'areaUnit', $violations);
        $input->direction = $this->enum(Direction::class, $data, 'direction', $violations);

        $violations->addAll($this->validator->validate($input));

        if (count($violations) > 0) {
            throw new ValidationFailedException($input, $violations);
        }

        return $input;
    }

    /**
     * @param array<string, mixed> $data
     */
    private function str(array $data, string $key): ?string
    {
        $v = $data[$key] ?? null;

        return (null === $v || '' === $v) ? null : (string) $v;
    }

    /**
     * @param array<string, mixed> $data
     */
    private function int(array $data, string $key): ?int
    {
        $v = $data[$key] ?? null;

        return (null === $v || '' === $v) ? null : (int) $v;
    }

    /**
     * @template T of \BackedEnum
     *
     * @param class-string<T>      $enumClass
     * @param array<string, mixed> $data
     *
     * @return T|null
     */
    private function enum(string $enumClass, array $data, string $key, ConstraintViolationList $violations): ?\BackedEnum
    {
        $raw = $data[$key] ?? null;
        if (null === $raw || '' === $raw) {
            return null;
        }

        $resolved = $enumClass::tryFrom((string) $raw);
        if (null === $resolved) {
            $violations->add(new ConstraintViolation(
                sprintf('"%s" is not a valid value.', $raw),
                null,
                [],
                null,
                $key,
                $raw
            ));
        }

        return $resolved;
    }
}

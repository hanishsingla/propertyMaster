<?php

namespace App\Api\Dto;

use App\Enum\AreaUnit;
use App\Enum\Direction;
use App\Enum\ListingType;
use App\Enum\PropertyCategory;
use App\Enum\PropertyStatus;
use App\Enum\PropertyType;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Write model for creating/updating a property. Enum fields are validated as
 * backed-enum values; price is accepted in major units (rupees) from the client.
 */
class PropertyInput
{
    #[Assert\NotBlank]
    #[Assert\Length(max: 180)]
    public ?string $title = null;

    #[Assert\NotBlank]
    public ?string $description = null;

    #[Assert\NotNull]
    public ?ListingType $listingType = null;

    #[Assert\NotNull]
    public ?PropertyCategory $category = null;

    #[Assert\NotNull]
    public ?PropertyType $type = null;

    public ?PropertyStatus $status = null;

    #[Assert\NotNull]
    #[Assert\Positive]
    public ?int $price = null; // major units (rupees)

    #[Assert\NotNull]
    #[Assert\Positive]
    public ?int $area = null;

    public ?AreaUnit $areaUnit = null;

    #[Assert\PositiveOrZero]
    public ?int $bedRooms = null;

    #[Assert\PositiveOrZero]
    public ?int $bathRooms = null;

    #[Assert\PositiveOrZero]
    public ?int $rooms = null;

    public ?Direction $direction = null;

    #[Assert\NotBlank]
    public ?string $city = null;

    public ?string $state = null;

    public ?string $country = null;

    #[Assert\Range(min: -90, max: 90)]
    public ?float $latitude = null;

    #[Assert\Range(min: -180, max: 180)]
    public ?float $longitude = null;

    public bool $isFeatured = false;
}

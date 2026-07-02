<?php

namespace App\Enum;

enum PropertyCategory: string implements LabelledEnum
{
    case Villa = 'villa';
    case Apartment = 'apartment';
    case Floor = 'floor';
    case Office = 'office';
    case Shop = 'shop';
    case Hotel = 'hotel';
    case Warehouse = 'warehouse';
    case AgriculturalFarmLand = 'agricultural_farm_land';

    public function label(): string
    {
        return match ($this) {
            self::Villa => 'Villa',
            self::Apartment => 'Apartment',
            self::Floor => 'Floor',
            self::Office => 'Office',
            self::Shop => 'Shop',
            self::Hotel => 'Hotel',
            self::Warehouse => 'Warehouse',
            self::AgriculturalFarmLand => 'Agricultural / Farm Land',
        };
    }

    /**
     * Categories that have no rooms/beds/baths (land-like).
     */
    public function isLand(): bool
    {
        return self::AgriculturalFarmLand === $this;
    }
}

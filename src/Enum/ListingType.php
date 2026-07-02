<?php

namespace App\Enum;

enum ListingType: string implements LabelledEnum
{
    case Sale = 'sale';
    case Rent = 'rent';

    public function label(): string
    {
        return match ($this) {
            self::Sale => 'For Sale',
            self::Rent => 'For Rent',
        };
    }
}

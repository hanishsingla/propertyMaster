<?php

namespace App\Enum;

enum PropertyStatus: string implements LabelledEnum
{
    case Draft = 'draft';
    case Published = 'published';
    case Sold = 'sold';
    case Rented = 'rented';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::Published => 'Published',
            self::Sold => 'Sold',
            self::Rented => 'Rented',
        };
    }

    public function isPubliclyVisible(): bool
    {
        return self::Published === $this;
    }
}

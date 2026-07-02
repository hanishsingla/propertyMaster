<?php

namespace App\Enum;

enum AreaUnit: string implements LabelledEnum
{
    case SquareFeet = 'sq_ft';
    case SquareMetre = 'sq_m';

    public function label(): string
    {
        return match ($this) {
            self::SquareFeet => 'sq. ft.',
            self::SquareMetre => 'sq. m.',
        };
    }
}

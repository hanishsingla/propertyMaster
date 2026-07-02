<?php

namespace App\Enum;

enum Direction: string implements LabelledEnum
{
    case North = 'north';
    case South = 'south';
    case East = 'east';
    case West = 'west';

    public function label(): string
    {
        return match ($this) {
            self::North => 'North',
            self::South => 'South',
            self::East => 'East',
            self::West => 'West',
        };
    }
}

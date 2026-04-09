<?php

namespace App\Enums;

enum DisciplineLevel: string
{
    case LOW = 'Nhẹ';
    case MEDIUM = 'Trung bình';
    case HIGH = 'Nặng';

    public static function values(): array
    {
        return array_map(static fn (self $case) => $case->value, self::cases());
    }
}


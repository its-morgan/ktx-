<?php

namespace App\Enums;

enum RegistrationType: string
{
    case RENTAL = 'Thuê phòng';
    case RETURN = 'Trả phòng';
    case CHANGE = 'Đổi phòng';

    public static function values(): array
    {
        return array_map(static fn (self $case) => $case->value, self::cases());
    }
}

<?php

namespace App\Enums;

enum RegistrationStatus: string
{
    case PENDING = 'Chờ xử lý';
    case APPROVED = 'Đã duyệt';
    case REJECTED = 'Từ chối';

    public static function values(): array
    {
        return array_map(static fn (self $case) => $case->value, self::cases());
    }
}


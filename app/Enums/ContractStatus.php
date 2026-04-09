<?php

namespace App\Enums;

enum ContractStatus: string
{
    case ACTIVE = 'Đang hiệu lực';
    case EXPIRED = 'Hết hạn';
    case TERMINATED = 'Đã thanh lý';

    public static function values(): array
    {
        return array_map(static fn (self $case) => $case->value, self::cases());
    }
}


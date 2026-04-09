<?php

namespace App\Enums;

enum InvoiceStatus: string
{
    case PENDING = 'Chưa thanh toán';
    case PAID = 'Đã thanh toán';

    public static function values(): array
    {
        return array_map(static fn (self $case) => $case->value, self::cases());
    }
}

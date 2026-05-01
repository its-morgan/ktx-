<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case AdminTruong = 'admin_truong';
    case AdminToaNha = 'admin_toanha';
    case LeTan = 'le_tan';
    case SinhVien = 'sinhvien';
    case CuuSinhVien = 'cuu_sinhvien';

    public function label(): string
    {
        return match($this) {
            self::Admin => 'Quản trị viên',
            self::AdminTruong => 'Admin trường',
            self::AdminToaNha => 'Admin tòa nhà',
            self::LeTan => 'Lễ tân',
            self::SinhVien => 'Sinh viên',
            self::CuuSinhVien => 'Cựu sinh viên',
        };
    }

    public static function values(): array
    {
        return array_map(static fn (self $case) => $case->value, self::cases());
    }
}

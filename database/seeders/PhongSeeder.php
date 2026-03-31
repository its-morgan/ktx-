<?php

namespace Database\Seeders;

use App\Models\Phong;
use Illuminate\Database\Seeder;

class PhongSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Phong::create([
            'tenphong' => 'a101',
            'giaphong' => 800000,
            'soluongtoida' => 4,
            'mota' => 'Phòng tiêu chuẩn',
            'gioitinh' => 'Nam',
        ]);
        Phong::create([
            'tenphong' => 'a102',
            'giaphong' => 800000,
            'soluongtoida' => 4,
            'mota' => 'Phòng tiêu chuẩn',
            'gioitinh' => 'Nữ',
        ]);
        Phong::create([
            'tenphong' => 'a103',
            'giaphong' => 850000,
            'soluongtoida' => 4,
            'mota' => 'Phòng có cửa sổ lớn',
            'gioitinh' => 'Nam',
        ]);
        Phong::create([
            'tenphong' => 'a104',
            'giaphong' => 850000,
            'soluongtoida' => 4,
            'mota' => 'Phòng có cửa sổ lớn',
            'gioitinh' => 'Nữ',
        ]);
        Phong::create([
            'tenphong' => 'b201',
            'giaphong' => 900000,
            'soluongtoida' => 6,
            'mota' => 'Phòng 6 người',
            'gioitinh' => 'Nam',
        ]);
        Phong::create([
            'tenphong' => 'b202',
            'giaphong' => 900000,
            'soluongtoida' => 6,
            'mota' => 'Phòng 6 người',
            'gioitinh' => 'Nữ',
        ]);
        Phong::create([
            'tenphong' => 'b203',
            'giaphong' => 950000,
            'soluongtoida' => 6,
            'mota' => 'Phòng thoáng',
            'gioitinh' => 'Nam',
        ]);
        Phong::create([
            'tenphong' => 'c301',
            'giaphong' => 1000000,
            'soluongtoida' => 8,
            'mota' => 'Phòng 8 người',
            'gioitinh' => 'Nữ',
        ]);
        Phong::create([
            'tenphong' => 'c302',
            'giaphong' => 1000000,
            'soluongtoida' => 8,
            'mota' => 'Phòng 8 người',
            'gioitinh' => 'Nam',
        ]);
        Phong::create([
            'tenphong' => 'c303',
            'giaphong' => 1100000,
            'soluongtoida' => 8,
            'mota' => 'Phòng 8 người, gần cầu thang',
            'gioitinh' => 'Nữ',
        ]);
    }
}

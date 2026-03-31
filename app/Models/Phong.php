<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Phong extends Model
{
    use HasFactory;

    protected $table = 'phong';

    protected $fillable = [
        'tenphong',
        'giaphong',
        'soluongtoida',
        'mota',
        'gioitinh',
    ];

    public function danhsachsinhvien(): HasMany
    {
        return $this->hasMany(Sinhvien::class, 'phong_id');
    }

    public function danhsachhoadon(): HasMany
    {
        return $this->hasMany(Hoadon::class, 'phong_id');
    }

    public function danhsachdangky(): HasMany
    {
        return $this->hasMany(Dangky::class, 'phong_id');
    }

    public function danhsachtaisan(): HasMany
    {
        return $this->hasMany(Taisan::class, 'phong_id');
    }

    public function danhsachhopdong(): HasMany
    {
        return $this->hasMany(Hopdong::class, 'phong_id');
    }
}

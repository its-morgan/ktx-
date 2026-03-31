<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Sinhvien extends Model
{
    use HasFactory;

    protected $table = 'sinhvien';

    protected $fillable = [
        'user_id',
        'masinhvien',
        'lop',
        'sodienthoai',
        'phong_id',
        'ngay_vao',
        'ngay_het_han',
    ];

    public function taikhoan(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function phong(): BelongsTo
    {
        return $this->belongsTo(Phong::class, 'phong_id');
    }

    public function danhsachdangky(): HasMany
    {
        return $this->hasMany(Dangky::class, 'sinhvien_id');
    }

    public function danhsachbaohong(): HasMany
    {
        return $this->hasMany(Baohong::class, 'sinhvien_id');
    }

    public function danhsachhopdong(): HasMany
    {
        return $this->hasMany(Hopdong::class, 'sinhvien_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Hopdong extends Model
{
    use HasFactory;

    protected $table = 'hopdong';

    protected $fillable = [
        'sinhvien_id',
        'phong_id',
        'ngay_bat_dau',
        'ngay_ket_thuc',
        'giaphong_luc_ky',
        'trang_thai',
        'ghichu',
    ];

    public function sinhvien(): BelongsTo
    {
        return $this->belongsTo(Sinhvien::class, 'sinhvien_id');
    }

    public function phong(): BelongsTo
    {
        return $this->belongsTo(Phong::class, 'phong_id');
    }
}

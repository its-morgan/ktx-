<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class Hoadon extends Model
{
    use HasFactory;

    protected $table = 'hoadon';

    protected $fillable = [
        'phong_id',
        'thang',
        'nam',
        'chisodiencu',
        'chisodienmoi',
        'chisonuoccu',
        'chisonuocmoi',
        'tongtien',
        'tienphong',
        'tiendien',
        'tiennuoc',
        'phidichvu',
        'trangthaithanhtoan',
        'ngayxuat',
    ];

    public function phong(): BelongsTo
    {
        return $this->belongsTo(Phong::class, 'phong_id');
    }
}

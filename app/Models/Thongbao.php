<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Thongbao extends Model
{
    use HasFactory;

    protected $table = 'thongbao';

    protected $fillable = [
        'tieude',
        'noidung',
        'ngaydang',
    ];
}

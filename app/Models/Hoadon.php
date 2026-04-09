<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class Hoadon extends Model
{
    use HasFactory;

    public const TRANGTHAI_CHUA_THANH_TOAN = 'Chưa thanh toán';
    public const TRANGTHAI_DA_THANH_TOAN = 'Đã thanh toán';
    public const TRANGTHAI_QUA_HAN = 'Quá hạn';

    private const ALLOWED_TRANSITIONS = [
        self::TRANGTHAI_CHUA_THANH_TOAN => [
            self::TRANGTHAI_DA_THANH_TOAN,
            self::TRANGTHAI_QUA_HAN,
        ],
        self::TRANGTHAI_QUA_HAN => [
            self::TRANGTHAI_DA_THANH_TOAN,
        ],
        self::TRANGTHAI_DA_THANH_TOAN => [],
    ];

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

    public function canTransitionTo(string $targetState): bool
    {
        $currentState = $this->normalizeState($this->trangthaithanhtoan);
        $targetState = $this->normalizeState($targetState);

        if (! array_key_exists($currentState, self::ALLOWED_TRANSITIONS)) {
            return false;
        }

        return in_array($targetState, self::ALLOWED_TRANSITIONS[$currentState], true);
    }

    public function transitionTo(string $targetState): bool
    {
        $targetState = $this->normalizeState($targetState);

        if (! $this->canTransitionTo($targetState)) {
            return false;
        }

        return $this->update([
            'trangthaithanhtoan' => $targetState,
        ]);
    }

    private function normalizeState(string $state): string
    {
        return match ($state) {
            'Chua thanh toan' => self::TRANGTHAI_CHUA_THANH_TOAN,
            'Da thanh toan' => self::TRANGTHAI_DA_THANH_TOAN,
            'Qua han' => self::TRANGTHAI_QUA_HAN,
            default => $state,
        };
    }
}

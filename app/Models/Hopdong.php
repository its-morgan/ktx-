<?php

namespace App\Models;

use App\Enums\ContractStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Hopdong extends Model
{
    use HasFactory;

    public const TRANGTHAI_DANG_HIEU_LUC = ContractStatus::ACTIVE->value;
    public const TRANGTHAI_HET_HAN = ContractStatus::EXPIRED->value;
    public const TRANGTHAI_DA_THANH_LY = ContractStatus::TERMINATED->value;

    private const ALLOWED_TRANSITIONS = [
        self::TRANGTHAI_DANG_HIEU_LUC => [
            self::TRANGTHAI_HET_HAN,
            self::TRANGTHAI_DA_THANH_LY,
        ],
        self::TRANGTHAI_HET_HAN => [
            self::TRANGTHAI_DA_THANH_LY,
        ],
        self::TRANGTHAI_DA_THANH_LY => [],
    ];

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

    public function canTransitionTo(string $targetState): bool
    {
        $currentState = $this->normalizeState($this->trang_thai);
        $targetState = $this->normalizeState($targetState);

        if (! array_key_exists($currentState, self::ALLOWED_TRANSITIONS)) {
            return false;
        }

        return in_array($targetState, self::ALLOWED_TRANSITIONS[$currentState], true);
    }

    public function transitionTo(string $targetState, ?string $note = null): bool
    {
        $targetState = $this->normalizeState($targetState);

        if (! $this->canTransitionTo($targetState)) {
            return false;
        }

        return $this->update([
            'trang_thai' => $targetState,
            'ghichu' => $note,
        ]);
    }

    private function normalizeState(string $state): string
    {
        return match ($state) {
            'Dang hieu luc' => self::TRANGTHAI_DANG_HIEU_LUC,
            'Het han' => self::TRANGTHAI_HET_HAN,
            'Da thanh ly' => self::TRANGTHAI_DA_THANH_LY,
            default => $state,
        };
    }
}

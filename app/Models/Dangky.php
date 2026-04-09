<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class Dangky extends Model
{
    use HasFactory;

    public const LOAI_THUE_PHONG = 'Thuê phòng';
    public const LOAI_TRA_PHONG = 'Trả phòng';
    public const LOAI_DOI_PHONG = 'Doi phong';

    public const TRANGTHAI_CHO_XU_LY = 'Chờ xử lý';
    public const TRANGTHAI_DA_DUYET = 'Đã duyệt';
    public const TRANGTHAI_TU_CHOI = 'Từ chối';

    private const ALLOWED_TRANSITIONS = [
        self::TRANGTHAI_CHO_XU_LY => [
            self::TRANGTHAI_DA_DUYET,
            self::TRANGTHAI_TU_CHOI,
        ],
        self::TRANGTHAI_DA_DUYET => [],
        self::TRANGTHAI_TU_CHOI => [],
    ];

    protected $table = 'dangky';

    protected $fillable = [
        'sinhvien_id',
        'phong_id',
        'loaidangky',
        'trangthai',
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
        $currentState = $this->normalizeState($this->trangthai);
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
            'trangthai' => $targetState,
            'ghichu' => $note,
        ]);
    }

    private function normalizeState(string $state): string
    {
        return match ($state) {
            'Cho xu ly' => self::TRANGTHAI_CHO_XU_LY,
            'Da duyet' => self::TRANGTHAI_DA_DUYET,
            'Tu choi' => self::TRANGTHAI_TU_CHOI,
            default => $state,
        };
    }
}

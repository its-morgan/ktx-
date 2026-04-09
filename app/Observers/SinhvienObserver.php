<?php

namespace App\Observers;

use App\Models\Phong;
use App\Models\Sinhvien;

class SinhvienObserver
{
    public function updated(Sinhvien $sinhvien): void
    {
        if (! $sinhvien->wasChanged('phong_id')) {
            return;
        }

        $phongCuId = $this->toPhongId($sinhvien->getOriginal('phong_id'));
        $phongMoiId = $this->toPhongId($sinhvien->phong_id);

        if ($phongCuId !== null && $phongCuId !== $phongMoiId) {
            Phong::where('id', $phongCuId)
                ->where('dango', '>', 0)
                ->decrement('dango');
        }

        if ($phongMoiId !== null && $phongMoiId !== $phongCuId) {
            Phong::where('id', $phongMoiId)->increment('dango');
        }
    }

    public function deleted(Sinhvien $sinhvien): void
    {
        $phongId = $this->toPhongId($sinhvien->phong_id ?? $sinhvien->getOriginal('phong_id'));
        if ($phongId === null) {
            return;
        }

        Phong::where('id', $phongId)
            ->where('dango', '>', 0)
            ->decrement('dango');
    }

    private function toPhongId(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        $phongId = (int) $value;

        return $phongId > 0 ? $phongId : null;
    }
}

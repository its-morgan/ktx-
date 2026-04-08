<?php

namespace App\Traits;

use App\Models\Kyluat;

trait KiemtraKyluat
{
    /**
     * Kiểm tra sinh viên có bị chặn đăng ký/gia hạn do vi phạm kỷ luật không.
     * 
     * Logic:
     * - Nếu có > 3 lần kỷ luật "Nhẹ" → Bị chặn
     * - Nếu có 1 lần kỷ luật "Nặng" → Bị chặn
     * 
     * @param int $sinhvienId
     * @return array ['bi_chan' => bool, 'ly_do' => string|null]
     */
    public function kiemTraKyluat(int $sinhvienId): array
    {
        // Đếm số lần kỷ luật "Nhẹ"
        $soLanNhe = Kyluat::where('sinhvien_id', $sinhvienId)
            ->where('mucdo', 'Nhe')
            ->count();

        // Kiểm tra có kỷ luật "Nặng" không
        $coNang = Kyluat::where('sinhvien_id', $sinhvienId)
            ->where('mucdo', 'Nang')
            ->exists();

        if ($coNang) {
            return [
                'bi_chan' => true,
                'ly_do' => 'Bạn đã bị kỷ luật mức độ Nặng, không được phép đăng ký hoặc gia hạn.',
            ];
        }

        if ($soLanNhe > 3) {
            return [
                'bi_chan' => true,
                'ly_do' => "Bạn đã bị kỷ luật {$soLanNhe} lần mức độ Nhẹ (quá 3 lần), không được phép đăng ký hoặc gia hạn.",
            ];
        }

        return [
            'bi_chan' => false,
            'ly_do' => null,
        ];
    }
}

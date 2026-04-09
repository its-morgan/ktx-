<?php

namespace App\Services;

use App\Enums\ContractStatus;
use App\Enums\InvoiceStatus;
use App\Enums\RegistrationStatus;
use App\Enums\RegistrationType;
use App\Mail\DangkyDaDuyetMail;
use App\Models\Dangky;
use App\Models\Hoadon;
use App\Models\Hopdong;
use App\Models\Phong;
use App\Models\Sinhvien;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class RegistrationService
{
    private const MESSAGE_ROOM_CONFLICT = 'Phong da day hoac dang co nguoi khac dang ky, vui long thu lai.';

    public function duyetDangKy(int $id, ?string $ngayHetHan = null): array
    {
        return DB::transaction(function () use ($id, $ngayHetHan) {
            $dangky = Dangky::with(['sinhvien.taikhoan', 'phong'])->where('id', $id)->lockForUpdate()->first();
            if (! $dangky) {
                return $this->buildResult('loi', 'Khong tim thay dang ky.');
            }

            if ($dangky->trangthai !== RegistrationStatus::PENDING->value) {
                return $this->buildResult('loi', 'Dang ky nay da duoc xu ly truoc do.');
            }

            $sinhvien = Sinhvien::where('id', $dangky->sinhvien?->id)->lockForUpdate()->first();
            $phong = Phong::where('id', $dangky->phong?->id)->lockForUpdate()->first();
            if (! $sinhvien || ! $phong) {
                return $this->buildResult('loi', 'Thieu du lieu sinh vien hoac phong.');
            }

            if ($dangky->loaidangky === RegistrationType::RETURN->value) {
                return $this->approveLeaveRoom($dangky, $sinhvien);
            }

            $ngayBatDau = now()->format('Y-m-d');
            $ngayKetThuc = $ngayHetHan ?? now()->addMonths(5)->format('Y-m-d');
            if (strtotime($ngayKetThuc) <= strtotime($ngayBatDau)) {
                return $this->buildResult('loi', 'Ngay ket thuc phai sau ngay bat dau.');
            }

            if ($dangky->loaidangky === RegistrationType::RENTAL->value && $sinhvien->phong_id) {
                return $this->buildResult('loi', 'Sinh vien da co phong, khong the duyet.');
            }

            $maxCapacity = $this->getMaxCapacity($phong);
            $currentCount = Sinhvien::where('phong_id', $phong->id)->count();
            if ($currentCount >= $maxCapacity) {
                return $this->buildResult('loi', self::MESSAGE_ROOM_CONFLICT);
            }

            if (! $dangky->transitionTo(RegistrationStatus::APPROVED->value, null)) {
                return $this->buildResult('loi', 'Khong the chuyen trang thai don dang ky.');
            }

            $phongCuId = (int) ($sinhvien->phong_id ?? 0);
            if ($dangky->loaidangky === RegistrationType::CHANGE->value && $sinhvien->phong_id) {
                Phong::where('id', (int) $sinhvien->phong_id)->lockForUpdate()->first();
                Hopdong::where('sinhvien_id', $sinhvien->id)
                    ->where('trang_thai', ContractStatus::ACTIVE->value)
                    ->update(['trang_thai' => ContractStatus::TERMINATED->value]);
            }

            $sinhvien->update([
                'phong_id' => $phong->id,
                'ngay_vao' => $ngayBatDau,
                'ngay_het_han' => $ngayKetThuc,
            ]);
            $this->syncActualOccupancy([$phongCuId, (int) $phong->id]);

            $hopdong = Hopdong::create([
                'sinhvien_id' => $sinhvien->id,
                'phong_id' => $phong->id,
                'ngay_bat_dau' => $ngayBatDau,
                'ngay_ket_thuc' => $ngayKetThuc,
                'giaphong_luc_ky' => (int) $phong->giaphong,
                'trang_thai' => ContractStatus::ACTIVE->value,
                'ghichu' => null,
            ]);

            $hoadon = $this->createInitialInvoice($phong);

            try {
                $email = $sinhvien->taikhoan?->email;
                if ($email) {
                    Mail::to($email)->send(new DangkyDaDuyetMail($sinhvien, $phong, $hopdong, $hoadon));
                }
            } catch (\Throwable $e) {
                report($e);
                Log::warning('Khong gui duoc email thong bao duyet dang ky.', [
                    'sinhvien_id' => $sinhvien->id,
                    'dangky_id' => $dangky->id,
                    'email' => $sinhvien->taikhoan?->email,
                    'error' => $e->getMessage(),
                ]);
            }

            return $this->buildResult('thanhcong', 'Duyet dang ky thanh cong. Da tao hop dong, hoa don dau tien va gui email thong bao.');
        });
    }

    private function approveLeaveRoom(Dangky $dangky, Sinhvien $sinhvien): array
    {
        $phongDangO = $sinhvien->phong_id
            ? Phong::where('id', (int) $sinhvien->phong_id)->lockForUpdate()->first()
            : null;
        $phongDangOId = (int) ($phongDangO?->id ?? 0);
        $giaPhongDangO = (int) ($phongDangO?->giaphong ?? 0);

        if (! $dangky->transitionTo(RegistrationStatus::APPROVED->value, null)) {
            return $this->buildResult('loi', 'Khong the chuyen trang thai don dang ky.');
        }

        Hopdong::where('sinhvien_id', $sinhvien->id)
            ->where('trang_thai', ContractStatus::ACTIVE->value)
            ->update(['trang_thai' => ContractStatus::TERMINATED->value]);

        $sinhvien->update(['phong_id' => null]);
        $this->syncActualOccupancy([$phongDangOId]);

        $currentDay = (int) now()->format('d');
        $daysInMonth = (int) now()->daysInMonth;
        $ratio = max(0, 1 - ($currentDay / $daysInMonth));
        $refundAmount = max(0, round($giaPhongDangO * $ratio));

        return $this->buildResult('thanhcong', 'Duyet yeu cau tra phong thanh cong. So tien hoan du kien: '.number_format($refundAmount).' VND.');
    }

    private function syncActualOccupancy(array $roomIds): void
    {
        $validRoomIds = array_unique(
            array_filter(
                array_map(static fn ($roomId) => (int) $roomId, $roomIds),
                static fn (int $roomId) => $roomId > 0
            )
        );

        foreach ($validRoomIds as $roomId) {
            $currentOccupancy = Sinhvien::where('phong_id', $roomId)->count();
            Phong::where('id', $roomId)->update(['dango' => $currentOccupancy]);
        }
    }

    private function getMaxCapacity(Phong $phong): int
    {
        $maxCapacity = (int) $phong->succhuamax;

        return max(1, $maxCapacity);
    }

    private function createInitalInvoice(Phong $phong): Hoadon
    {
        $month = (int) now()->format('m');
        $year = (int) now()->format('Y');
        $roomPrice = (int) $phong->giaphong;

        return Hoadon::firstOrCreate(
            [
                'phong_id' => $phong->id,
                'thang' => $month,
                'nam' => $year,
            ],
            [
                'chisodiencu' => 0,
                'chisodienmoi' => 0,
                'chisonuoccu' => 0,
                'chisonuocmoi' => 0,
                'tienphong' => $roomPrice,
                'tiendien' => 0,
                'tiennuoc' => 0,
                'phidichvu' => 0,
                'tongtien' => $roomPrice,
                'trangthaithanhtoan' => InvoiceStatus::PENDING->value,
                'ngayxuat' => now()->format('Y-m-d'),
            ]
        );
    }

    private function buildResult(string $toastType, string $toastMessage): array
    {
        return [
            'toast_loai' => $toastType,
            'toast_noidung' => $toastMessage,
        ];
    }
}

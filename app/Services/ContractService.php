<?php

namespace App\Services;

use App\Enums\ContractStatus;
use App\Models\Hopdong;
use App\Models\Sinhvien;
use Illuminate\Support\Facades\DB;

class ContractService
{
    /**
     * Create a new contract.
     * 
     * @param array $data Validated contract data (sinhvien_id, phong_id, ngay_bat_dau, ngay_ket_thuc)
     * @return array ['success' => bool, 'message' => string, 'contract' => Hopdong|null]
     */
    public function createContract(array $data): array
    {
        try {
            return DB::transaction(function () use ($data) {
                $sinhvien = Sinhvien::where('id', (int) $data['sinhvien_id'])
                    ->lockForUpdate()
                    ->first();
                $phong = \App\Models\Phong::where('id', (int) $data['phong_id'])
                    ->lockForUpdate()
                    ->first();

                if (!$sinhvien || !$phong) {
                    return ['success' => false, 'message' => 'Không tìm thấy dữ liệu sinh viên hoặc phòng.', 'contract' => null];
                }

                // Check if student already has a room
                if ($sinhvien->phong_id) {
                    return ['success' => false, 'message' => 'Sinh viên đã có phòng, không thể tạo hợp đồng mới.', 'contract' => null];
                }

                // Check if student already has active contract in different room
                $activeContract = Hopdong::where('sinhvien_id', $sinhvien->id)
                    ->where('trang_thai', ContractStatus::ACTIVE->value)
                    ->lockForUpdate()
                    ->first();

                if ($activeContract) {
                    return ['success' => false, 'message' => 'Sinh viên đang có hợp đồng hiệu lực ở phòng ' . $activeContract->phong->tenphong . ', không thể tạo thêm.', 'contract' => null];
                }

                // Check room capacity
                if ($phong->dango >= $phong->succhuamax) {
                    return ['success' => false, 'message' => 'Phòng đã đầy, không thể thêm sinh viên.', 'contract' => null];
                }

                // Check gender compatibility
                $gioitinhSV = $sinhvien->taikhoan->gioitinh ?? null;
                if ($phong->gioitinh && $gioitinhSV && $phong->gioitinh !== $gioitinhSV) {
                    return ['success' => false, 'message' => 'Giới tính sinh viên không phù hợp với phòng.', 'contract' => null];
                }

                // Create contract
                $contract = Hopdong::create([
                    'sinhvien_id' => $sinhvien->id,
                    'phong_id' => $phong->id,
                    'ngay_bat_dau' => $data['ngay_bat_dau'],
                    'ngay_ket_thuc' => $data['ngay_ket_thuc'],
                    'giaphong_luc_ky' => (int) $phong->giaphong,
                    'trang_thai' => ContractStatus::ACTIVE->value,
                    'ghichu' => null,
                ]);

                // Update student with room assignment
                $sinhvien->update([
                    'phong_id' => $phong->id,
                    'ngay_vao' => $data['ngay_bat_dau'],
                    'ngay_het_han' => $data['ngay_ket_thuc'],
                ]);

                // Sync occupancy count
                $this->syncOccupancy([(int) $phong->id]);

                return ['success' => true, 'message' => 'Tạo hợp đồng thành công.', 'contract' => $contract];
            });
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => 'Có lỗi xảy ra: ' . $e->getMessage(), 'contract' => null];
        }
    }

    /**
     * Extend a contract.
     * 
     * @param int $contractId
     * @param string $newEndDate (Y-m-d format)
     * @param string $currentEndDate (for validation)
     * @return array ['success' => bool, 'message' => string]
     */
    public function extendContract(int $contractId, string $newEndDate, string $currentEndDate): array
    {
        $hopdong = Hopdong::find($contractId);

        if (!$hopdong) {
            return ['success' => false, 'message' => 'Không tìm thấy hợp đồng.'];
        }

        // Validate that new end date is after current end date
        if (strtotime($newEndDate) <= strtotime($currentEndDate)) {
            return ['success' => false, 'message' => 'Ngày kết thúc mới phải sau ngày kết thúc hiện tại của hợp đồng (' . $currentEndDate . ').'];
        }

        $phong = $hopdong->phong;
        $sinhvien = $hopdong->sinhvien;

        if (!$phong || !$sinhvien) {
            return ['success' => false, 'message' => 'Thiếu dữ liệu phòng hoặc sinh viên để gia hạn.'];
        }

        // Check gender compatibility hasn't changed
        $roomGender = $phong->gioitinh ?? null;
        $studentGender = $sinhvien->taikhoan->gioitinh ?? null;
        if ($roomGender && $studentGender && $roomGender !== $studentGender) {
            return ['success' => false, 'message' => 'Phòng đã đổi giới tính, không thể gia hạn hợp đồng.'];
        }

        // Check room still has capacity
        $currentOccupancy = Sinhvien::where('phong_id', $phong->id)->count();
        if ($currentOccupancy > (int) $phong->succhuamax) {
            return ['success' => false, 'message' => 'Phòng đã đầy, không thể gia hạn hợp đồng.'];
        }

        // Update contract
        $hopdong->update(['ngay_ket_thuc' => $newEndDate]);

        // Update student if still in same room
        if ($sinhvien->phong_id === $phong->id) {
            $sinhvien->update(['ngay_het_han' => $newEndDate]);
        }

        return ['success' => true, 'message' => 'Gia hạn hợp đồng thành công.'];
    }

    /**
     * Close/terminate a contract and remove student from room.
     * 
     * @param int $contractId
     * @return array ['success' => bool, 'message' => string]
     */
    public function closeContract(int $contractId): array
    {
        $hopdong = Hopdong::find($contractId);

        if (!$hopdong) {
            return ['success' => false, 'message' => 'Không tìm thấy hợp đồng.'];
        }

        if ($hopdong->trang_thai === ContractStatus::TERMINATED->value) {
            return ['success' => false, 'message' => 'Hợp đồng đã được thanh lý trước đó.'];
        }

        $sinhvien = $hopdong->sinhvien;

        try {
            return DB::transaction(function () use ($hopdong, $sinhvien) {
                $hopdong->update(['trang_thai' => ContractStatus::TERMINATED->value]);

                if ($sinhvien) {
                    $currentRoomId = (int) ($sinhvien->phong_id ?? $hopdong->phong_id ?? 0);
                    $sinhvien->update([
                        'phong_id' => null,
                        'ngay_vao' => null,
                        'ngay_het_han' => null,
                    ]);

                    // Terminate all other active contracts for this student
                    Hopdong::where('sinhvien_id', $sinhvien->id)
                        ->where('trang_thai', ContractStatus::ACTIVE->value)
                        ->where('id', '<>', $hopdong->id)
                        ->update(['trang_thai' => ContractStatus::TERMINATED->value]);

                    // Update occupancy count
                    $this->syncOccupancy([$currentRoomId]);
                }

                return ['success' => true, 'message' => 'Đã thanh lý hợp đồng và giải phóng sinh viên khỏi phòng.'];
            });
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => 'Có lỗi xảy ra: ' . $e->getMessage()];
        }
    }

    /**
     * Synchronize occupancy count (dango) for given rooms.
     * 
     * @param array $roomIds
     * @return void
     */
    private function syncOccupancy(array $roomIds): void
    {
        foreach ($roomIds as $roomId) {
            $count = Sinhvien::where('phong_id', $roomId)->count();
            \App\Models\Phong::where('id', $roomId)->update(['dango' => $count]);
        }
    }
}

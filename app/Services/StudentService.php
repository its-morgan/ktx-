<?php

namespace App\Services;

use App\Enums\ContractStatus;
use App\Models\Hopdong;
use App\Models\Phong;
use App\Models\Sinhvien;
use Illuminate\Support\Facades\DB;

class StudentService
{
    /**
     * Update student profile information.
     * 
     * @param int $studentId
     * @param array $data (name, masinhvien, lop, sodienthoai, gioitinh)
     * @return array ['success' => bool, 'message' => string]
     */
    public function updateStudent(int $studentId, array $data): array
    {
        $sinhvien = Sinhvien::find($studentId);

        if (!$sinhvien) {
            return ['success' => false, 'message' => 'Không tìm thấy sinh viên.'];
        }

        $user = $sinhvien->taikhoan;
        if (!$user) {
            return ['success' => false, 'message' => 'Không tìm thấy tài khoản sinh viên.'];
        }

        try {
            $user->update(['name' => $data['name'], 'gioitinh' => $data['gioitinh']]);

            $sinhvien->update([
                'masinhvien' => $data['masinhvien'],
                'lop' => $data['lop'],
                'sodienthoai' => $data['sodienthoai'],
            ]);

            return ['success' => true, 'message' => 'Cập nhật sinh viên thành công.'];
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => 'Có lỗi xảy ra: ' . $e->getMessage()];
        }
    }

    /**
     * Assign student to a room (or remove from room if phongId is null).
     * 
     * @param int $studentId
     * @param int|null $phoneId
     * @return array ['success' => bool, 'message' => string]
     */
    public function assignRoom(int $studentId, ?int $phoneId): array
    {
        try {
            return DB::transaction(function () use ($studentId, $phoneId) {
                $sinhvien = Sinhvien::where('id', $studentId)->lockForUpdate()->first();
                if (!$sinhvien) {
                    return ['success' => false, 'message' => 'Không tìm thấy sinh viên.'];
                }

                $oldRoomId = (int) ($sinhvien->phong_id ?? 0);

                // Allow removing student from room (phongId = null)
                if ($phoneId === null || (int) $phoneId === 0) {
                    Hopdong::where('sinhvien_id', $sinhvien->id)
                        ->where('trang_thai', ContractStatus::ACTIVE->value)
                        ->update(['trang_thai' => ContractStatus::TERMINATED->value]);

                    $sinhvien->update(['phong_id' => null]);
                    $this->syncOccupancy([$oldRoomId]);

                    return ['success' => true, 'message' => 'Đã cập nhật sinh viên về trạng thái không có phòng.'];
                }

                $phong = Phong::where('id', (int) $phoneId)->lockForUpdate()->first();
                if (!$phong) {
                    return ['success' => false, 'message' => 'Phòng không tồn tại.'];
                }

                // If already in same room, no action needed
                if ((int) $sinhvien->phong_id === (int) $phong->id) {
                    return ['success' => true, 'message' => 'Sinh viên đang ở đúng phòng này.'];
                }

                // Terminate active contracts when assigning new room
                Hopdong::where('sinhvien_id', $sinhvien->id)
                    ->where('trang_thai', ContractStatus::ACTIVE->value)
                    ->update(['trang_thai' => ContractStatus::TERMINATED->value]);

                // Lock students in target room to avoid race condition on capacity check
                $currentOccupancy = Sinhvien::where('phong_id', $phong->id)
                    ->lockForUpdate()
                    ->count();

                if ($currentOccupancy >= (int) $phong->succhuamax) {
                    return ['success' => false, 'message' => 'Phòng đã đầy, không thể chuyển.'];
                }

                $sinhvien->update(['phong_id' => $phong->id]);
                $this->syncOccupancy([$oldRoomId, (int) $phong->id]);

                return ['success' => true, 'message' => 'Chuyển phòng cho sinh viên thành công.'];
            });
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => 'Có lỗi xảy ra khi chuyển phòng: ' . $e->getMessage()];
        }
    }

    /**
     * Remove student from room (set phong_id to null and terminate contracts).
     * 
     * @param int $studentId
     * @return array ['success' => bool, 'message' => string]
     */
    public function removeFromRoom(int $studentId): array
    {
        try {
            return DB::transaction(function () use ($studentId) {
                $sinhvien = Sinhvien::where('id', $studentId)->lockForUpdate()->first();
                if (!$sinhvien) {
                    return ['success' => false, 'message' => 'Không tìm thấy sinh viên.'];
                }

                $oldRoomId = (int) ($sinhvien->phong_id ?? 0);

                Hopdong::where('sinhvien_id', $sinhvien->id)
                    ->where('trang_thai', ContractStatus::ACTIVE->value)
                    ->update(['trang_thai' => ContractStatus::TERMINATED->value]);

                $sinhvien->update([
                    'phong_id' => null,
                    'ngay_vao' => null,
                    'ngay_het_han' => null,
                ]);

                $this->syncOccupancy([$oldRoomId]);

                return ['success' => true, 'message' => 'Đã cho sinh viên rời phòng thành công.'];
            });
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => 'Có lỗi xảy ra khi cho rời phòng: ' . $e->getMessage()];
        }
    }

    /**
     * Synchronize occupancy count (dango) for given rooms.
     * 
     * @param array $roomIds
     * @return void
     */
    public function syncOccupancy(array $roomIds): void
    {
        $validRoomIds = array_unique(
            array_filter(
                array_map(static fn ($id) => (int) $id, $roomIds),
                static fn (int $id) => $id > 0
            )
        );

        foreach ($validRoomIds as $roomId) {
            $occupancy = Sinhvien::where('phong_id', $roomId)->count();
            Phong::where('id', $roomId)->update(['dango' => $occupancy]);
        }
    }
}

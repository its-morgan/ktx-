<?php

namespace App\Http\Controllers;

use App\Enums\ContractStatus;
use App\Models\Hopdong;
use App\Models\Phong;
use App\Models\Sinhvien;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SinhvienController extends Controller
{
    /**
     * List all students for admin.
     */
    public function listStudents(Request $request)
    {
        $tuKhoa = $request->query('q', '');

        $danhsachsinhvien = Sinhvien::when($tuKhoa, function ($query, $tuKhoa) {
            return $query->where('masinhvien', 'like', '%'.trim($tuKhoa).'%');
        })->get();

        $danhsachphong = Phong::all();

        return view('admin.sinhvien.danhsach', [
            'danhsachsinhvien' => $danhsachsinhvien,
            'danhsachphong' => $danhsachphong,
            'tuKhoa' => $tuKhoa,
        ]);
    }

    /**
     * Update student information (admin).
     */
    public function updateStudent(Request $request, int $id)
    {
        $sinhvien = Sinhvien::find($id);

        if (! $sinhvien) {
            return redirect()->back()->with('toast_loai', 'loi')->with('toast_noidung', 'Khong tim thay sinh vien.');
        }

        $dulieu = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'masinhvien' => ['required', 'string', 'max:20'],
            'lop' => ['required', 'string', 'max:50'],
            'sodienthoai' => ['required', 'string', 'max:15'],
            'gioitinh' => ['required', 'in:Nam,Nữ'],
        ]);

        $user = $sinhvien->taikhoan;
        if (! $user) {
            return redirect()->back()->with('toast_loai', 'loi')->with('toast_noidung', 'Khong tim thay tai khoan sinh vien.');
        }

        $user->update(['name' => $dulieu['name'], 'gioitinh' => $dulieu['gioitinh']]);

        $sinhvien->update([
            'masinhvien' => $dulieu['masinhvien'],
            'lop' => $dulieu['lop'],
            'sodienthoai' => $dulieu['sodienthoai'],
        ]);

        return redirect()->back()->with('toast_loai', 'thanhcong')->with('toast_noidung', 'Cap nhat sinh vien thanh cong.');
    }

    /**
     * Assign or change room for a student (admin).
     */
    public function assignRoom(Request $request, int $id)
    {
        $dulieu = $request->validate(
            [
                'phong_id' => ['nullable', 'numeric'],
            ],
            [
                'phong_id.numeric' => 'Phong khong hop le.',
            ]
        );

        $phongId = $dulieu['phong_id'] ?? null;

        try {
            return DB::transaction(function () use ($id, $phongId) {
                $sinhvien = Sinhvien::where('id', $id)->lockForUpdate()->first();
                if (! $sinhvien) {
                    return redirect()
                        ->back()
                        ->with('toast_loai', 'loi')
                        ->with('toast_noidung', 'Khong tim thay sinh vien.');
                }
                $phongCuId = (int) ($sinhvien->phong_id ?? 0);

                // Cho phep chon "khong co phong" (phong_id = null)
                if ($phongId === null || (int) $phongId === 0) {
                    Hopdong::where('sinhvien_id', $sinhvien->id)
                        ->where('trang_thai', ContractStatus::ACTIVE->value)
                        ->update(['trang_thai' => ContractStatus::TERMINATED->value]);

                    $sinhvien->update([
                        'phong_id' => null,
                    ]);
                    $this->syncOccupancy([$phongCuId]);

                    return redirect()
                        ->back()
                        ->with('toast_loai', 'thanhcong')
                        ->with('toast_noidung', 'Da cap nhat sinh vien ve trang thai chua co phong.');
                }

                $phong = Phong::where('id', (int) $phongId)->lockForUpdate()->first();
                if (! $phong) {
                    return redirect()
                        ->back()
                        ->with('toast_loai', 'loi')
                        ->with('toast_noidung', 'Phong khong ton tai.');
                }

                // Neu chuyen toi dung phong dang o thi khong lam gi
                if ((int) $sinhvien->phong_id === (int) $phong->id) {
                    return redirect()
                        ->back()
                        ->with('toast_loai', 'thanhcong')
                        ->with('toast_noidung', 'Sinh vien dang o dung phong nay.');
                }

                // Terminate active contracts when assigning new room
                Hopdong::where('sinhvien_id', $sinhvien->id)
                    ->where('trang_thai', ContractStatus::ACTIVE->value)
                    ->update(['trang_thai' => ContractStatus::TERMINATED->value]);

                // Khoa cac ban ghi sinh vien trong phong dich de tranh race condition khi dem suc chua
                $soluonghientai = Sinhvien::where('phong_id', $phong->id)
                    ->lockForUpdate()
                    ->count();

                if ($soluonghientai >= (int) $phong->succhuamax) {
                    return redirect()
                        ->back()
                        ->with('toast_loai', 'loi')
                        ->with('toast_noidung', 'Phong da du nguoi, khong the chuyen.');
                }

                $sinhvien->update([
                    'phong_id' => $phong->id,
                ]);
                $this->syncOccupancy([$phongCuId, (int) $phong->id]);

                return redirect()
                    ->back()
                    ->with('toast_loai', 'thanhcong')
                    ->with('toast_noidung', 'Chuyen phong cho sinh vien thanh cong.');
            });
        } catch (\Throwable $e) {
            return redirect()
                ->back()
                ->with('toast_loai', 'loi')
                ->with('toast_noidung', 'Co loi xay ra khi chuyen phong: '.$e->getMessage());
        }
    }

    /**
     * Remove student from room (set phong_id to null).
     */
    public function removeFromRoom(int $id)
    {
        try {
            return DB::transaction(function () use ($id) {
                $sinhvien = Sinhvien::where('id', $id)->lockForUpdate()->first();
                if (! $sinhvien) {
                    return redirect()
                        ->back()
                        ->with('toast_loai', 'loi')
                        ->with('toast_noidung', 'Khong tim thay sinh vien.');
                }
                $phongCuId = (int) ($sinhvien->phong_id ?? 0);

                Hopdong::where('sinhvien_id', $sinhvien->id)
                    ->where('trang_thai', ContractStatus::ACTIVE->value)
                    ->update(['trang_thai' => ContractStatus::TERMINATED->value]);

                $sinhvien->update([
                    'phong_id' => null,
                    'ngay_vao' => null,
                    'ngay_het_han' => null,
                ]);
                $this->syncOccupancy([$phongCuId]);

                return redirect()
                    ->back()
                    ->with('toast_loai', 'thanhcong')
                    ->with('toast_noidung', 'Da cho sinh vien roi phong thanh cong.');
            });
        } catch (\Throwable $e) {
            return redirect()
                ->back()
                ->with('toast_loai', 'loi')
                ->with('toast_noidung', 'Co loi xay ra khi cho roi phong: '.$e->getMessage());
        }
    }

    private function syncOccupancy(array $roomIds): void
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

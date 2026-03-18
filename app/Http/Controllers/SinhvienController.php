<?php

namespace App\Http\Controllers;

use App\Models\Phong;
use App\Models\Sinhvien;
use Illuminate\Http\Request;

class SinhvienController extends Controller
{
    /**
     * Hàm này hiển thị danh sách sinh viên cho admin.
     * - Danh sách sinh viên lấy từ: bảng sinhvien
     * - Danh sách phòng lấy từ: bảng phong (để hiển thị tên phòng)
     */
    public function danhsachsinhvien(Request $request)
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
     * Hàm này xử lý admin chuyển phòng cho sinh viên.
     * - $id lấy từ route (id của sinhvien)
     * - Dữ liệu phòng mới lấy từ form: phong_id
     * - Kiểm tra phòng còn chỗ dựa vào: bảng sinhvien (đếm theo phong_id) và phong.soluongtoida
     */
    public function chuyenphongsinhvien(Request $request, int $id)
    {
        $sinhvien = Sinhvien::find($id);

        if (! $sinhvien) {
            return redirect()
                ->back()
                ->with('toast_loai', 'loi')
                ->with('toast_noidung', 'Không tìm thấy sinh viên.');
        }

        $dulieu = $request->validate(
            [
                'phong_id' => ['nullable', 'numeric'],
            ],
            [
                'phong_id.numeric' => 'Phòng không hợp lệ.',
            ]
        );

        // Cho phép chọn "không có phòng" (phong_id = null)
        $phong_id = $dulieu['phong_id'] ?? null;

        if ($phong_id === null || (int) $phong_id === 0) {
            $sinhvien->update([
                'phong_id' => null,
            ]);

            return redirect()
                ->back()
                ->with('toast_loai', 'thanhcong')
                ->with('toast_noidung', 'Đã cập nhật sinh viên về trạng thái chưa có phòng.');
        }

        $phong = Phong::find((int) $phong_id);

        if (! $phong) {
            return redirect()
                ->back()
                ->with('toast_loai', 'loi')
                ->with('toast_noidung', 'Phòng không tồn tại.');
        }

        // Nếu chuyển tới đúng phòng đang ở thì không làm gì
        if ((int) $sinhvien->phong_id === (int) $phong->id) {
            return redirect()
                ->back()
                ->with('toast_loai', 'thanhcong')
                ->with('toast_noidung', 'Sinh viên đang ở đúng phòng này.');
        }

        // Đếm số sinh viên hiện tại trong phòng mới
        $soluonghientai = Sinhvien::where('phong_id', $phong->id)->count();

        if ($soluonghientai >= (int) $phong->soluongtoida) {
            return redirect()
                ->back()
                ->with('toast_loai', 'loi')
                ->with('toast_noidung', 'Phòng đã đủ người, không thể chuyển.');
        }

        $sinhvien->update([
            'phong_id' => $phong->id,
        ]);

        return redirect()
            ->back()
            ->with('toast_loai', 'thanhcong')
            ->with('toast_noidung', 'Chuyển phòng cho sinh viên thành công.');
    }

    /**
     * Ham nay xu ly admin cho sinh vien roi phong (set phong_id ve null).
     * - $id lay tu route (id cua sinhvien)
     */
    public function choroiophong(int $id)
    {
        $sinhvien = Sinhvien::find($id);

        if (! $sinhvien) {
            return redirect()
                ->back()
                ->with("toast_loai", "loi")
                ->with("toast_noidung", "Khong tim thay sinh vien.");
        }

        $sinhvien->update([
            "phong_id" => null,
        ]);

        return redirect()
            ->back()
            ->with("toast_loai", "thanhcong")
            ->with("toast_noidung", "Da cho sinh vien roi phong thanh cong.");
    }
}


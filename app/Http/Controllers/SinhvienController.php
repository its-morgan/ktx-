<?php

namespace App\Http\Controllers;

use App\Models\Hopdong;
use App\Models\Phong;
use App\Models\Sinhvien;
use Illuminate\Http\Request;

class SinhvienController extends Controller
{
    /**
     * Hàm này hiển thị danh sách sinh viên cho admin.
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
     * Cập nhật thông tin sinh viên (admin)
     */
    public function capnhatsinhvien(Request $request, int $id)
    {
        $sinhvien = Sinhvien::find($id);

        if (! $sinhvien) {
            return redirect()->back()->with('toast_loai', 'loi')->with('toast_noidung', 'Không tìm thấy sinh viên.');
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
            return redirect()->back()->with('toast_loai', 'loi')->with('toast_noidung', 'Không tìm thấy tài khoản sinh viên.');
        }

        $user->update(['name' => $dulieu['name'], 'gioitinh' => $dulieu['gioitinh']]);

        $sinhvien->update([
            'masinhvien' => $dulieu['masinhvien'],
            'lop' => $dulieu['lop'],
            'sodienthoai' => $dulieu['sodienthoai'],
        ]);

        return redirect()->back()->with('toast_loai', 'thanhcong')->with('toast_noidung', 'Cập nhật sinh viên thành công.');
    }

    /**
     * Hàm này xử lý admin chuyển phòng cho sinh viên.

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

        // Đóng hợp đồng cũ của sinh viên nếu đang hiệu lực
        Hopdong::where('sinhvien_id', $sinhvien->id)
            ->where('trang_thai', 'Đang hiệu lực')
            ->update(['trang_thai' => 'Đã thanh lý']);

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
     * Hàm này xử lý admin cho sinh viên rời phòng (set phong_id về null).
     * - $id lấy từ route (id của sinhvien)
     */
    public function choroiophong(int $id)
    {
        $sinhvien = Sinhvien::find($id);

        if (! $sinhvien) {
            return redirect()
                ->back()
                ->with("toast_loai", "loi")
                ->with("toast_noidung", "Không tìm thấy sinh viên.");
        }

        Hopdong::where('sinhvien_id', $sinhvien->id)
            ->where('trang_thai', 'Đang hiệu lực')
            ->update(['trang_thai' => 'Đã thanh lý']);

        $sinhvien->update([
            "phong_id" => null,
            'ngay_vao' => null,
            'ngay_het_han' => null,
        ]);

        return redirect()
            ->back()
            ->with("toast_loai", "thanhcong")
            ->with("toast_noidung", "Đã cho sinh viên rời phòng thành công.");
    }
}


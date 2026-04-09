<?php

namespace App\Http\Controllers;

use App\Enums\MaintenanceStatus;
use App\Models\Baohong;
use App\Models\Phong;
use App\Models\Sinhvien;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class BaohongController extends Controller
{

    /**
     * List maintenance requests for current student.
     */
    public function listMaintenanceRequests()
    {
        $sinhvien = Sinhvien::where('user_id', Auth::id())->first();

        if (! $sinhvien) {
            return view('student.baohong.danhsach', [
                'danhsachbaohong' => collect(),
            ]);
        }

        $danhsachbaohong = Baohong::where('sinhvien_id', $sinhvien->id)->get();

        return view('student.baohong.danhsach', [
            'danhsachbaohong' => $danhsachbaohong,
        ]);
    }

    /**
     * Create a new maintenance request from student.
     */
    public function storeMaintenance(Request $request)
    {
        $dulieu = $request->validate(
            [
                'mota' => ['required'],
                'noidung' => ['nullable', 'string'],
                'anhminhhoa' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            ],
            [
                'mota.required' => 'Mô tả lỗi không được để trống.',
                'anhminhhoa.image' => 'Tệp đính kèm phải là hình ảnh.',
                'anhminhhoa.mimes' => 'Ảnh chỉ chấp nhận định dạng jpg, jpeg, png, webp.',
                'anhminhhoa.max' => 'Ảnh tối đa 4MB.',
            ]
        );

        // Lấy sinh viên đang đăng nhập từ bảng sinhvien (user_id)
        $sinhvien = Sinhvien::where('user_id', Auth::id())->first();

        if (! $sinhvien) {
            return redirect()
                ->back()
                ->with('toast_loai', 'loi')
                ->with('toast_noidung', 'Không tìm thấy thông tin sinh viên.');
        }

        // Nếu chưa có phòng thì không cho báo hỏng
        if (! $sinhvien->phong_id) {
            return redirect()
                ->back()
                ->with('toast_loai', 'loi')
                ->with('toast_noidung', 'Bạn chưa được xếp phòng nên chưa thể gửi báo hỏng.');
        }

        // Xử lý lưu ảnh vào public/anhbaohong
        $duongdananh = null;
        if ($request->hasFile('anhminhhoa')) {
            $thumuc = public_path('anhbaohong');
            File::ensureDirectoryExists($thumuc);

            $tenfile = time().'_'.$request->file('anhminhhoa')->getClientOriginalName();
            $request->file('anhminhhoa')->move($thumuc, $tenfile);
            $duongdananh = 'anhbaohong/'.$tenfile;
        }

        Baohong::create([
            'sinhvien_id' => $sinhvien->id,
            'phong_id' => (int) $sinhvien->phong_id,
            'mota' => $dulieu['mota'],
            'noidung' => $dulieu['noidung'] ?? null,
            'anhminhhoa' => $duongdananh,
            'trangthai' => MaintenanceStatus::PENDING->value,
        ]);

        return redirect()
            ->back()
            ->with('toast_loai', 'thanhcong')
            ->with('toast_noidung', 'Gửi báo hỏng thành công.');
    }

    /**
     * List all maintenance requests for admin.
     */
    public function listMaintenanceRequestsAdmin(Request $request)
    {
        $status = $request->query('status', '');

        $danhsachbaohong = Baohong::when($status && $status !== 'Tất cả', function ($query) use ($status) {
            return $query->where('trangthai', $status);
        })->get();

        $danhsachphong = Phong::all();
        $danhsachsinhvien = Sinhvien::all();

        return view('admin.baohong.danhsach', [
            'danhsachbaohong' => $danhsachbaohong,
            'danhsachphong' => $danhsachphong,
            'danhsachsinhvien' => $danhsachsinhvien,
            'status' => $status,
        ]);
    }

    /**
     * Update maintenance request status (admin).
     */
    public function updateMaintenance(Request $request, int $id)
    {
        $baohong = Baohong::find($id);

        if (! $baohong) {
            return redirect()
                ->back()
                ->with('toast_loai', 'loi')
                ->with('toast_noidung', 'Không tìm thấy báo hỏng.');
        }

        $dulieu = $request->validate(
            [
                'trangthai' => ['required', 'in:' . implode(',', [
                    MaintenanceStatus::PENDING->value,
                    MaintenanceStatus::SCHEDULED->value,
                    MaintenanceStatus::IN_PROGRESS->value,
                    MaintenanceStatus::COMPLETED->value,
                ])],
                'ngayhen' => ['nullable', 'date'],
                'noidung' => ['nullable', 'string'],
            ],
            [
                'trangthai.required' => 'Trang thai khong duoc de trong.',
                'trangthai.in' => 'Trang thai khong hop le.',
                'ngayhen.date' => 'Ngay hen phai la dinh dang ngay hop le.',
            ]
        );

        $baohong->update([
            'trangthai' => $dulieu['trangthai'],
            'ngayhen' => $dulieu['ngayhen'] ?? $baohong->ngayhen,
            'noidung' => $dulieu['noidung'] ?? $baohong->noidung,
        ]);

        // Tao thong bao cho sinh vien khi admin cap nhat ngay hen
        if ($dulieu['ngayhen']) {
            \App\Models\Thongbao::create([
                'tieude' => 'Cap nhat lich hen sua chua',
                'noidung' => 'Yeu cau bao hong cua ban da duoc hen ngay sua chua: ' . date('d/m/Y', strtotime($dulieu['ngayhen'])) . '. Noi dung: ' . ($dulieu['noidung'] ?? 'Khong co'),
                'doituong' => 'sinhvien',
                'sinhvien_id' => (int) $baohong->sinhvien_id,
                'phong_id' => (int) $baohong->phong_id,
                'ngaydang' => now(),
            ]);
        }

        return redirect()
            ->back()
            ->with('toast_loai', 'thanhcong')
            ->with('toast_noidung', 'Cap nhat trang thai bao hong thanh cong.');
    }
}

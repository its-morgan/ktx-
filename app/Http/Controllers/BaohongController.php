<?php

namespace App\Http\Controllers;

use App\Models\Baohong;
use App\Models\Phong;
use App\Models\Sinhvien;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class BaohongController extends Controller
{
    /**
     * Trạng thái báo hỏng: chờ sửa.
     */
    private const TRANGTHAI_CHOSUA = 'Chờ sửa';

    /**
     * Trạng thái báo hỏng: đã xong.
     */
    private const TRANGTHAI_DAXONG = 'Đã xong';

    /**
     * Hàm này hiển thị danh sách báo hỏng của sinh viên đang đăng nhập.
     * - Sinh viên hiện tại lấy từ: bảng sinhvien (lọc theo user_id)
     * - Báo hỏng lấy từ: bảng baohong (lọc theo sinhvien_id)
     */
    public function danhsachbaohong()
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
     * Hàm này xử lý sinh viên gửi yêu cầu báo hỏng (có thể đính kèm ảnh).
     * - Nội dung mô tả lấy từ form: mota
     * - Ảnh minh họa lấy từ form: anhminhhoa (lưu vào public/anhbaohong)
     */
    public function thembaohong(Request $request)
    {
        $dulieu = $request->validate(
            [
                'mota' => ['required'],
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
            'anhminhhoa' => $duongdananh,
            'trangthai' => self::TRANGTHAI_CHOSUA,
        ]);

        return redirect()
            ->back()
            ->with('toast_loai', 'thanhcong')
            ->with('toast_noidung', 'Gửi báo hỏng thành công.');
    }

    /**
     * Hàm này hiển thị danh sách báo hỏng cho admin.
     * - Danh sách báo hỏng lấy từ: bảng baohong
     * - Danh sách phòng/sinh viên lấy từ: bảng phong, sinhvien (để hiển thị)
     */
    public function danhsachbaohongquantri(Request $request)
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
     * Hàm này xử lý admin cập nhật trạng thái báo hỏng.
     * - $id lấy từ route (id của baohong)
     * - Trạng thái lấy từ form: trangthai ("Chờ sửa"|"Đã xong")
     */
    public function capnhatbaohong(Request $request, int $id)
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
                'trangthai' => ['required', 'in:Chờ sửa,Đã xong'],
            ],
            [
                'trangthai.required' => 'Trạng thái không được để trống.',
                'trangthai.in' => 'Trạng thái không hợp lệ.',
            ]
        );

        $baohong->update([
            'trangthai' => $dulieu['trangthai'],
        ]);

        return redirect()
            ->back()
            ->with('toast_loai', 'thanhcong')
            ->with('toast_noidung', 'Cập nhật trạng thái báo hỏng thành công.');
    }
}

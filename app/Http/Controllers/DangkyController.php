<?php

namespace App\Http\Controllers;

use App\Models\Dangky;
use App\Models\Phong;
use App\Models\Sinhvien;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DangkyController extends Controller
{
    /**
     * Trạng thái đăng ký: chờ xử lý.
     */
    private const TRANGTHAI_CHOXULY = 'Chờ xử lý';

    /**
     * Trạng thái đăng ký: đã duyệt.
     */
    private const TRANGTHAI_DADUYET = 'Đã duyệt';

    /**
     * Trạng thái đăng ký: từ chối.
     */
    private const TRANGTHAI_TUCHOI = 'Từ chối';

    /**
     * Hàm này xử lý sinh viên gửi đăng ký phòng.
     * - Sinh viên hiện tại lấy từ: bảng sinhvien (lọc theo user_id)
     * - Dữ liệu phòng lấy từ: form (phong_id)
     */
    public function themdangky(Request $request)
    {
        $dulieu = $request->validate(
            [
                'phong_id' => ['required', 'numeric'],
            ],
            [
                'phong_id.required' => 'Bạn chưa chọn phòng.',
                'phong_id.numeric' => 'Phòng không hợp lệ.',
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

        // Nếu sinh viên đã có phòng rồi thì không cho đăng ký nữa
        if ($sinhvien->phong_id) {
            return redirect()
                ->back()
                ->with('toast_loai', 'loi')
                ->with('toast_noidung', 'Bạn đã được xếp phòng, không thể đăng ký thêm.');
        }

        // Kiểm tra phòng có tồn tại không
        $phong = Phong::find((int) $dulieu['phong_id']);

        if (! $phong) {
            return redirect()
                ->back()
                ->with('toast_loai', 'loi')
                ->with('toast_noidung', 'Phòng không tồn tại.');
        }

        // Kiểm tra phòng còn chỗ không (đếm số sinh viên đang ở phòng)
        $soluonghientai = Sinhvien::where('phong_id', $phong->id)->count();
        if ($soluonghientai >= (int) $phong->soluongtoida) {
            return redirect()
                ->back()
                ->with('toast_loai', 'loi')
                ->with('toast_noidung', 'Phòng này đã đủ người.');
        }

        // Kiểm tra sinh viên đã có đăng ký đang chờ xử lý chưa
        $dangkychoduyet = Dangky::where('sinhvien_id', $sinhvien->id)
            ->where('trangthai', self::TRANGTHAI_CHOXULY)
            ->first();

        if ($dangkychoduyet) {
            return redirect()
                ->back()
                ->with('toast_loai', 'loi')
                ->with('toast_noidung', 'Bạn đã gửi đăng ký, vui lòng chờ admin xử lý.');
        }

        Dangky::create([
            'sinhvien_id' => $sinhvien->id,
            'phong_id' => $phong->id,
            'trangthai' => self::TRANGTHAI_CHOXULY,
            'ghichu' => null,
        ]);

        return redirect()
            ->back()
            ->with('toast_loai', 'thanhcong')
            ->with('toast_noidung', 'Gửi đăng ký phòng thành công. Vui lòng chờ admin duyệt.');
    }

    /**
     * Hàm này hiển thị danh sách đăng ký cho admin.
     * - Danh sách đăng ký lấy từ: bảng dangky
     * - Danh sách phòng/sinh viên lấy từ: bảng phong, sinhvien (để hiển thị)
     */
    public function danhsachdangky()
    {
        $danhsachdangky = Dangky::all();
        $danhsachphong = Phong::all();
        $danhsachsinhvien = Sinhvien::all();

        return view('admin.dangky.danhsach', [
            'danhsachdangky' => $danhsachdangky,
            'danhsachphong' => $danhsachphong,
            'danhsachsinhvien' => $danhsachsinhvien,
        ]);
    }

    /**
     * Hàm này xử lý admin duyệt đăng ký.
     * - $id lấy từ route (id của dangky)
     * - Khi duyệt: cập nhật dangky.trangthai = "Đã duyệt", và cập nhật sinhvien.phong_id
     */
    public function duyetdangky(int $id)
    {
        $dangky = Dangky::find($id);

        if (! $dangky) {
            return redirect()
                ->back()
                ->with('toast_loai', 'loi')
                ->with('toast_noidung', 'Không tìm thấy đăng ký.');
        }

        if ($dangky->trangthai !== self::TRANGTHAI_CHOXULY) {
            return redirect()
                ->back()
                ->with('toast_loai', 'loi')
                ->with('toast_noidung', 'Đăng ký này đã được xử lý trước đó.');
        }

        $sinhvien = Sinhvien::find((int) $dangky->sinhvien_id);
        $phong = Phong::find((int) $dangky->phong_id);

        if (! $sinhvien || ! $phong) {
            return redirect()
                ->back()
                ->with('toast_loai', 'loi')
                ->with('toast_noidung', 'Thiếu dữ liệu sinh viên hoặc phòng.');
        }

        // Nếu sinh viên đã có phòng thì không duyệt nữa
        if ($sinhvien->phong_id) {
            return redirect()
                ->back()
                ->with('toast_loai', 'loi')
                ->with('toast_noidung', 'Sinh viên đã có phòng, không thể duyệt.');
        }

        // Kiểm tra phòng còn chỗ không
        $soluonghientai = Sinhvien::where('phong_id', $phong->id)->count();
        if ($soluonghientai >= (int) $phong->soluongtoida) {
            return redirect()
                ->back()
                ->with('toast_loai', 'loi')
                ->with('toast_noidung', 'Phòng đã đủ người, không thể duyệt.');
        }

        $dangky->update([
            'trangthai' => self::TRANGTHAI_DADUYET,
            'ghichu' => null,
        ]);

        $sinhvien->update([
            'phong_id' => $phong->id,
        ]);

        return redirect()
            ->back()
            ->with('toast_loai', 'thanhcong')
            ->with('toast_noidung', 'Duyệt đăng ký thành công và đã cập nhật sinh viên vào phòng.');
    }

    /**
     * Hàm này xử lý admin từ chối đăng ký.
     * - $id lấy từ route (id của dangky)
     * - $request->ghichu lấy từ form (lý do từ chối)
     */
    public function tuchoidangky(Request $request, int $id)
    {
        $dangky = Dangky::find($id);

        if (! $dangky) {
            return redirect()
                ->back()
                ->with('toast_loai', 'loi')
                ->with('toast_noidung', 'Không tìm thấy đăng ký.');
        }

        $dulieu = $request->validate(
            [
                'ghichu' => ['nullable'],
            ],
            []
        );

        $dangky->update([
            'trangthai' => self::TRANGTHAI_TUCHOI,
            'ghichu' => $dulieu['ghichu'] ?? null,
        ]);

        return redirect()
            ->back()
            ->with('toast_loai', 'thanhcong')
            ->with('toast_noidung', 'Từ chối đăng ký thành công.');
    }
}

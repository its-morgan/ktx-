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
            'loaidangky' => 'Thuê phòng',
            'trangthai' => self::TRANGTHAI_CHOXULY,
            'ghichu' => null,
        ]);

        return redirect()
            ->back()
            ->with('toast_loai', 'thanhcong')
            ->with('toast_noidung', 'Gửi đăng ký phòng thành công. Vui lòng chờ admin duyệt.');
    }

    /**
     * Hàm này xử lý sinh viên yêu cầu trả phòng.
     * - Tạo bản ghi dangky.loaidangky = 'Trả phòng'
     * - Gán trạng thái là chờ xử lý để admin duyệt.
     */
    public function yeucautraphong()
    {
        $sinhvien = Sinhvien::where('user_id', Auth::id())->first();

        if (! $sinhvien || ! $sinhvien->phong_id) {
            return redirect()
                ->back()
                ->with('toast_loai', 'loi')
                ->with('toast_noidung', 'Bạn hiện không có phòng để trả.');
        }

        $dangkychoduyet = Dangky::where('sinhvien_id', $sinhvien->id)
            ->where('trangthai', self::TRANGTHAI_CHOXULY)
            ->where('loaidangky', 'Trả phòng')
            ->first();

        if ($dangkychoduyet) {
            return redirect()
                ->back()
                ->with('toast_loai', 'loi')
                ->with('toast_noidung', 'Bạn đã gửi yêu cầu trả phòng, vui lòng chờ admin xử lý.');
        }

        Dangky::create([
            'sinhvien_id' => $sinhvien->id,
            'phong_id' => $sinhvien->phong_id,
            'loaidangky' => 'Trả phòng',
            'trangthai' => self::TRANGTHAI_CHOXULY,
            'ghichu' => null,
        ]);

        return redirect()
            ->back()
            ->with('toast_loai', 'thanhcong')
            ->with('toast_noidung', 'Gửi yêu cầu trả phòng thành công.');
    }

    /**
     * Hàm này hiển thị danh sách đăng ký cho admin.
     * - Danh sách đăng ký lấy từ: bảng dangky
     * - Danh sách phòng/sinh viên lấy từ: bảng phong, sinhvien (để hiển thị)
     */
    public function danhsachdangky(Request $request)
    {
        $status = $request->query('status', '');

        $danhsachdangky = Dangky::when($status && $status !== 'Tất cả', function ($query) use ($status) {
            return $query->where('trangthai', $status);
        })->get();

        $danhsachphong = Phong::all();
        $danhsachsinhvien = Sinhvien::all();

        return view('admin.dangky.danhsach', [
            'danhsachdangky' => $danhsachdangky,
            'danhsachphong' => $danhsachphong,
            'danhsachsinhvien' => $danhsachsinhvien,
            'status' => $status,
        ]);
    }

    /**
     * Hàm này xử lý admin duyệt đăng ký.
     * - $id lấy từ route (id của dangky)
     * - Khi duyệt: cập nhật dangky.trangthai = "Đã duyệt", và cập nhật sinhvien.phong_id
     */
    public function duyetdangky(Request $request, int $id)
    {
        $dulieu = $request->validate([
            'ngay_het_han' => ['nullable', 'date', 'after_or_equal:today'],
        ]);

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

        // Nếu đăng ký là Thuê phòng và sinh viên đã có phòng thì không duyệt
        if ($dangky->loaidangky === 'Thuê phòng' && $sinhvien->phong_id) {
            return redirect()
                ->back()
                ->with('toast_loai', 'loi')
                ->with('toast_noidung', 'Sinh viên đã có phòng, không thể duyệt.');
        }

        // Kiểm tra phòng còn chỗ không chỉ áp dụng cho Thuê phòng
        if ($dangky->loaidangky === 'Thuê phòng') {
            $soluonghientai = Sinhvien::where('phong_id', $phong->id)->count();
            if ($soluonghientai >= (int) $phong->soluongtoida) {
                return redirect()
                    ->back()
                    ->with('toast_loai', 'loi')
                    ->with('toast_noidung', 'Phòng đã đủ người, không thể duyệt.');
            }
        }

        $dangky->update([
            'trangthai' => self::TRANGTHAI_DADUYET,
            'ghichu' => null,
        ]);

        if ($dangky->loaidangky === 'Trả phòng') {
            // Khi duyệt trả phòng thì bỏ phong_id của sinh viên
            $sinhvien->update(['phong_id' => null]);

            // Edge case: tính tiền thừa/thiếu theo ngày trong tháng
            $ngayhientai = (int) now()->format('d');
            $ngaytrongthang = (int) now()->daysInMonth;
            $tyle = max(0, 1 - ($ngayhientai / $ngaytrongthang));
            $tienphong = (int) $phong->giaphong;
            $sotienhoan = round($tienphong * $tyle);
            $sotienhoan = max(0, $sotienhoan);

            $thongbao = 'Duyệt yêu cầu trả phòng thành công. Đã giải phóng phòng.';
            $thongbao .= ' Số tiền hoàn lại dự kiến: ' . number_format($sotienhoan) . ' đ (' . round($tyle*100) . '%).';

            return redirect()
                ->back()
                ->with('toast_loai', 'thanhcong')
                ->with('toast_noidung', $thongbao);
        }

        // Nếu là đăng ký thuê phòng thì gán phòng cho sinh viên và tạo hợp đồng
        $ngayBatDau = now()->format('Y-m-d');
        $ngayKetThuc = $dulieu['ngay_het_han'] ?? now()->addMonths(5)->format('Y-m-d');

        if (strtotime($ngayKetThuc) <= strtotime($ngayBatDau)) {
            return redirect()->back()->with('toast_loai', 'loi')->with('toast_noidung', 'Ngày kết thúc phải sau ngày bắt đầu.');
        }

        $sinhvien->update([
            'phong_id' => $phong->id,
            'ngay_vao' => $ngayBatDau,
            'ngay_het_han' => $ngayKetThuc,
        ]);

        \App\Models\Hopdong::create([
            'sinhvien_id' => $sinhvien->id,
            'phong_id' => $phong->id,
            'ngay_bat_dau' => $ngayBatDau,
            'ngay_ket_thuc' => $ngayKetThuc,
            'giaphong_luc_ky' => (int) $phong->giaphong,
            'trang_thai' => 'Đang hiệu lực',
            'ghichu' => null,
        ]);

        return redirect()
            ->back()
            ->with('toast_loai', 'thanhcong')
            ->with('toast_noidung', 'Duyệt đăng ký thành công, tạo hợp đồng mới và cập nhật sinh viên vào phòng.');
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

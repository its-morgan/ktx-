<?php

namespace App\Http\Controllers;

use App\Models\Hoadon;
use App\Models\Phong;
use App\Models\Sinhvien;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HoadonController extends Controller
{
    /**
     * Trạng thái thanh toán: chưa thanh toán.
     */
    private const TRANGTHAI_CHUATHANHTOAN = 'Chưa thanh toán';

    /**
     * Trạng thái thanh toán: đã thanh toán.
     */
    private const TRANGTHAI_DATHANHTOAN = 'Đã thanh toán';

    /**
     * Đơn giá điện (VND / số) để demo.
     */
    private const DONGIADIEN = 3500;

    /**
     * Đơn giá nước (VND / số) để demo.
     */
    private const DONGIANUOC = 15000;

    /**
     * Hàm này hiển thị danh sách hóa đơn cho admin.
     * - Danh sách hóa đơn lấy từ: bảng hoadon
     * - Danh sách phòng lấy từ: bảng phong
     */
    public function danhsachhoadonquantri()
    {
        $danhsachhoadon = Hoadon::all();
        $danhsachphong = Phong::all();

        return view('admin.hoadon.danhsach', [
            'danhsachhoadon' => $danhsachhoadon,
            'danhsachphong' => $danhsachphong,
            'dongiadien' => self::DONGIADIEN,
            'dongianuoc' => self::DONGIANUOC,
        ]);
    }

    /**
     * Hàm này xử lý tạo/cập nhật hóa đơn khi admin nhập chỉ số điện nước.
     * - Dữ liệu lấy từ form: phong_id, thang, nam, chisodiencu, chisodienmoi, chisonuoccu, chisonuocmoi
     * - Tiền được tính theo công thức:
     *   tongtien = giaphong + (chisodienmoi - chisodiencu)*DONGIADIEN + (chisonuocmoi - chisonuoccu)*DONGIANUOC
     */
    public function xulyhoadon(Request $request)
    {
        $dulieu = $request->validate(
            [
                'phong_id' => ['required', 'numeric'],
                'thang' => ['required', 'numeric', 'min:1', 'max:12'],
                'nam' => ['required', 'numeric', 'min:2000', 'max:2100'],
                'chisodiencu' => ['required', 'numeric', 'min:0'],
                'chisodienmoi' => ['required', 'numeric', 'min:0'],
                'chisonuoccu' => ['required', 'numeric', 'min:0'],
                'chisonuocmoi' => ['required', 'numeric', 'min:0'],
            ],
            [
                'phong_id.required' => 'Bạn chưa chọn phòng.',
                'thang.required' => 'Tháng không được để trống.',
                'nam.required' => 'Năm không được để trống.',
                'chisodiencu.required' => 'Chỉ số điện cũ không được để trống.',
                'chisodienmoi.required' => 'Chỉ số điện mới không được để trống.',
                'chisonuoccu.required' => 'Chỉ số nước cũ không được để trống.',
                'chisonuocmoi.required' => 'Chỉ số nước mới không được để trống.',
                'chisodiencu.numeric' => 'Chỉ số điện cũ phải là số.',
                'chisodienmoi.numeric' => 'Chỉ số điện mới phải là số.',
                'chisonuoccu.numeric' => 'Chỉ số nước cũ phải là số.',
                'chisonuocmoi.numeric' => 'Chỉ số nước mới phải là số.',
            ]
        );

        // Kiểm tra chỉ số mới phải >= chỉ số cũ
        if ((int) $dulieu['chisodienmoi'] < (int) $dulieu['chisodiencu']) {
            return redirect()
                ->back()
                ->withErrors(['chisodienmoi' => 'Chỉ số điện mới phải lớn hơn hoặc bằng chỉ số điện cũ.'])
                ->withInput();
        }

        if ((int) $dulieu['chisonuocmoi'] < (int) $dulieu['chisonuoccu']) {
            return redirect()
                ->back()
                ->withErrors(['chisonuocmoi' => 'Chỉ số nước mới phải lớn hơn hoặc bằng chỉ số nước cũ.'])
                ->withInput();
        }

        // Lấy phòng từ bảng phong để lấy giaphong
        $phong = Phong::find((int) $dulieu['phong_id']);
        if (! $phong) {
            return redirect()
                ->back()
                ->with('toast_loai', 'loi')
                ->with('toast_noidung', 'Phòng không tồn tại.');
        }

        // Tính tiền điện nước theo công thức đã chọn
        $tiendien = ((int) $dulieu['chisodienmoi'] - (int) $dulieu['chisodiencu']) * self::DONGIADIEN;
        $tiennuoc = ((int) $dulieu['chisonuocmoi'] - (int) $dulieu['chisonuoccu']) * self::DONGIANUOC;
        $tongtien = ((int) $phong->giaphong) + $tiendien + $tiennuoc;

        // Tìm hóa đơn theo phòng + tháng + năm (nếu có thì update, chưa có thì create)
        $hoadoncu = Hoadon::where('phong_id', (int) $dulieu['phong_id'])
            ->where('thang', (int) $dulieu['thang'])
            ->where('nam', (int) $dulieu['nam'])
            ->first();

        if ($hoadoncu) {
            $hoadoncu->update([
                'chisodiencu' => (int) $dulieu['chisodiencu'],
                'chisodienmoi' => (int) $dulieu['chisodienmoi'],
                'chisonuoccu' => (int) $dulieu['chisonuoccu'],
                'chisonuocmoi' => (int) $dulieu['chisonuocmoi'],
                'tongtien' => $tongtien,
            ]);
        } else {
            Hoadon::create([
                'phong_id' => (int) $dulieu['phong_id'],
                'thang' => (int) $dulieu['thang'],
                'nam' => (int) $dulieu['nam'],
                'chisodiencu' => (int) $dulieu['chisodiencu'],
                'chisodienmoi' => (int) $dulieu['chisodienmoi'],
                'chisonuoccu' => (int) $dulieu['chisonuoccu'],
                'chisonuocmoi' => (int) $dulieu['chisonuocmoi'],
                'tongtien' => $tongtien,
                'trangthaithanhtoan' => self::TRANGTHAI_CHUATHANHTOAN,
            ]);
        }

        return redirect()
            ->back()
            ->with('toast_loai', 'thanhcong')
            ->with('toast_noidung', 'Cập nhật hóa đơn thành công.');
    }

    /**
     * Hàm này hiển thị danh sách hóa đơn của sinh viên đang đăng nhập.
     * - Sinh viên hiện tại lấy từ: bảng sinhvien (lọc theo user_id)
     * - Hóa đơn lấy từ: bảng hoadon (lọc theo phong_id)
     */
    public function hoadoncuatoi()
    {
        $sinhvien = Sinhvien::where('user_id', Auth::id())->first();

        if (! $sinhvien || ! $sinhvien->phong_id) {
            return view('student.hoadon.danhsach', [
                'danhsachhoadon' => collect(),
            ]);
        }

        $danhsachhoadon = Hoadon::where('phong_id', (int) $sinhvien->phong_id)->get();

        return view('student.hoadon.danhsach', [
            'danhsachhoadon' => $danhsachhoadon,
        ]);
    }

    /**
     * Hàm này xử lý admin xác nhận đã thanh toán cho hóa đơn.
     * - $id lấy từ route (id của hoadon)
     */
    public function xacnhanthanhtoan(int $id)
    {
        $hoadon = Hoadon::find($id);

        if (! $hoadon) {
            return redirect()
                ->back()
                ->with('toast_loai', 'loi')
                ->with('toast_noidung', 'Không tìm thấy hóa đơn.');
        }

        $hoadon->update([
            'trangthaithanhtoan' => self::TRANGTHAI_DATHANHTOAN,
        ]);

        return redirect()
            ->back()
            ->with('toast_loai', 'thanhcong')
            ->with('toast_noidung', 'Xác nhận thanh toán thành công.');
    }
}

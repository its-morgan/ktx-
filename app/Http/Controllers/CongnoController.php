<?php

namespace App\Http\Controllers;

use App\Models\Hoadon;
use App\Models\Phong;
use App\Models\Sinhvien;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CongnoController extends Controller
{
    /**
     * Trạng thái thanh toán: chưa thanh toán.
     */
    private const TRANGTHAI_CHUATHANHTOAN = 'Chưa thanh toán';

    /**
     * Số ngày quá hạn để tính công nợ.
     */
    private const NGAY_QUAHAN = 5;

    /**
     * Display arrearage (debt) report.
     * - List students with unpaid invoices overdue more than 5 days.
     */
    public function showArrearage(Request $request)
    {
        $ngayQuaHan = now()->subDays(self::NGAY_QUAHAN)->format('Y-m-d');

        // Lấy danh sách hóa đơn quá hạn
        $hoadonQuaHan = Hoadon::where('trangthaithanhtoan', self::TRANGTHAI_CHUATHANHTOAN)
            ->whereNotNull('ngayxuat')
            ->whereDate('ngayxuat', '<=', $ngayQuaHan)
            ->with(['phong.danhsachsinhvien'])
            ->get();

        // Tính tổng công nợ theo phòng và sinh viên
        $congnoTheoPhong = [];
        $tongCongNo = 0;

        foreach ($hoadonQuaHan as $hoadon) {
            $phongId = $hoadon->phong_id;

            if (!isset($congnoTheoPhong[$phongId])) {
                $phong = $hoadon->phong;
                $congnoTheoPhong[$phongId] = [
                    'phong' => $phong,
                    'sinhvien' => $phong ? $phong->danhsachsinhvien : collect(),
                    'hoadon' => [],
                    'tongtien' => 0,
                ];
            }

            $congnoTheoPhong[$phongId]['hoadon'][] = $hoadon;
            $congnoTheoPhong[$phongId]['tongtien'] += $hoadon->tongtien;
            $tongCongNo += $hoadon->tongtien;
        }

        // Thống kê tổng quan
        $thongke = [
            'tong_phong_no' => count($congnoTheoPhong),
            'tong_sinh_vien_no' => collect($congnoTheoPhong)->pluck('sinhvien')->flatten()->count(),
            'tong_tien_no' => $tongCongNo,
            'so_hoa_don_qua_han' => $hoadonQuaHan->count(),
        ];

        return view('admin.congno.danhsach', [
            'congnoTheoPhong' => $congnoTheoPhong,
            'thongke' => $thongke,
            'ngayQuaHan' => self::NGAY_QUAHAN,
        ]);
    }

    /**
     * Send reminder notification to students with debt.
     */
    public function sendReminderNotification(Request $request, int $phongId)
    {
        $phong = Phong::find($phongId);

        if (! $phong) {
            return redirect()
                ->back()
                ->with('toast_loai', 'loi')
                ->with('toast_noidung', 'Không tìm thấy phòng.');
        }

        $ngayQuaHan = now()->subDays(self::NGAY_QUAHAN)->format('Y-m-d');

        $hoadonQuaHan = Hoadon::where('phong_id', $phongId)
            ->where('trangthaithanhtoan', self::TRANGTHAI_CHUATHANHTOAN)
            ->whereNotNull('ngayxuat')
            ->whereDate('ngayxuat', '<=', $ngayQuaHan)
            ->get();

        if ($hoadonQuaHan->isEmpty()) {
            return redirect()
                ->back()
                ->with('toast_loai', 'thanhcong')
                ->with('toast_noidung', 'Phòng này không có công nợ quá hạn.');
        }

        $tongNo = $hoadonQuaHan->sum('tongtien');

        // Tạo thông báo cho sinh viên trong phòng
        \App\Models\Thongbao::create([
            'tieude' => 'Nhắc nhở thanh toán tiền phòng/điện nước',
            'noidung' => "Phòng {$phong->tenphong} có hóa đơn chưa thanh toán tổng số tiền " . number_format($tongNo) . " đ. Vui lòng thanh toán trong vòng 3 ngày để tránh bị phạt hoặc cắt điện nước.",
            'doituong' => 'sinhvien',
            'phong_id' => (int) $phong->id,
            'sinhvien_id' => null,
            'ngaydang' => now(),
        ]);

        return redirect()
            ->back()
            ->with('toast_loai', 'thanhcong')
            ->with('toast_noidung', "Đã gửi thông báo nhắc nhở cho phòng {$phong->tenphong}.");
    }
}

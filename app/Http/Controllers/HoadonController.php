<?php

namespace App\Http\Controllers;

use App\Models\Hoadon;
use App\Models\Phong;
use App\Models\Sinhvien;
use App\Services\InvoiceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HoadonController extends Controller
{
    public function __construct(private InvoiceService $invoiceService)
    {
    }

    private const TRANGTHAI_CHUATHANHTOAN = Hoadon::TRANGTHAI_CHUA_THANH_TOAN;
    private const TRANGTHAI_DATHANHTOAN = Hoadon::TRANGTHAI_DA_THANH_TOAN;

    public function listInvoicesAdmin()
    {
        $danhsachhoadon = Hoadon::all();
        $danhsachphong = Phong::all();
        $bangGia = $this->invoiceService->layBangGia();

        return view('admin.hoadon.danhsach', [
            'danhsachhoadon' => $danhsachhoadon,
            'danhsachphong' => $danhsachphong,
            'dongiadien' => $bangGia['dongiadien'],
            'dongianuoc' => $bangGia['dongianuoc'],
        ]);
    }

    public function processInvoices(Request $request)
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
                'phong_id.required' => 'Ban chua chon phong.',
                'thang.required' => 'Thang khong duoc de trong.',
                'nam.required' => 'Nam khong duoc de trong.',
                'chisodiencu.required' => 'Chi so dien cu khong duoc de trong.',
                'chisodienmoi.required' => 'Chi so dien moi khong duoc de trong.',
                'chisonuoccu.required' => 'Chi so nuoc cu khong duoc de trong.',
                'chisonuocmoi.required' => 'Chi so nuoc moi khong duoc de trong.',
            ]
        );

        $ketQua = $this->invoiceService->xuLyHoaDon($dulieu);

        $phanHoi = redirect()
            ->back()
            ->with('toast_loai', $ketQua['toast_loai'])
            ->with('toast_noidung', $ketQua['toast_noidung']);

        if (! empty($ketQua['loi'])) {
            $phanHoi = $phanHoi->withErrors($ketQua['loi']);
        }

        if (! empty($ketQua['giu_input'])) {
            $phanHoi = $phanHoi->withInput();
        }

        return $phanHoi;
    }

    public function myInvoices()
    {
        $sinhvien = Sinhvien::where('user_id', Auth::id())->first();
        if (! $sinhvien || ! $sinhvien->phong_id) {
            return redirect()->back()->with('toast_loai', 'loi')->with('toast_noidung', 'Ban chua co phong.');
        }

        $lichSuHoaDon = Hoadon::where('phong_id', (int) $sinhvien->phong_id)
            ->orderByDesc('nam')
            ->orderByDesc('thang')
            ->paginate(12);

        $thongKeTongHop = Hoadon::where('phong_id', (int) $sinhvien->phong_id)
            ->selectRaw(
                'COUNT(*) as tong_hoa_don,
                SUM(CASE WHEN trangthaithanhtoan = ? THEN 1 ELSE 0 END) as chua_thanh_toan,
                SUM(CASE WHEN trangthaithanhtoan = ? THEN 1 ELSE 0 END) as da_thanh_toan,
                SUM(CASE WHEN trangthaithanhtoan = ? THEN tongtien ELSE 0 END) as tong_tien_da_tra',
                [
                    self::TRANGTHAI_CHUATHANHTOAN,
                    self::TRANGTHAI_DATHANHTOAN,
                    self::TRANGTHAI_DATHANHTOAN,
                ]
            )
            ->first();

        $thongKe = [
            'tong_hoa_don' => (int) ($thongKeTongHop->tong_hoa_don ?? 0),
            'da_thanh_toan' => (int) ($thongKeTongHop->da_thanh_toan ?? 0),
            'chua_thanh_toan' => (int) ($thongKeTongHop->chua_thanh_toan ?? 0),
            'tong_tien_da_tra' => (int) ($thongKeTongHop->tong_tien_da_tra ?? 0),
        ];

        return view('student.phongcuatoi.lichSuHoaDon', [
            'lichSuHoaDon' => $lichSuHoaDon,
            'thongKe' => $thongKe,
        ]);
    }

    public function confirmPayment(int $id)
    {
        $hoadon = Hoadon::find($id);
        if (! $hoadon) {
            return redirect()->back()->with('toast_loai', 'loi')->with('toast_noidung', 'Khong tim thay hoa don.');
        }

        if (! $hoadon->transitionTo(self::TRANGTHAI_DATHANHTOAN)) {
            return redirect()->back()->with('toast_loai', 'loi')->with('toast_noidung', 'Khong the chuyen trang thai hoa don o buoc hien tai.');
        }

        return redirect()->back()->with('toast_loai', 'thanhcong')->with('toast_noidung', 'Xac nhan thanh toan thanh cong.');
    }

    public function viewMyInvoiceDetails(int $id)
    {
        $sinhvien = Sinhvien::where('user_id', Auth::id())->first();

        if (! $sinhvien || ! $sinhvien->phong_id) {
            return redirect()->back()->with('toast_loai', 'loi')->with('toast_noidung', 'Ban chua co phong.');
        }

        $hoadon = Hoadon::where('id', $id)
            ->where('phong_id', (int) $sinhvien->phong_id)
            ->with('phong')
            ->first();

        if (! $hoadon) {
            return redirect()->back()->with('toast_loai', 'loi')->with('toast_noidung', 'Khong tim thay hoa don.');
        }

        $soNguoiTrongPhong = Sinhvien::where('phong_id', (int) $sinhvien->phong_id)->count();
        $tienDienMoiNguoi = $soNguoiTrongPhong > 0 ? $hoadon->tiendien / $soNguoiTrongPhong : 0;
        $tienNuocMoiNguoi = $soNguoiTrongPhong > 0 ? $hoadon->tiennuoc / $soNguoiTrongPhong : 0;
        $tienPhongMoiNguoi = $soNguoiTrongPhong > 0 ? $hoadon->tienphong / $soNguoiTrongPhong : 0;
        $phiDichVuMoiNguoi = $soNguoiTrongPhong > 0 ? $hoadon->phidichvu / $soNguoiTrongPhong : 0;
        $tongTienMoiNguoi = $tienDienMoiNguoi + $tienNuocMoiNguoi + $tienPhongMoiNguoi + $phiDichVuMoiNguoi;

        return view('student.phongcuatoi.chiTietHoaDon', [
            'hoadon' => $hoadon,
            'soNguoiTrongPhong' => $soNguoiTrongPhong,
            'chiTietTien' => [
                'tien_phong' => round($tienPhongMoiNguoi, 0),
                'tien_dien' => round($tienDienMoiNguoi, 0),
                'tien_nuoc' => round($tienNuocMoiNguoi, 0),
                'phi_dich_vu' => round($phiDichVuMoiNguoi, 0),
                'tong_tien' => round($tongTienMoiNguoi, 0),
            ],
        ]);
    }

    public function downloadInvoicePDF(int $id)
    {
        $hoadon = Hoadon::with(['phong.danhsachsinhvien.taikhoan'])->find($id);
        if (! $hoadon) {
            return redirect()->back()->with('toast_loai', 'loi')->with('toast_noidung', 'Khong tim thay hoa don.');
        }

        $user = Auth::user();
        if (($user->vaitro ?? null) === 'sinhvien') {
            $sinhvien = Sinhvien::where('user_id', (int) $user->id)->first();
            if (! $sinhvien || (int) $sinhvien->phong_id !== (int) $hoadon->phong_id) {
                abort(403, 'Ban khong co quyen xem hoa don nay.');
            }
        }

        $phong = $hoadon->phong;
        $danhsachsinhvien = $phong ? $phong->danhsachsinhvien : collect();

        if (! class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            return redirect()->back()->with('toast_loai', 'loi')->with('toast_noidung', 'Vui long cai dat barryvdh/laravel-dompdf.');
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.hoadon', [
            'hoadon' => $hoadon,
            'phong' => $phong,
            'danhsachsinhvien' => $danhsachsinhvien,
        ]);

        return $pdf->download('hoadon_' . $hoadon->thang . '_' . $hoadon->nam . '_' . ($phong->tenphong ?? 'phong') . '.pdf');
    }
}

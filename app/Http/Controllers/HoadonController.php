<?php

namespace App\Http\Controllers;

use App\Models\Cauhinh;
use App\Models\Hoadon;
use App\Models\Phong;
use App\Models\Sinhvien;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HoadonController extends Controller
{
    private const TRANGTHAI_CHUATHANHTOAN = Hoadon::TRANGTHAI_CHUA_THANH_TOAN;
    private const TRANGTHAI_DATHANHTOAN = Hoadon::TRANGTHAI_DA_THANH_TOAN;

    private const DONGIADIEN = 3500;
    private const DONGIANUOC = 15000;

    private function layGiaCauHinh(string $key, string $macdinh): string
    {
        $item = Cauhinh::where('ten', $key)->first();

        return $item ? $item->giatri : $macdinh;
    }

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
                'phong_id.required' => 'Ban chua chon phong.',
                'thang.required' => 'Thang khong duoc de trong.',
                'nam.required' => 'Nam khong duoc de trong.',
                'chisodiencu.required' => 'Chi so dien cu khong duoc de trong.',
                'chisodienmoi.required' => 'Chi so dien moi khong duoc de trong.',
                'chisonuoccu.required' => 'Chi so nuoc cu khong duoc de trong.',
                'chisonuocmoi.required' => 'Chi so nuoc moi khong duoc de trong.',
            ]
        );

        if ((int) $dulieu['chisodienmoi'] < (int) $dulieu['chisodiencu']) {
            return redirect()->back()->withErrors([
                'chisodienmoi' => 'Chi so dien moi phai lon hon hoac bang chi so dien cu.',
            ])->with('toast_loai', 'loi')->with('toast_noidung', 'Chi so dien moi phai lon hon hoac bang chi so dien cu.')->withInput();
        }

        if ((int) $dulieu['chisonuocmoi'] < (int) $dulieu['chisonuoccu']) {
            return redirect()->back()->withErrors([
                'chisonuocmoi' => 'Chi so nuoc moi phai lon hon hoac bang chi so nuoc cu.',
            ])->with('toast_loai', 'loi')->with('toast_noidung', 'Chi so nuoc moi phai lon hon hoac bang chi so nuoc cu.')->withInput();
        }

        $phong = Phong::find((int) $dulieu['phong_id']);
        if (! $phong) {
            return redirect()->back()->with('toast_loai', 'loi')->with('toast_noidung', 'Phong khong ton tai.');
        }

        $dongiadien = (int) $this->layGiaCauHinh('gia_dien', (string) self::DONGIADIEN);
        $dongianuoc = (int) $this->layGiaCauHinh('gia_nuoc', (string) self::DONGIANUOC);

        $tiendien = ((int) $dulieu['chisodienmoi'] - (int) $dulieu['chisodiencu']) * $dongiadien;
        $tiennuoc = ((int) $dulieu['chisonuocmoi'] - (int) $dulieu['chisonuoccu']) * $dongianuoc;
        $tienphong = (int) $phong->giaphong;
        $phidichvu = 0;
        $tongtien = $tienphong + $tiendien + $tiennuoc + $phidichvu;

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
                'tienphong' => $tienphong,
                'tiendien' => $tiendien,
                'tiennuoc' => $tiennuoc,
                'phidichvu' => $phidichvu,
                'tongtien' => $tongtien,
                'ngayxuat' => now()->format('Y-m-d'),
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
                'tienphong' => $tienphong,
                'tiendien' => $tiendien,
                'tiennuoc' => $tiennuoc,
                'phidichvu' => $phidichvu,
                'tongtien' => $tongtien,
                'trangthaithanhtoan' => self::TRANGTHAI_CHUATHANHTOAN,
                'ngayxuat' => now()->format('Y-m-d'),
            ]);
        }

        return redirect()->back()->with('toast_loai', 'thanhcong')->with('toast_noidung', 'Cap nhat hoa don thanh cong.');
    }

    public function hoadoncuatoi()
    {
        $sinhvien = Sinhvien::where('user_id', Auth::id())->first();
        if (! $sinhvien || ! $sinhvien->phong_id) {
            return redirect()->back()->with('toast_loai', 'loi')->with('toast_noidung', 'Ban chua co phong.');
        }

        $lichSuHoaDon = Hoadon::where('phong_id', (int) $sinhvien->phong_id)
            ->orderByDesc('nam')
            ->orderByDesc('thang')
            ->paginate(12);

        $thongKe = [
            'tong_hoa_don' => Hoadon::where('phong_id', (int) $sinhvien->phong_id)->count(),
            'da_thanh_toan' => Hoadon::where('phong_id', (int) $sinhvien->phong_id)
                ->where('trangthaithanhtoan', self::TRANGTHAI_DATHANHTOAN)
                ->count(),
            'chua_thanh_toan' => Hoadon::where('phong_id', (int) $sinhvien->phong_id)
                ->where('trangthaithanhtoan', self::TRANGTHAI_CHUATHANHTOAN)
                ->count(),
            'tong_tien_da_tra' => Hoadon::where('phong_id', (int) $sinhvien->phong_id)
                ->where('trangthaithanhtoan', self::TRANGTHAI_DATHANHTOAN)
                ->sum('tongtien'),
        ];

        return view('student.phongcuatoi.lichSuHoaDon', [
            'lichSuHoaDon' => $lichSuHoaDon,
            'thongKe' => $thongKe,
        ]);
    }

    public function xacnhanthanhtoan(int $id)
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

    public function chiTietHoaDonCuaToi(int $id)
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

    public function xuatPDF(int $id)
    {
        $hoadon = Hoadon::with(['phong.danhsachsinhvien.taikhoan'])->find($id);
        if (! $hoadon) {
            return redirect()->back()->with('toast_loai', 'loi')->with('toast_noidung', 'Khong tim thay hoa don.');
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

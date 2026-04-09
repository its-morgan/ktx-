<?php

namespace App\Http\Controllers;

use App\Mail\DangkyDaDuyetMail;
use App\Models\Dangky;
use App\Models\Hoadon;
use App\Models\Hopdong;
use App\Models\Phong;
use App\Models\Sinhvien;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class DangkyController extends Controller
{
    use \App\Traits\KiemtraKyluat;

    private const TRANGTHAI_CHOXULY = Dangky::TRANGTHAI_CHO_XU_LY;
    private const TRANGTHAI_DADUYET = Dangky::TRANGTHAI_DA_DUYET;
    private const TRANGTHAI_TUCHOI = Dangky::TRANGTHAI_TU_CHOI;
    private const THONGBAO_XUNG_DOT_PHONG = 'Phòng đã đầy hoặc đang có người khác đăng ký, vui lòng thử lại.';

    public function themdangky(Request $request)
    {
        $dulieu = $request->validate(
            [
                'phong_id' => ['required', 'numeric'],
            ],
            [
                'phong_id.required' => 'Ban chua chon phong.',
                'phong_id.numeric' => 'Phong khong hop le.',
            ]
        );

        $sinhvien = Sinhvien::where('user_id', Auth::id())->first();
        if (! $sinhvien) {
            return redirect()->back()->with('toast_loai', 'loi')->with('toast_noidung', 'Khong tim thay thong tin sinh vien.');
        }

        $ketQuaKyluat = $this->kiemTraKyluat($sinhvien->id);
        if ($ketQuaKyluat['bi_chan']) {
            return redirect()->back()->with('toast_loai', 'loi')->with('toast_noidung', $ketQuaKyluat['ly_do']);
        }

        try {
            return DB::transaction(function () use ($dulieu, $sinhvien) {
                $sinhvien = Sinhvien::where('id', $sinhvien->id)->lockForUpdate()->first();
                if (! $sinhvien) {
                    return redirect()->back()->with('toast_loai', 'loi')->with('toast_noidung', 'Khong tim thay thong tin sinh vien.');
                }

                if ($sinhvien->phong_id) {
                    return redirect()->back()->with('toast_loai', 'loi')->with('toast_noidung', 'Ban da duoc xep phong, khong the dang ky them.');
                }

                $phong = Phong::where('id', (int) $dulieu['phong_id'])->lockForUpdate()->first();
                if (! $phong) {
                    return redirect()->back()->with('toast_loai', 'loi')->with('toast_noidung', 'Phong khong ton tai.');
                }

                $succhuaToiDa = $this->laySucChuaToiDa($phong);
                $soLuongHienTai = Sinhvien::where('phong_id', $phong->id)->count();
                if ($soLuongHienTai >= $succhuaToiDa) {
                    return redirect()->back()->with('toast_loai', 'loi')->with('toast_noidung', self::THONGBAO_XUNG_DOT_PHONG);
                }

                $dangkyChoDuyet = Dangky::where('sinhvien_id', $sinhvien->id)
                    ->where('trangthai', self::TRANGTHAI_CHOXULY)
                    ->first();
                if ($dangkyChoDuyet) {
                    return redirect()->back()->with('toast_loai', 'loi')->with('toast_noidung', 'Ban da gui dang ky, vui long cho admin xu ly.');
                }

                Dangky::create([
                    'sinhvien_id' => $sinhvien->id,
                    'phong_id' => $phong->id,
                    'loaidangky' => Dangky::LOAI_THUE_PHONG,
                    'trangthai' => self::TRANGTHAI_CHOXULY,
                    'ghichu' => null,
                ]);

                return redirect()->back()->with('toast_loai', 'thanhcong')->with('toast_noidung', 'Gui dang ky phong thanh cong. Vui long cho admin duyet.');
            });
        } catch (\Throwable $e) {
            return redirect()->back()->with('toast_loai', 'loi')->with('toast_noidung', 'Co loi xay ra: ' . $e->getMessage());
        }
    }

    public function yeucautraphong()
    {
        $sinhvien = Sinhvien::where('user_id', Auth::id())->first();
        if (! $sinhvien || ! $sinhvien->phong_id) {
            return redirect()->back()->with('toast_loai', 'loi')->with('toast_noidung', 'Ban hien khong co phong de tra.');
        }

        $phongDangO = $sinhvien->phong()->first();
        $coHoaDonChuaThanhToan = $phongDangO
            ? $phongDangO->danhsachhoadon()
                ->where('trangthaithanhtoan', Hoadon::TRANGTHAI_CHUA_THANH_TOAN)
                ->exists()
            : false;
        if ($coHoaDonChuaThanhToan) {
            return redirect()->back()->with('toast_loai', 'loi')->with('toast_noidung', 'Bạn còn hóa đơn chưa thanh toán. Vui lòng hoàn thành nghĩa vụ tài chính trước khi gửi yêu cầu trả phòng.');
        }

        $dangkyChoDuyet = Dangky::where('sinhvien_id', $sinhvien->id)
            ->where('trangthai', self::TRANGTHAI_CHOXULY)
            ->where('loaidangky', Dangky::LOAI_TRA_PHONG)
            ->first();
        if ($dangkyChoDuyet) {
            return redirect()->back()->with('toast_loai', 'loi')->with('toast_noidung', 'Ban da gui yeu cau tra phong, vui long cho admin xu ly.');
        }

        Dangky::create([
            'sinhvien_id' => $sinhvien->id,
            'phong_id' => $sinhvien->phong_id,
            'loaidangky' => Dangky::LOAI_TRA_PHONG,
            'trangthai' => self::TRANGTHAI_CHOXULY,
            'ghichu' => null,
        ]);

        return redirect()->back()->with('toast_loai', 'thanhcong')->with('toast_noidung', 'Gui yeu cau tra phong thanh cong.');
    }

    public function yeucaudoiphong(Request $request)
    {
        $dulieu = $request->validate([
            'phong_moi_id' => ['required', 'numeric', 'exists:phong,id'],
            'lydo' => ['required', 'string'],
        ], [
            'phong_moi_id.required' => 'Vui long chon phong moi.',
            'lydo.required' => 'Vui long nhap ly do doi phong.',
        ]);

        $sinhvien = Sinhvien::where('user_id', Auth::id())->first();
        if (! $sinhvien || ! $sinhvien->phong_id) {
            return redirect()->back()->with('toast_loai', 'loi')->with('toast_noidung', 'Ban hien khong co phong de doi.');
        }

        $ketQuaKyluat = $this->kiemTraKyluat($sinhvien->id);
        if ($ketQuaKyluat['bi_chan']) {
            return redirect()->back()->with('toast_loai', 'loi')->with('toast_noidung', $ketQuaKyluat['ly_do']);
        }

        if ((int) $sinhvien->phong_id === (int) $dulieu['phong_moi_id']) {
            return redirect()->back()->with('toast_loai', 'loi')->with('toast_noidung', 'Phong moi phai khac phong hien tai.');
        }

        $dangkyChoDuyet = Dangky::where('sinhvien_id', $sinhvien->id)
            ->where('trangthai', self::TRANGTHAI_CHOXULY)
            ->where('loaidangky', Dangky::LOAI_DOI_PHONG)
            ->first();
        if ($dangkyChoDuyet) {
            return redirect()->back()->with('toast_loai', 'loi')->with('toast_noidung', 'Ban da gui yeu cau doi phong, vui long cho admin xu ly.');
        }

        Dangky::create([
            'sinhvien_id' => $sinhvien->id,
            'phong_id' => (int) $dulieu['phong_moi_id'],
            'loaidangky' => Dangky::LOAI_DOI_PHONG,
            'trangthai' => self::TRANGTHAI_CHOXULY,
            'ghichu' => $dulieu['lydo'],
        ]);

        return redirect()->back()->with('toast_loai', 'thanhcong')->with('toast_noidung', 'Gui yeu cau doi phong thanh cong. Vui long cho admin duyet.');
    }

    public function danhsachdangky(Request $request)
    {
        $status = $request->query('status', '');

        $danhsachdangky = Dangky::when($status && ! in_array($status, ['Tat ca', 'Tất cả'], true), function ($query) use ($status) {
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
     * Duyet dang ky theo workflow lien thong:
     * sinh vien gui don -> admin duyet -> tao hop dong -> tao hoa don dau tien -> gui email.
     */
    public function duyetdangky(Request $request, int $id)
    {
        $dulieu = $request->validate([
            'ngay_het_han' => ['nullable', 'date', 'after_or_equal:today'],
        ]);

        try {
            return DB::transaction(function () use ($id, $dulieu) {
                $dangky = Dangky::with(['sinhvien.taikhoan', 'phong'])->where('id', $id)->lockForUpdate()->first();
                if (! $dangky) {
                    return redirect()->back()->with('toast_loai', 'loi')->with('toast_noidung', 'Khong tim thay dang ky.');
                }

                if ($dangky->trangthai !== self::TRANGTHAI_CHOXULY) {
                    return redirect()->back()->with('toast_loai', 'loi')->with('toast_noidung', 'Dang ky nay da duoc xu ly truoc do.');
                }

                $sinhvien = Sinhvien::where('id', $dangky->sinhvien?->id)->lockForUpdate()->first();
                $phong = Phong::where('id', $dangky->phong?->id)->lockForUpdate()->first();
                if (! $sinhvien || ! $phong) {
                    return redirect()->back()->with('toast_loai', 'loi')->with('toast_noidung', 'Thieu du lieu sinh vien hoac phong.');
                }

                if ($dangky->loaidangky === Dangky::LOAI_TRA_PHONG) {
                    $phongDangO = $sinhvien->phong_id
                        ? Phong::where('id', (int) $sinhvien->phong_id)->lockForUpdate()->first()
                        : null;
                    $giaPhongDangO = (int) ($phongDangO?->giaphong ?? 0);

                    if (! $dangky->transitionTo(self::TRANGTHAI_DADUYET, null)) {
                        return redirect()->back()->with('toast_loai', 'loi')->with('toast_noidung', 'Khong the chuyen trang thai don dang ky.');
                    }

                    Hopdong::where('sinhvien_id', $sinhvien->id)
                        ->where('trang_thai', Hopdong::TRANGTHAI_DANG_HIEU_LUC)
                        ->update(['trang_thai' => Hopdong::TRANGTHAI_DA_THANH_LY]);

                    $sinhvien->update(['phong_id' => null]);

                    $ngayhientai = (int) now()->format('d');
                    $ngaytrongthang = (int) now()->daysInMonth;
                    $tyle = max(0, 1 - ($ngayhientai / $ngaytrongthang));
                    $sotienhoan = max(0, round($giaPhongDangO * $tyle));

                    return redirect()
                        ->back()
                        ->with('toast_loai', 'thanhcong')
                        ->with('toast_noidung', 'Duyet yeu cau tra phong thanh cong. So tien hoan du kien: ' . number_format($sotienhoan) . ' VND.');
                }

                $ngayBatDau = now()->format('Y-m-d');
                $ngayKetThuc = $dulieu['ngay_het_han'] ?? now()->addMonths(5)->format('Y-m-d');
                if (strtotime($ngayKetThuc) <= strtotime($ngayBatDau)) {
                    return redirect()->back()->with('toast_loai', 'loi')->with('toast_noidung', 'Ngay ket thuc phai sau ngay bat dau.');
                }

                if ($dangky->loaidangky === Dangky::LOAI_THUE_PHONG && $sinhvien->phong_id) {
                    return redirect()->back()->with('toast_loai', 'loi')->with('toast_noidung', 'Sinh vien da co phong, khong the duyet.');
                }

                $succhuaToiDa = $this->laySucChuaToiDa($phong);
                $soLuongHienTai = Sinhvien::where('phong_id', $phong->id)->count();
                if ($soLuongHienTai >= $succhuaToiDa) {
                    return redirect()->back()->with('toast_loai', 'loi')->with('toast_noidung', self::THONGBAO_XUNG_DOT_PHONG);
                }

                if (! $dangky->transitionTo(self::TRANGTHAI_DADUYET, null)) {
                    return redirect()->back()->with('toast_loai', 'loi')->with('toast_noidung', 'Khong the chuyen trang thai don dang ky.');
                }

                if ($dangky->loaidangky === Dangky::LOAI_DOI_PHONG && $sinhvien->phong_id) {
                    Phong::where('id', (int) $sinhvien->phong_id)->lockForUpdate()->first();
                    Hopdong::where('sinhvien_id', $sinhvien->id)
                        ->where('trang_thai', Hopdong::TRANGTHAI_DANG_HIEU_LUC)
                        ->update(['trang_thai' => Hopdong::TRANGTHAI_DA_THANH_LY]);
                }

                $sinhvien->update([
                    'phong_id' => $phong->id,
                    'ngay_vao' => $ngayBatDau,
                    'ngay_het_han' => $ngayKetThuc,
                ]);

                $hopdong = Hopdong::create([
                    'sinhvien_id' => $sinhvien->id,
                    'phong_id' => $phong->id,
                    'ngay_bat_dau' => $ngayBatDau,
                    'ngay_ket_thuc' => $ngayKetThuc,
                    'giaphong_luc_ky' => (int) $phong->giaphong,
                    'trang_thai' => Hopdong::TRANGTHAI_DANG_HIEU_LUC,
                    'ghichu' => null,
                ]);

                $hoadon = $this->taoHoaDonDauTien($phong);

                try {
                    $email = $sinhvien->taikhoan?->email;
                    if ($email) {
                        Mail::to($email)->send(new DangkyDaDuyetMail($sinhvien, $phong, $hopdong, $hoadon));
                    }
                } catch (\Throwable $e) {
                    report($e);
                }

                return redirect()
                    ->back()
                    ->with('toast_loai', 'thanhcong')
                    ->with('toast_noidung', 'Duyet dang ky thanh cong. Da tao hop dong, hoa don dau tien va gui email thong bao.');
            });
        } catch (\Throwable $e) {
            return redirect()->back()->with('toast_loai', 'loi')->with('toast_noidung', 'Co loi xay ra: ' . $e->getMessage());
        }
    }

    public function tuchoidangky(Request $request, int $id)
    {
        $dangky = Dangky::find($id);
        if (! $dangky) {
            return redirect()->back()->with('toast_loai', 'loi')->with('toast_noidung', 'Khong tim thay dang ky.');
        }

        $dulieu = $request->validate([
            'ghichu' => ['nullable'],
        ]);

        if (! $dangky->transitionTo(self::TRANGTHAI_TUCHOI, $dulieu['ghichu'] ?? null)) {
            return redirect()->back()->with('toast_loai', 'loi')->with('toast_noidung', 'Khong the tu choi don o trang thai hien tai.');
        }

        return redirect()->back()->with('toast_loai', 'thanhcong')->with('toast_noidung', 'Tu choi dang ky thanh cong.');
    }

    private function laySucChuaToiDa(Phong $phong): int
    {
        $succhua = (int) $phong->succhuamax;

        return max(1, $succhua);
    }

    private function taoHoaDonDauTien(Phong $phong): Hoadon
    {
        $thang = (int) now()->format('m');
        $nam = (int) now()->format('Y');
        $tienphong = (int) $phong->giaphong;

        return Hoadon::firstOrCreate(
            [
                'phong_id' => $phong->id,
                'thang' => $thang,
                'nam' => $nam,
            ],
            [
                'chisodiencu' => 0,
                'chisodienmoi' => 0,
                'chisonuoccu' => 0,
                'chisonuocmoi' => 0,
                'tienphong' => $tienphong,
                'tiendien' => 0,
                'tiennuoc' => 0,
                'phidichvu' => 0,
                'tongtien' => $tienphong,
                'trangthaithanhtoan' => Hoadon::TRANGTHAI_CHUA_THANH_TOAN,
                'ngayxuat' => now()->format('Y-m-d'),
            ]
        );
    }
}

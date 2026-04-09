<?php

namespace App\Http\Controllers;

use App\Models\Hopdong;
use App\Models\Phong;
use App\Models\Sinhvien;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HopdongController extends Controller
{
    use \App\Traits\KiemtraKyluat;

    /**
     * Trạng thái hợp đồng: Đang hiệu lực.
     */
    private const TRANGTHAI_DANGHIEULUC = 'Đang hiệu lực';

    /**
     * Trạng thái hợp đồng: Đã thanh lý.
     */
    private const TRANGTHAI_DATHANHLY = 'Đã thanh lý';

    public function index(Request $request)
    {
        $trangthai = $request->query('trangthai', 'Tất cả');
        $timkiem = $request->query('timkiem', '');

        $danhsachhopdong = Hopdong::with(['sinhvien.taikhoan', 'phong'])
            ->when($trangthai && $trangthai !== 'Tất cả', function ($query) use ($trangthai) {
                return $query->where('trang_thai', $trangthai);
            })
            ->when($timkiem, function ($query) use ($timkiem) {
                return $query->whereHas('sinhvien', function ($q) use ($timkiem) {
                    $q->where('masinhvien', 'like', "%{$timkiem}%");
                });
            })
            ->orderBy('id', 'desc')
            ->paginate(20);

        // Lấy danh sách sinh viên chưa có phòng cho form tạo hợp đồng thủ công
        $sinhvienChuaCoPhong = Sinhvien::whereNull('phong_id')->get();
        $danhsachphong = Phong::all();

        return view('admin.hopdong.danhsach', [
            'danhsachhopdong' => $danhsachhopdong,
            'trangthai' => $trangthai,
            'timkiem' => $timkiem,
            'sinhvienChuaCoPhong' => $sinhvienChuaCoPhong,
            'danhsachphong' => $danhsachphong,
        ]);
    }

    /**
     * Tạo hợp đồng thủ công cho admin.
     * - Kiểm tra sinh viên chưa có hợp đồng đang hiệu lực ở phòng khác.
     * - Kiểm tra phòng còn chỗ trống.
     * - Sử dụng DB::transaction để đảm bảo toàn vẹn dữ liệu.
     */
    public function taohopdong(Request $request)
    {
        $dulieu = $request->validate([
            'sinhvien_id' => ['required', 'numeric', 'exists:sinhvien,id'],
            'phong_id' => ['required', 'numeric', 'exists:phong,id'],
            'ngay_bat_dau' => ['required', 'date'],
            'ngay_ket_thuc' => ['required', 'date', 'after:ngay_bat_dau'],
        ], [
            'sinhvien_id.required' => 'Vui lòng chọn sinh viên.',
            'phong_id.required' => 'Vui lòng chọn phòng.',
            'ngay_bat_dau.required' => 'Vui lòng nhập ngày bắt đầu.',
            'ngay_ket_thuc.required' => 'Vui lòng nhập ngày kết thúc.',
            'ngay_ket_thuc.after' => 'Ngày kết thúc phải sau ngày bắt đầu.',
        ]);

        try {
            return DB::transaction(function () use ($dulieu) {
                $sinhvien = Sinhvien::find((int) $dulieu['sinhvien_id']);
                $phong = Phong::find((int) $dulieu['phong_id']);

                // Kiểm tra sinh viên đã có phòng chưa
                if ($sinhvien->phong_id) {
                    return redirect()
                        ->back()
                        ->with('toast_loai', 'loi')
                        ->with('toast_noidung', 'Sinh viên đã có phòng, không thể tạo hợp đồng mới.');
                }

                // Kiểm tra sinh viên đã có hợp đồng đang hiệu lực ở phòng khác chưa
                $hopdongHieuLuc = Hopdong::where('sinhvien_id', $sinhvien->id)
                    ->where('trang_thai', self::TRANGTHAI_DANGHIEULUC)
                    ->first();

                if ($hopdongHieuLuc) {
                    return redirect()
                        ->back()
                        ->with('toast_loai', 'loi')
                        ->with('toast_noidung', 'Sinh viên đang có hợp đồng hiệu lực ở phòng ' . $hopdongHieuLuc->phong->tenphong . ', không thể tạo thêm.');
                }

                // Kiểm tra phòng còn chỗ không (dùng cột dango)
                if ($phong->dango >= $phong->succhuamax) {
                    return redirect()
                        ->back()
                        ->with('toast_loai', 'loi')
                        ->with('toast_noidung', 'Phòng đã đầy, không thể thêm sinh viên.');
                }

                // Kiểm tra giới tính phòng
                $gioitinhSV = $sinhvien->taikhoan->gioitinh ?? null;
                if ($phong->gioitinh && $gioitinhSV && $phong->gioitinh !== $gioitinhSV) {
                    return redirect()
                        ->back()
                        ->with('toast_loai', 'loi')
                        ->with('toast_noidung', 'Giới tính sinh viên không phù hợp với phòng.');
                }

                // Tạo hợp đồng mới
                Hopdong::create([
                    'sinhvien_id' => $sinhvien->id,
                    'phong_id' => $phong->id,
                    'ngay_bat_dau' => $dulieu['ngay_bat_dau'],
                    'ngay_ket_thuc' => $dulieu['ngay_ket_thuc'],
                    'giaphong_luc_ky' => (int) $phong->giaphong,
                    'trang_thai' => self::TRANGTHAI_DANGHIEULUC,
                    'ghichu' => null,
                ]);

                // Cập nhật sinh viên và tăng số người đang ở
                $sinhvien->update([
                    'phong_id' => $phong->id,
                    'ngay_vao' => $dulieu['ngay_bat_dau'],
                    'ngay_het_han' => $dulieu['ngay_ket_thuc'],
                ]);

                return redirect()
                    ->back()
                    ->with('toast_loai', 'thanhcong')
                    ->with('toast_noidung', 'Tạo hợp đồng thành công.');
            });
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('toast_loai', 'loi')
                ->with('toast_noidung', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    public function giahan(Request $request, int $id)
    {
        $hopdong = Hopdong::find($id);

        if (! $hopdong) {
            return redirect()->back()->with('toast_loai', 'loi')->with('toast_noidung', 'Không tìm thấy hợp đồng.');
        }

        // Kiểm tra kỷ luật trước khi gia hạn
        $ketQuaKyluat = $this->kiemTraKyluat($hopdong->sinhvien_id);
        if ($ketQuaKyluat['bi_chan']) {
            return redirect()
                ->back()
                ->with('toast_loai', 'loi')
                ->with('toast_noidung', $ketQuaKyluat['ly_do']);
        }

        $ngayKetThucHienTai = (string) $hopdong->ngay_ket_thuc;

        $dulieu = $request->validate(
            [
                'ngay_ket_thuc' => ['required', 'date', 'after:today', 'after:' . $ngayKetThucHienTai],
            ],
            [
                'ngay_ket_thuc.required' => 'Bạn phải nhập ngày kết thúc.',
                'ngay_ket_thuc.date' => 'Ngày kết thúc không hợp lệ.',
                'ngay_ket_thuc.after' => 'Ngày kết thúc mới phải sau ngày hôm nay và sau ngày kết thúc hiện tại của hợp đồng (' . $ngayKetThucHienTai . ').',
            ]
        );

        $phong = $hopdong->phong;
        $sinhvien = $hopdong->sinhvien;

        if (! $phong || ! $sinhvien) {
            return redirect()->back()->with('toast_loai', 'loi')->with('toast_noidung', 'Thiếu dữ liệu phòng hoặc sinh viên để gia hạn.');
        }

        $gioitinhphong = $phong->gioitinh ?? null;
        $gioitinhsv = $sinhvien->taikhoan->gioitinh ?? null;
        if ($gioitinhphong && $gioitinhsv && $gioitinhphong !== $gioitinhsv) {
            return redirect()->back()->with('toast_loai', 'loi')->with('toast_noidung', 'Phòng đã đổi giới tính, không thể gia hạn hợp đồng.');
        }

        $soluonghientai = Sinhvien::where('phong_id', $phong->id)->count();
        if ($soluonghientai > (int) $phong->succhuamax) {
            return redirect()->back()->with('toast_loai', 'loi')->with('toast_noidung', 'Phòng đã đầy, không thể gia hạn hợp đồng.');
        }

        $hopdong->update([
            'ngay_ket_thuc' => $dulieu['ngay_ket_thuc'],
        ]);

        if ($sinhvien->phong_id === $phong->id) {
            $sinhvien->update(['ngay_het_han' => $dulieu['ngay_ket_thuc']]);
        }

        return redirect()->back()->with('toast_loai', 'thanhcong')->with('toast_noidung', 'Gia hạn hợp đồng thành công.');
    }

    public function thanhly(int $id)
    {
        $hopdong = Hopdong::find($id);

        if (! $hopdong) {
            return redirect()->back()->with('toast_loai', 'loi')->with('toast_noidung', 'Không tìm thấy hợp đồng.');
        }

        if ($hopdong->trang_thai === 'Đã thanh lý') {
            return redirect()->back()->with('toast_loai', 'loi')->with('toast_noidung', 'Hợp đồng đã được thanh lý trước đó.');
        }

        $sinhvien = $hopdong->sinhvien;
        $phong = $hopdong->phong;

        try {
            return DB::transaction(function () use ($hopdong, $sinhvien, $phong) {
                $hopdong->update(['trang_thai' => self::TRANGTHAI_DATHANHLY]);

                if ($sinhvien) {
                    $sinhvien->update([
                        'phong_id' => null,
                        'ngay_vao' => null,
                        'ngay_het_han' => null,
                    ]);

                    Hopdong::where('sinhvien_id', $sinhvien->id)
                        ->where('trang_thai', self::TRANGTHAI_DANGHIEULUC)
                        ->where('id', '<>', $hopdong->id)
                        ->update(['trang_thai' => self::TRANGTHAI_DATHANHLY]);
                }

                return redirect()->back()->with('toast_loai', 'thanhcong')->with('toast_noidung', 'Đã thanh lý hợp đồng và giải phóng sinh viên khỏi phòng.');
            });
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('toast_loai', 'loi')
                ->with('toast_noidung', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    public function hopdongcuaem(Request $request)
    {
        $sinhvien = Sinhvien::where('user_id', auth()->id())->first();

        if (! $sinhvien) {
            return redirect()->back()->with('toast_loai', 'loi')->with('toast_noidung', 'Không tìm thấy thông tin sinh viên.');
        }

        $danhsachhopdong = $sinhvien->danhsachhopdong()->with('phong')->orderBy('id', 'desc')->get();

        return view('student.hopdong.index', [
            'danhsachhopdong' => $danhsachhopdong,
        ]);
    }

    /**
     * Xuất hợp đồng PDF.
     * - Sử dụng Barryvdh\DomPDF\Facade\Pdf (cần cài đặt package)
     */
    public function xuatPDF(int $id)
    {
        $hopdong = Hopdong::with(['sinhvien.taikhoan', 'phong'])->find($id);

        if (! $hopdong) {
            return redirect()
                ->back()
                ->with('toast_loai', 'loi')
                ->with('toast_noidung', 'Không tìm thấy hợp đồng.');
        }

        // Nếu chưa cài DomPDF, trả về thông báo
        if (! class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            return redirect()
                ->back()
                ->with('toast_loai', 'loi')
                ->with('toast_noidung', 'Vui lòng cài đặt package barryvdh/laravel-dompdf: composer require barryvdh/laravel-dompdf');
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.hopdong', [
            'hopdong' => $hopdong,
            'sinhvien' => $hopdong->sinhvien,
            'phong' => $hopdong->phong,
        ]);

        return $pdf->download('hopdong_' . $hopdong->sinhvien->masinhvien . '_' . $hopdong->ngay_bat_dau . '.pdf');
    }
}

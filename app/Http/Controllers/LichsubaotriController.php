<?php

namespace App\Http\Controllers;

use App\Models\Lichsubaotri;
use App\Models\Phong;
use App\Models\Vattu;
use Illuminate\Http\Request;

class LichsubaotriController extends Controller
{
    /**
     * Trang quản lý bảo trì riêng cho admin.
     */
    public function danhsach(Request $request)
    {
        $tuKhoa = trim((string) $request->query('q', ''));
        $phongId = $request->query('phong_id');

        $baseQuery = Lichsubaotri::query()
            ->when($tuKhoa !== '', function ($query) use ($tuKhoa) {
                $query->where(function ($innerQuery) use ($tuKhoa) {
                    $innerQuery->where('noidung', 'like', '%'.$tuKhoa.'%')
                        ->orWhere('donvithuchien', 'like', '%'.$tuKhoa.'%')
                        ->orWhereHas('vattu', function ($vattuQuery) use ($tuKhoa) {
                            $vattuQuery->where('tenvattu', 'like', '%'.$tuKhoa.'%')
                                ->orWhereHas('phong', function ($phongQuery) use ($tuKhoa) {
                                    $phongQuery->where('tenphong', 'like', '%'.$tuKhoa.'%');
                                });
                        });
                });
            })
            ->when($phongId, function ($query) use ($phongId) {
                $query->whereHas('vattu', function ($vattuQuery) use ($phongId) {
                    $vattuQuery->where('phong_id', $phongId);
                });
            });

        $thongKe = [
            'tong_luot' => (clone $baseQuery)->count(),
            'tong_chiphi' => (clone $baseQuery)->sum('chiphi'),
            'thang_nay' => (clone $baseQuery)
                ->whereMonth('ngaybaotri', now()->month)
                ->whereYear('ngaybaotri', now()->year)
                ->count(),
        ];

        $danhsachBaoTri = (clone $baseQuery)
            ->with(['vattu.phong'])
            ->orderByDesc('ngaybaotri')
            ->paginate(15)
            ->withQueryString();

        $danhsachPhong = Phong::orderBy('tenphong')->get(['id', 'tenphong']);

        return view('admin.baotri.danhsach', [
            'danhsachBaoTri' => $danhsachBaoTri,
            'danhsachPhong' => $danhsachPhong,
            'thongKe' => $thongKe,
            'tuKhoa' => $tuKhoa,
            'phongId' => $phongId,
        ]);
    }

    /**
     * Hiển thị lịch sử bảo trì của vật tư.
     */
    public function lichsu(Request $request, int $vattuId)
    {
        $vattu = Vattu::with('phong')->find($vattuId);

        if (! $vattu) {
            return redirect()
                ->back()
                ->with('toast_loai', 'loi')
                ->with('toast_noidung', 'Không tìm thấy vật tư.');
        }

        $lichsubaotri = Lichsubaotri::where('vattu_id', $vattuId)
            ->orderByDesc('ngaybaotri')
            ->paginate(10);

        $conBaohanh = $this->kiemTraBaohanh($vattu);

        return view('admin.vattu.lichsu', [
            'vattu' => $vattu,
            'lichsubaotri' => $lichsubaotri,
            'conBaohanh' => $conBaohanh,
        ]);
    }

    /**
     * Thêm lịch sử bảo trì mới.
     */
    public function thembaotri(Request $request, int $vattuId)
    {
        $vattu = Vattu::find($vattuId);

        if (! $vattu) {
            return redirect()
                ->back()
                ->with('toast_loai', 'loi')
                ->with('toast_noidung', 'Không tìm thấy vật tư.');
        }

        $dulieu = $request->validate([
            'ngaybaotri' => ['required', 'date'],
            'noidung' => ['required', 'string'],
            'chiphi' => ['nullable', 'numeric', 'min:0'],
            'donvithuchien' => ['nullable', 'string', 'max:255'],
        ], [
            'ngaybaotri.required' => 'Vui lòng nhập ngày bảo trì.',
            'ngaybaotri.date' => 'Ngày bảo trì không hợp lệ.',
            'noidung.required' => 'Vui lòng nhập nội dung bảo trì.',
        ]);

        Lichsubaotri::create([
            'vattu_id' => $vattuId,
            'ngaybaotri' => $dulieu['ngaybaotri'],
            'noidung' => $dulieu['noidung'],
            'chiphi' => $dulieu['chiphi'] ?? 0,
            'donvithuchien' => $dulieu['donvithuchien'] ?? null,
        ]);

        return redirect()
            ->back()
            ->with('toast_loai', 'thanhcong')
            ->with('toast_noidung', 'Thêm lịch sử bảo trì thành công.');
    }

    /**
     * Kiểm tra vật tư còn trong thời gian bảo hành không.
     */
    private function kiemTraBaohanh(Vattu $vattu): array
    {
        if (! $vattu->ngaymua || ! $vattu->thoigianbaohanh) {
            return [
                'co_baohanh' => false,
                'ngay_het_han' => null,
                'so_ngay_con_lai' => 0,
            ];
        }

        $ngayHetHan = \Carbon\Carbon::parse($vattu->ngaymua)->addMonths($vattu->thoigianbaohanh);
        $soNgayConLai = now()->diffInDays($ngayHetHan, false);

        return [
            'co_baohanh' => $soNgayConLai > 0,
            'ngay_het_han' => $ngayHetHan->format('d/m/Y'),
            'so_ngay_con_lai' => max(0, $soNgayConLai),
        ];
    }

    /**
     * Danh sách vật tư sắp hết bảo hành (trong vòng 30 ngày).
     */
    public function vattuSapHetBaohanh()
    {
        $ngay30NgayToi = now()->addDays(30);

        $vattuSapHetBaohanh = Vattu::whereNotNull('ngaymua')
            ->whereNotNull('thoigianbaohanh')
            ->get()
            ->filter(function ($vattu) use ($ngay30NgayToi) {
                $ngayHetHan = \Carbon\Carbon::parse($vattu->ngaymua)->addMonths($vattu->thoigianbaohanh);

                return $ngayHetHan->isBetween(now(), $ngay30NgayToi);
            });

        return view('admin.vattu.saphethan', [
            'vattuSapHetBaohanh' => $vattuSapHetBaohanh,
        ]);
    }
}

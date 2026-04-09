<?php

namespace App\Http\Controllers;

use App\Models\Lienhe;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LienheController extends Controller
{
    public function listInquiries(Request $request): View
    {
        $tuKhoa = trim((string) $request->query('q', ''));
        $trangThai = (string) $request->query('trang_thai', 'tatca');

        $danhSachLienHe = Lienhe::query()
            ->when($trangThai !== 'tatca', function ($query) use ($trangThai) {
                return $query->where('trang_thai', $trangThai);
            })
            ->when($tuKhoa !== '', function ($query) use ($tuKhoa) {
                return $query->where(function ($subQuery) use ($tuKhoa) {
                    $subQuery->where('ho_ten', 'like', "%{$tuKhoa}%")
                        ->orWhere('email', 'like', "%{$tuKhoa}%")
                        ->orWhere('noi_dung', 'like', "%{$tuKhoa}%");
                });
            })
            ->orderByRaw('CASE WHEN trang_thai = ? THEN 0 ELSE 1 END', [Lienhe::TRANG_THAI_CHUA_XU_LY])
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        $thongKe = [
            'tong_so' => Lienhe::count(),
            'chua_xu_ly' => Lienhe::where('trang_thai', Lienhe::TRANG_THAI_CHUA_XU_LY)->count(),
            'da_xu_ly' => Lienhe::where('trang_thai', Lienhe::TRANG_THAI_DA_XU_LY)->count(),
        ];

        return view('admin.lienhe.danhsach', [
            'danhSachLienHe' => $danhSachLienHe,
            'tuKhoa' => $tuKhoa,
            'trangThai' => $trangThai,
            'thongKe' => $thongKe,
        ]);
    }

    public function updateStatus(Request $request, int $id): RedirectResponse
    {
        $duLieu = $request->validate([
            'trang_thai' => ['required', 'in:' . implode(',', [
                Lienhe::TRANG_THAI_CHUA_XU_LY,
                Lienhe::TRANG_THAI_DA_XU_LY,
            ])],
        ]);

        $lienHe = Lienhe::find($id);
        if (! $lienHe) {
            return redirect()->back()->with('toast_loai', 'loi')->with('toast_noidung', 'Khong tim thay lien he.');
        }

        $lienHe->update([
            'trang_thai' => $duLieu['trang_thai'],
        ]);

        return redirect()->back()->with('toast_loai', 'thanhcong')->with('toast_noidung', 'Cap nhat trang thai lien he thanh cong.');
    }
}


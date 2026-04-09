<?php

namespace App\Http\Controllers;

use App\Models\Thongbao;
use Illuminate\Http\Request;

class ThongbaoController extends Controller
{
    /**
     * Danh sách thông báo cho sinh viên.
     */
    public function danhsach(Request $request)
    {
        $loai = $request->query('loai', 'tatca');

        $thongbao = Thongbao::when($loai === 'moi_nhat', function ($query) {
                return $query->where('ngaydang', '>=', now()->subDays(7));
            })
            ->where(function ($query) {
                $query->where('doituong', 'sinhvien')
                    ->orWhere('doituong', 'tatca')
                    ->orWhereNull('doituong');
            })
            ->orderByDesc('ngaydang')
            ->paginate(15);

        // Thống kê
        $thongKe = [
            'tong_so' => Thongbao::where(function ($query) {
                    $query->where('doituong', 'sinhvien')
                        ->orWhere('doituong', 'tatca')
                        ->orWhereNull('doituong');
                })->count(),
            'trong_thang' => Thongbao::where(function ($query) {
                    $query->where('doituong', 'sinhvien')
                        ->orWhere('doituong', 'tatca')
                        ->orWhereNull('doituong');
                })
                ->whereYear('ngaydang', now()->year)
                ->whereMonth('ngaydang', now()->month)
                ->count(),
            'tuan_nay' => Thongbao::where(function ($query) {
                    $query->where('doituong', 'sinhvien')
                        ->orWhere('doituong', 'tatca')
                        ->orWhereNull('doituong');
                })
                ->where('ngaydang', '>=', now()->subDays(7))
                ->count(),
        ];

        return view('student.thongbao.danhsach', [
            'thongbao' => $thongbao,
            'loai' => $loai,
            'thongKe' => $thongKe,
        ]);
    }

    /**
     * Chi tiết thông báo cho sinh viên.
     */
    public function chitiet(int $id)
    {
        $thongbao = Thongbao::where('id', $id)
            ->where(function ($query) {
                $query->where('doituong', 'sinhvien')
                    ->orWhere('doituong', 'tatca')
                    ->orWhereNull('doituong');
            })
            ->first();

        if (! $thongbao) {
            return redirect()
                ->route('student.thongbao')
                ->with('toast_loai', 'loi')
                ->with('toast_noidung', 'Không tìm thấy thông báo.');
        }

        // Thông báo liên quan (cùng tháng)
        $thongbaoLienQuan = Thongbao::where('id', '<>', $id)
            ->where(function ($query) {
                $query->where('doituong', 'sinhvien')
                    ->orWhere('doituong', 'tatca')
                    ->orWhereNull('doituong');
            })
            ->whereYear('ngaydang', now()->year)
            ->whereMonth('ngaydang', now()->month)
            ->orderByDesc('ngaydang')
            ->limit(5)
            ->get();

        return view('student.thongbao.chitiet', [
            'thongbao' => $thongbao,
            'thongbaoLienQuan' => $thongbaoLienQuan,
        ]);
    }

    /**
     * Danh sách thông báo (admin).
     */
    public function index()
    {
        $thongbao = Thongbao::orderByDesc('ngaydang')->paginate(20);

        return view('admin.thongbao.danhsach', compact('thongbao'));
    }

    /**
     * Tạo thông báo mới (admin).
     */
    public function store(Request $request)
    {
        $dulieu = $request->validate([
            'tieude' => ['required', 'string', 'max:255'],
            'noidung' => ['required', 'string'],
            'doituong' => ['nullable', 'in:sinhvien,admin,tatca'],
            'ngaydang' => ['nullable', 'date'],
        ], [
            'tieude.required' => 'Vui lòng nhập tiêu đề.',
            'noidung.required' => 'Vui lòng nhập nội dung.',
        ]);

        Thongbao::create([
            'tieude' => $dulieu['tieude'],
            'noidung' => $dulieu['noidung'],
            'doituong' => $dulieu['doituong'] ?? 'tatca',
            'ngaydang' => $dulieu['ngaydang'] ?? now(),
        ]);

        return redirect()
            ->back()
            ->with('toast_loai', 'thanhcong')
            ->with('toast_noidung', 'Đăng thông báo thành công.');
    }

    /**
     * Cập nhật thông báo (admin).
     */
    public function update(Request $request, int $id)
    {
        $thongbao = Thongbao::find($id);

        if (! $thongbao) {
            return redirect()
                ->back()
                ->with('toast_loai', 'loi')
                ->with('toast_noidung', 'Không tìm thấy thông báo.');
        }

        $dulieu = $request->validate([
            'tieude' => ['required', 'string', 'max:255'],
            'noidung' => ['required', 'string'],
            'doituong' => ['nullable', 'in:sinhvien,admin,tatca'],
            'ngaydang' => ['nullable', 'date'],
        ]);

        $thongbao->update([
            'tieude' => $dulieu['tieude'],
            'noidung' => $dulieu['noidung'],
            'doituong' => $dulieu['doituong'] ?? $thongbao->doituong,
            'ngaydang' => $dulieu['ngaydang'] ?? $thongbao->ngaydang,
        ]);

        return redirect()
            ->back()
            ->with('toast_loai', 'thanhcong')
            ->with('toast_noidung', 'Cập nhật thông báo thành công.');
    }

    /**
     * Xóa thông báo (admin).
     */
    public function destroy(int $id)
    {
        $thongbao = Thongbao::find($id);

        if (! $thongbao) {
            return redirect()
                ->back()
                ->with('toast_loai', 'loi')
                ->with('toast_noidung', 'Không tìm thấy thông báo.');
        }

        $thongbao->delete();

        return redirect()
            ->back()
            ->with('toast_loai', 'thanhcong')
            ->with('toast_noidung', 'Xóa thông báo thành công.');
    }
}

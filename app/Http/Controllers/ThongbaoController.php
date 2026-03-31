<?php

namespace App\Http\Controllers;

use App\Models\Thongbao;
use Illuminate\Http\Request;

class ThongbaoController extends Controller
{
    /**
     * Danh sách thông báo (admin)
     */
    public function index()
    {
        $thongbao = Thongbao::orderByDesc('ngaydang')->get();

        return view('admin.thongbao.danhsach', compact('thongbao'));
    }

    /**
     * Tạo thông báo mới (admin)
     */
    public function store(Request $request)
    {
        $dulieu = $request->validate([
            'tieude' => ['required', 'string'],
            'noidung' => ['required', 'string'],
            'ngaydang' => ['required', 'date'],
        ]);

        Thongbao::create($dulieu);

        return redirect()->back()->with('toast_loai', 'thanhcong')->with('toast_noidung', 'Đăng thông báo thành công.');
    }

    /**
     * Cập nhật thông báo (admin)
     */
    public function update(Request $request, int $id)
    {
        $thongbao = Thongbao::find($id);

        if (! $thongbao) {
            return redirect()->back()->with('toast_loai', 'loi')->with('toast_noidung', 'Không tìm thấy thông báo.');
        }

        $dulieu = $request->validate([
            'tieude' => ['required', 'string'],
            'noidung' => ['required', 'string'],
            'ngaydang' => ['required', 'date'],
        ]);

        $thongbao->update($dulieu);

        return redirect()->back()->with('toast_loai', 'thanhcong')->with('toast_noidung', 'Cập nhật thông báo thành công.');
    }

    /**
     * Xóa thông báo (admin)
     */
    public function destroy(int $id)
    {
        $thongbao = Thongbao::find($id);

        if (! $thongbao) {
            return redirect()->back()->with('toast_loai', 'loi')->with('toast_noidung', 'Không tìm thấy thông báo.');
        }

        $thongbao->delete();

        return redirect()->back()->with('toast_loai', 'thanhcong')->with('toast_noidung', 'Xóa thông báo thành công.');
    }

    /**
     * Xem chi tiết thông báo (student).
     */
    public function chitiet(int $id)
    {
        $thongbao = Thongbao::find($id);

        if (! $thongbao) {
            abort(404, 'Không tìm thấy thông báo.');
        }

        return view('student.chitietthongbao', compact('thongbao'));
    }
}

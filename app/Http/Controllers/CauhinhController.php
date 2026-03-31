<?php

namespace App\Http\Controllers;

use App\Models\Cauhinh;
use Illuminate\Http\Request;

class CauhinhController extends Controller
{
    /**
     * Hiển thị cấu hình hệ thống cho admin
     * - Lấy dữ liệu từ bảng cauhinh
     */
    public function index()
    {
        $cauhinh = Cauhinh::all()->keyBy('ten');

        return view('admin.cauhinh.index', compact('cauhinh'));
    }

    /**
     * Cập nhật cấu hình (gia_dien, gia_nuoc, hotline)
     * - Dữ liệu form gửi qua: gia_dien, gia_nuoc, hotline
     */
    public function update(Request $request)
    {
        $dulieu = $request->validate([
            'gia_dien' => ['required', 'numeric', 'min:0'],
            'gia_nuoc' => ['required', 'numeric', 'min:0'],
            'hotline' => ['required', 'string'],
        ]);

        foreach (['gia_dien', 'gia_nuoc', 'hotline'] as $key) {
            Cauhinh::updateOrCreate(['ten' => $key], ['giatri' => (string) $dulieu[$key]]);
        }

        return redirect()->back()->with('toast_loai', 'thanhcong')->with('toast_noidung', 'Cấu hình cập nhật thành công.');
    }
}

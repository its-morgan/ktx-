<?php

namespace App\Http\Controllers;

use App\Models\Kyluat;
use App\Models\Sinhvien;
use Illuminate\Http\Request;

class KyluatController extends Controller
{
    /**
     * Danh sách kỷ luật cho admin.
     * - Lấy tất cả kỷ luật, có hỗ trợ filter bằng sinh viên hoặc mức độ.
     */
    public function danhsachkyluat(Request $request)
    {
        $sinhviens = Sinhvien::all();

        $kyluat = Kyluat::when($request->query('sinhvien_id'), function ($query) use ($request) {
            return $query->where('sinhvien_id', $request->query('sinhvien_id'));
        })->when($request->query('mucdo'), function ($query) use ($request) {
            return $query->where('mucdo', $request->query('mucdo'));
        })->orderByDesc('ngayvipham')->get();

        return view('admin.kyluat.danhsach', [
            'kyluat' => $kyluat,
            'sinhviens' => $sinhviens,
            'selectedSinhvien' => $request->query('sinhvien_id', ''),
            'selectedMucDo' => $request->query('mucdo', ''),
        ]);
    }

    /**
     * Thêm kỷ luật cho sinh viên (admin).
     */
    public function themkyluat(Request $request)
    {
        $dulieu = $request->validate([
            'sinhvien_id' => ['required', 'numeric', 'exists:sinhvien,id'],
            'noidung' => ['required', 'string'],
            'ngayvipham' => ['required', 'date'],
            'mucdo' => ['required', 'string'],
        ]);

        Kyluat::create($dulieu);

        return redirect()->back()->with('toast_loai', 'thanhcong')->with('toast_noidung', 'Thêm kỷ luật cho sinh viên thành công.');
    }
}

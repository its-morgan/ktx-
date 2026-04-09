<?php

namespace App\Http\Controllers;

use App\Enums\DisciplineLevel;
use App\Models\Kyluat;
use App\Models\Sinhvien;
use Illuminate\Http\Request;

class KyluatController extends Controller
{
    /**
     * Discipline list for admin.
     * - Get all disciplines with support for filtering by student or level.
     */
    public function listDisciplines(Request $request)
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
     * Discipline history of current student.
     */
    public function myDisciplines()
    {
        $sinhvien = Sinhvien::where('user_id', auth()->id())->first();

        if (! $sinhvien) {
            return view('student.kyluatcuaem', ['kyluat' => collect()]);
        }

        $kyluat = Kyluat::where('sinhvien_id', $sinhvien->id)->orderByDesc('ngayvipham')->get();

        return view('student.kyluatcuaem', ['kyluat' => $kyluat]);
    }

    /**
     * Add discipline for student (admin).
     */
    public function storeDiscipline(Request $request)
    {
        $dulieu = $request->validate([
            'sinhvien_id' => ['required', 'numeric', 'exists:sinhvien,id'],
            'noidung' => ['required', 'string'],
            'ngayvipham' => ['required', 'date'],
            'mucdo' => ['required', 'string', 'in:' . implode(',', DisciplineLevel::values())],
        ]);

        Kyluat::create($dulieu);

        return redirect()->back()->with('toast_loai', 'thanhcong')->with('toast_noidung', 'Thêm kỷ luật cho sinh viên thành công.');
    }

    /**
     * Update discipline (admin).
     * - $id is the discipline record id from route
     * - Form data: noidung, ngayvipham, mucdo
     */
    public function updateDiscipline(Request $request, int $id)
    {
        $kyluat = Kyluat::find($id);

        if (! $kyluat) {
            return redirect()->back()->with('toast_loai', 'loi')->with('toast_noidung', 'Không tìm thấy kỷ luật.');
        }

        $dulieu = $request->validate([
            'noidung' => ['required', 'string'],
            'ngayvipham' => ['required', 'date'],
            'mucdo' => ['required', 'string', 'in:' . implode(',', DisciplineLevel::values())],
        ]);

        $kyluat->update($dulieu);

        return redirect()->back()->with('toast_loai', 'thanhcong')->with('toast_noidung', 'Cập nhật kỷ luật thành công.');
    }
}

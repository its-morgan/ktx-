<?php

namespace App\Http\Controllers;

use App\Models\Danhgia;
use App\Models\Phong;
use App\Models\Sinhvien;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DanhgiaController extends Controller
{
    /**
     * Display list of room reviews.
     */
    public function listReviews(Request $request, int $phongId)
    {
        $phong = Phong::find($phongId);

        if (! $phong) {
            return redirect()
                ->back()
                ->with('toast_loai', 'loi')
                ->with('toast_noidung', 'Không tìm thấy phòng.');
        }

        $danhsachdanhgia = Danhgia::where('phong_id', $phongId)
            ->with('sinhvien.taikhoan')
            ->orderByDesc('ngaydanhgia')
            ->paginate(10);

        // Tính điểm trung bình
        $diemTrungBinh = Danhgia::where('phong_id', $phongId)->avg('diem');

        return view('admin.phong.danhgia', [
            'phong' => $phong,
            'danhsachdanhgia' => $danhsachdanhgia,
            'diemTrungBinh' => round($diemTrungBinh, 1),
        ]);
    }

    /**
     * Student submits room review.
     */
    public function storeReview(Request $request)
    {
        $dulieu = $request->validate([
            'diem' => ['required', 'integer', 'min:1', 'max:5'],
            'noidung' => ['nullable', 'string', 'max:500'],
        ], [
            'diem.required' => 'Vui lòng chọn số sao đánh giá.',
            'diem.integer' => 'Điểm đánh giá phải là số nguyên.',
            'diem.min' => 'Điểm đánh giá tối thiểu là 1 sao.',
            'diem.max' => 'Điểm đánh giá tối đa là 5 sao.',
        ]);

        $sinhvien = Sinhvien::where('user_id', Auth::id())->first();

        if (! $sinhvien || ! $sinhvien->phong_id) {
            return redirect()
                ->back()
                ->with('toast_loai', 'loi')
                ->with('toast_noidung', 'Bạn chưa có phòng để đánh giá.');
        }

        // Kiểm tra đã đánh giá trong tháng này chưa
        $thangNay = now()->format('Y-m');
        $daDanhGia = Danhgia::where('sinhvien_id', $sinhvien->id)
            ->where('phong_id', $sinhvien->phong_id)
            ->whereYear('ngaydanhgia', now()->year)
            ->whereMonth('ngaydanhgia', now()->month)
            ->exists();

        if ($daDanhGia) {
            return redirect()
                ->back()
                ->with('toast_loai', 'loi')
                ->with('toast_noidung', 'Bạn đã đánh giá phòng trong tháng này rồi.');
        }

        Danhgia::create([
            'sinhvien_id' => $sinhvien->id,
            'phong_id' => $sinhvien->phong_id,
            'diem' => $dulieu['diem'],
            'noidung' => $dulieu['noidung'] ?? null,
            'ngaydanhgia' => now()->format('Y-m-d'),
        ]);

        return redirect()
            ->back()
            ->with('toast_loai', 'thanhcong')
            ->with('toast_noidung', 'Cảm ơn bạn đã đánh giá phòng!');
    }

    /**
     * Display review form for student.
     */
    public function showReviewForm()
    {
        $sinhvien = Sinhvien::where('user_id', Auth::id())->first();

        if (! $sinhvien || ! $sinhvien->phong_id) {
            return redirect()
                ->route('student.trangchu')
                ->with('toast_loai', 'loi')
                ->with('toast_noidung', 'Bạn chưa có phòng để đánh giá.');
        }

        $phong = Phong::find($sinhvien->phong_id);

        // Kiểm tra đã đánh giá trong tháng này chưa
        $daDanhGia = Danhgia::where('sinhvien_id', $sinhvien->id)
            ->where('phong_id', $sinhvien->phong_id)
            ->whereYear('ngaydanhgia', now()->year)
            ->whereMonth('ngaydanhgia', now()->month)
            ->exists();

        return view('student.danhgia.form', [
            'phong' => $phong,
            'daDanhGia' => $daDanhGia,
        ]);
    }
}

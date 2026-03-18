<?php

namespace App\Http\Controllers;

use App\Models\Phong;
use App\Models\Sinhvien;
use Illuminate\Http\Request;

class PhongController extends Controller
{
    /**
     * Hàm này hiển thị danh sách phòng trống cho sinh viên.
     * - Danh sách phòng lấy từ: bảng phong
     * - Số người đang ở trong phòng lấy từ: bảng sinhvien (cột phong_id)
     */
    public function danhsachphong()
    {
        $danhsachphong = Phong::all();

        $danhsachphongtrong = $danhsachphong->filter(function ($phong) {
            $soluonghientai = Sinhvien::where('phong_id', $phong->id)->count();
            return $soluonghientai < (int) $phong->soluongtoida;
        });

        return view('student.phong.danhsach', [
            'danhsachphong' => $danhsachphongtrong,
        ]);
    }

    /**
     * Hàm này hiển thị danh sách phòng cho admin (quản trị).
     * - Danh sách phòng lấy từ: bảng phong
     * - Số lượng đang ở lấy từ: bảng sinhvien (đếm theo phong_id)
     */
    public function danhsachphongquantri()
    {
        $danhsachphong = Phong::all();

        // Tạo map số lượng đang ở theo phong_id để view hiển thị nhanh
        $soluongdango_theophong = Sinhvien::all()
            ->groupBy('phong_id')
            ->map(function ($nhom) {
                return $nhom->count();
            })
            ->toArray();

        return view('admin.phong.danhsach', [
            'danhsachphong' => $danhsachphong,
            'soluongdango_theophong' => $soluongdango_theophong,
        ]);
    }

    /**
     * Hàm này xử lý thêm mới phòng (admin).
     * - Dữ liệu lấy từ form: tenphong, giaphong, soluongtoida, mota
     */
    public function themphong(Request $request)
    {
        $dulieu = $request->validate(
            [
                'tenphong' => ['required'],
                'giaphong' => ['required', 'numeric', 'min:0'],
                'soluongtoida' => ['required', 'numeric', 'min:1'],
                'mota' => ['nullable'],
            ],
            [
                'tenphong.required' => 'Tên phòng không được để trống.',
                'giaphong.required' => 'Giá phòng không được để trống.',
                'giaphong.numeric' => 'Giá phòng phải là số.',
                'soluongtoida.required' => 'Số lượng tối đa không được để trống.',
                'soluongtoida.numeric' => 'Số lượng tối đa phải là số.',
                'soluongtoida.min' => 'Số lượng tối đa phải lớn hơn hoặc bằng 1.',
            ]
        );

        Phong::create($dulieu);

        return redirect()
            ->back()
            ->with('toast_loai', 'thanhcong')
            ->with('toast_noidung', 'Thêm phòng thành công.');
    }

    /**
     * Hàm này xử lý cập nhật phòng (admin).
     * - $id lấy từ route
     * - Dữ liệu lấy từ form: tenphong, giaphong, soluongtoida, mota
     */
    public function capnhatphong(Request $request, int $id)
    {
        $phong = Phong::find($id);

        if (! $phong) {
            return redirect()
                ->back()
                ->with('toast_loai', 'loi')
                ->with('toast_noidung', 'Không tìm thấy phòng.');
        }

        $dulieu = $request->validate(
            [
                'tenphong' => ['required'],
                'giaphong' => ['required', 'numeric', 'min:0'],
                'soluongtoida' => ['required', 'numeric', 'min:1'],
                'mota' => ['nullable'],
            ],
            [
                'tenphong.required' => 'Tên phòng không được để trống.',
                'giaphong.required' => 'Giá phòng không được để trống.',
                'giaphong.numeric' => 'Giá phòng phải là số.',
                'soluongtoida.required' => 'Số lượng tối đa không được để trống.',
                'soluongtoida.numeric' => 'Số lượng tối đa phải là số.',
                'soluongtoida.min' => 'Số lượng tối đa phải lớn hơn hoặc bằng 1.',
            ]
        );

        $phong->update($dulieu);

        return redirect()
            ->back()
            ->with('toast_loai', 'thanhcong')
            ->with('toast_noidung', 'Cập nhật phòng thành công.');
    }

    /**
     * Hàm này xử lý xóa phòng (admin).
     * - $id lấy từ route
     */
    public function xoaphong(int $id)
    {
        $phong = Phong::find($id);

        if (! $phong) {
            return redirect()
                ->back()
                ->with('toast_loai', 'loi')
                ->with('toast_noidung', 'Không tìm thấy phòng.');
        }

        $phong->delete();

        return redirect()
            ->back()
            ->with('toast_loai', 'thanhcong')
            ->with('toast_noidung', 'Xóa phòng thành công.');
    }
}

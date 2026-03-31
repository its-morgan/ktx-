<?php

namespace App\Http\Controllers;

use App\Models\Phong;
use App\Models\Sinhvien;
use App\Models\Taisan;
use Illuminate\Http\Request;

class PhongController extends Controller
{
    /**
     * Hàm này hiển thị danh sách phòng trống cho sinh viên.
     * - Danh sách phòng lấy từ: bảng phong
     * - Số người đang ở trong phòng lấy từ: bảng sinhvien (cột phong_id)
     */
    public function danhsachphong(Request $request)
    {
        $tuKhoa = $request->query('q', '');
        $sinhvien = Sinhvien::where('user_id', auth()->id())->first();
        $gioitinhSinhvien = optional($sinhvien->taikhoan)->gioitinh ?? null;

        $danhsachphong = Phong::when($tuKhoa, function ($query, $tuKhoa) {
            return $query->where('tenphong', 'like', '%'.trim($tuKhoa).'%');
        })->when($gioitinhSinhvien, function ($query) use ($gioitinhSinhvien) {
            return $query->where('gioitinh', $gioitinhSinhvien);
        })->get();

        $danhsachphongtrong = $danhsachphong->filter(function ($phong) {
            $soluonghientai = Sinhvien::where('phong_id', $phong->id)->count();
            return $soluonghientai < (int) $phong->soluongtoida;
        });

        return view('student.phong.danhsach', [
            'danhsachphong' => $danhsachphongtrong,
            'tuKhoa' => $tuKhoa,
        ]);
    }

    /**
     * Chức năng sinh viên: xem tài sản phòng đang ở.
     */
    public function taisanphong()
    {
        $sinhvien = Sinhvien::where('user_id', auth()->id())->first();

        if (! $sinhvien || ! $sinhvien->phong_id) {
            return view('student.taisanphong', ['taisan' => collect(), 'phong' => null]);
        }

        $phong = Phong::find($sinhvien->phong_id);

        $taisan = Taisan::where('phong_id', $sinhvien->phong_id)->get();

        return view('student.taisanphong', compact('taisan', 'phong'));
    }

    /**
     * Hàm này hiển thị danh sách phòng cho admin (quản trị).
     * - Danh sách phòng lấy từ: bảng phong
     * - Số lượng đang ở lấy từ: bảng sinhvien (đếm theo phong_id)
     */
    public function danhsachphongquantri(Request $request)
    {
        $tuKhoa = $request->query('q', '');

        $viewMode = $request->query('view', 'table');

        $danhsachphong = Phong::when($tuKhoa, function ($query, $tuKhoa) {
            return $query->where('tenphong', 'like', '%'.trim($tuKhoa).'%');
        })->get();

        $soluongdango_theophong = Sinhvien::all()
            ->groupBy('phong_id')
            ->map(function ($nhom) {
                return $nhom->count();
            })
            ->toArray();

        return view('admin.phong.danhsach', [
            'danhsachphong' => $danhsachphong,
            'soluongdango_theophong' => $soluongdango_theophong,
            'tuKhoa' => $tuKhoa,
            'viewMode' => $viewMode,
        ]);
    }

    /**
     * Hàm này hiển thị chi tiết phòng (admin) bao gồm tài sản.
     */
    public function chitietphong(int $id)
    {
        $phong = Phong::find($id);

        if (! $phong) {
            return redirect()->back()->with('toast_loai', 'loi')->with('toast_noidung', 'Không tìm thấy phòng.');
        }

        $taisan = $phong->danhsachtaisan()->get();

        return view('admin.phong.chitiet', compact('phong', 'taisan'));
    }

    /**
     * Thêm tài sản vào phòng (admin).
     */
    public function themtaisan(Request $request, int $id)
    {
        $phong = Phong::find($id);

        if (! $phong) {
            return redirect()->back()->with('toast_loai', 'loi')->with('toast_noidung', 'Không tìm thấy phòng.');
        }

        $dulieu = $request->validate([
            'tentaisan' => ['required', 'string'],
            'soluong' => ['required', 'numeric', 'min:1'],
            'tinhtrang' => ['required', 'string'],
        ]);

        Taisan::create(array_merge($dulieu, ['phong_id' => $phong->id]));

        return redirect()->back()->with('toast_loai', 'thanhcong')->with('toast_noidung', 'Thêm tài sản thành công.');
    }

    /**
     * Cập nhật tài sản (admin).
     */
    public function capnhattaisan(Request $request, int $id, int $taisanId)
    {
        $taisan = Taisan::find($taisanId);

        if (! $taisan || $taisan->phong_id !== $id) {
            return redirect()->back()->with('toast_loai', 'loi')->with('toast_noidung', 'Không tìm thấy tài sản.');
        }

        $dulieu = $request->validate([
            'tentaisan' => ['required', 'string'],
            'soluong' => ['required', 'numeric', 'min:1'],
            'tinhtrang' => ['required', 'string'],
        ]);

        $taisan->update($dulieu);

        return redirect()->back()->with('toast_loai', 'thanhcong')->with('toast_noidung', 'Cập nhật tài sản thành công.');
    }

    /**
     * Xóa tài sản (admin).
     */
    public function xoataisan(int $id, int $taisanId)
    {
        $taisan = Taisan::find($taisanId);

        if (! $taisan || $taisan->phong_id !== $id) {
            return redirect()->back()->with('toast_loai', 'loi')->with('toast_noidung', 'Không tìm thấy tài sản.');
        }

        $taisan->delete();

        return redirect()->back()->with('toast_loai', 'thanhcong')->with('toast_noidung', 'Xóa tài sản thành công.');
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
                'gioitinh' => ['required', 'in:Nam,Nữ'],
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
                'gioitinh' => ['required', 'in:Nam,Nữ'],
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

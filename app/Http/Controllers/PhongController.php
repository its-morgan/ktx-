<?php

namespace App\Http\Controllers;

use App\Models\Hopdong;
use App\Models\Phong;
use App\Models\Sinhvien;
use App\Models\Taisan;
use App\Models\Vattu;
use Illuminate\Http\Request;

class PhongController extends Controller
{
    /**
     * ROUTE CÔNG KHAI: Hiển thị danh sách phòng cho khách (không cần đăng nhập).
     * - Nhóm phòng theo tầng
     * - Hiển thị số chỗ còn trống
     */
    public function danhsachphongcongkhai(Request $request)
    {
        $tuKhoa = $request->query('q', '');
        $tangLoc = $request->query('tang', '');
        $gioiTinhLoc = $request->query('gioitinh', '');

        $danhsachphong = Phong::withCount('danhsachsinhvien')->when($tuKhoa, function ($query, $tuKhoa) {
            return $query->where('tenphong', 'like', '%'.trim($tuKhoa).'%');
        })->when($tangLoc, function ($query) use ($tangLoc) {
            return $query->where('tang', $tangLoc);
        })->when($gioiTinhLoc, function ($query) use ($gioiTinhLoc) {
            return $query->where('gioitinh', $gioiTinhLoc);
        })->orderBy('tang')->orderBy('tenphong')->get();

        // Tính số người đang ở mỗi phòng
        $soluongdango_theophong = $danhsachphong
            ->mapWithKeys(function ($phong) {
                return [$phong->id => $phong->so_nguoi_dang_o];
            })
            ->toArray();

        // Nhóm phòng theo tầng
        $phongTheoTang = $danhsachphong->groupBy('tang');

        // Lấy danh sách tầng cho filter
        $danhsachtang = Phong::select('tang')->distinct()->orderBy('tang')->pluck('tang');

        return view('public.phong.danhsach', [
            'phongTheoTang' => $phongTheoTang,
            'soluongdango_theophong' => $soluongdango_theophong,
            'tuKhoa' => $tuKhoa,
            'tangLoc' => $tangLoc,
            'gioiTinhLoc' => $gioiTinhLoc,
            'danhsachtang' => $danhsachtang,
        ]);
    }

    /**
     * ROUTE CÔNG KHAI: Xem chi tiết vật tư của phòng.
     */
    public function chitietvattuphong(int $id)
    {
        $phong = Phong::find($id);

        if (! $phong) {
            return redirect()->route('public.danhsachphong')
                ->with('toast_loai', 'loi')
                ->with('toast_noidung', 'Khong tim thay phong.');
        }

        $vattu = Vattu::where('phong_id', $id)->get();
        $taisan = Taisan::where('phong_id', $id)->get();

        // Tính số người đang ở
        $soluongdango = Sinhvien::where('phong_id', $id)->count();
        $sochocontrong = $phong->succhuamax - $soluongdango;

        return view('public.phong.vattu', compact('phong', 'vattu', 'taisan', 'soluongdango', 'sochocontrong'));
    }

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

        $danhsachphong = Phong::withCount('danhsachsinhvien')->when($tuKhoa, function ($query, $tuKhoa) {
            return $query->where('tenphong', 'like', '%'.trim($tuKhoa).'%');
        })->when($gioitinhSinhvien, function ($query) use ($gioitinhSinhvien) {
            return $query->where('gioitinh', $gioitinhSinhvien);
        })->get();

        // Tính số người đang ở cho mỗi phòng
        $soluongdango_theophong = $danhsachphong
            ->mapWithKeys(function ($phong) {
                return [$phong->id => $phong->so_nguoi_dang_o];
            })
            ->toArray();

        $danhsachphongtrong = $danhsachphong->filter(function ($phong) use ($soluongdango_theophong) {
            $soluonghientai = $phong->so_nguoi_dang_o;
            return $soluonghientai < (int) $phong->succhuamax;
        });

        return view('student.phong.danhsach', [
            'danhsachphong' => $danhsachphongtrong,
            'soluongdango_theophong' => $soluongdango_theophong,
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
        $tangLoc = $request->query('tang', '');

        $viewMode = $request->query('view', 'table');

        $danhsachphong = Phong::withCount('danhsachsinhvien')->when($tuKhoa, function ($query, $tuKhoa) {
            return $query->where('tenphong', 'like', '%'.trim($tuKhoa).'%');
        })->when($tangLoc, function ($query) use ($tangLoc) {
            return $query->where('tang', $tangLoc);
        })->orderBy('tang')->orderBy('tenphong')->get();

        $soluongdango_theophong = $danhsachphong
            ->mapWithKeys(function ($phong) {
                return [$phong->id => $phong->so_nguoi_dang_o];
            })
            ->toArray();

        // Nhóm phòng theo tầng cho hiển thị
        $phongTheoTang = $danhsachphong->groupBy('tang');

        // Lấy danh sách tầng cho filter
        $danhsachtang = Phong::select('tang')->distinct()->orderBy('tang')->pluck('tang');

        return view('admin.phong.danhsach', [
            'danhsachphong' => $danhsachphong,
            'phongTheoTang' => $phongTheoTang,
            'soluongdango_theophong' => $soluongdango_theophong,
            'tuKhoa' => $tuKhoa,
            'tangLoc' => $tangLoc,
            'danhsachtang' => $danhsachtang,
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
        $vattu = $phong->danhsachvattu()->get();

        return view('admin.phong.chitiet', compact('phong', 'taisan', 'vattu'));
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
     * Thêm vật tư vào phòng (admin).
     */
    public function themvattu(Request $request, int $id)
    {
        $phong = Phong::find($id);

        if (! $phong) {
            return redirect()->back()->with('toast_loai', 'loi')->with('toast_noidung', 'Khong tim thay phong.');
        }

        $dulieu = $request->validate([
            'tenvattu' => ['required', 'string'],
            'soluong' => ['required', 'numeric', 'min:1'],
            'tinhtrang' => ['required', 'string'],
            'mota' => ['nullable', 'string'],
        ]);

        Vattu::create(array_merge($dulieu, ['phong_id' => $phong->id]));

        return redirect()->back()->with('toast_loai', 'thanhcong')->with('toast_noidung', 'Them vat tu thanh cong.');
    }

    /**
     * Cập nhật vật tư (admin).
     */
    public function capnhatvattu(Request $request, int $id, int $vattuId)
    {
        $vattu = Vattu::find($vattuId);

        if (! $vattu || $vattu->phong_id !== $id) {
            return redirect()->back()->with('toast_loai', 'loi')->with('toast_noidung', 'Khong tim thay vat tu.');
        }

        $dulieu = $request->validate([
            'tenvattu' => ['required', 'string'],
            'soluong' => ['required', 'numeric', 'min:1'],
            'tinhtrang' => ['required', 'string'],
            'mota' => ['nullable', 'string'],
        ]);

        $vattu->update($dulieu);

        return redirect()->back()->with('toast_loai', 'thanhcong')->with('toast_noidung', 'Cap nhat vat tu thanh cong.');
    }

    /**
     * Xóa vật tư (admin).
     */
    public function xoavattu(int $id, int $vattuId)
    {
        $vattu = Vattu::find($vattuId);

        if (! $vattu || $vattu->phong_id !== $id) {
            return redirect()->back()->with('toast_loai', 'loi')->with('toast_noidung', 'Khong tim thay vat tu.');
        }

        $vattu->delete();

        return redirect()->back()->with('toast_loai', 'thanhcong')->with('toast_noidung', 'Xoa vat tu thanh cong.');
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
                'tang' => ['required', 'numeric', 'min:1'],
                'giaphong' => ['required', 'numeric', 'min:0'],
                'soluongtoida' => ['required', 'numeric', 'min:1'],
                'succhuamax' => ['required', 'numeric', 'min:1', 'same:soluongtoida'],
                'mota' => ['nullable'],
                'gioitinh' => ['required', 'in:Nam,Nữ'],
            ],
            [
                'tenphong.required' => 'Ten phong khong duoc de trong.',
                'tang.required' => 'Tang khong duoc de trong.',
                'giaphong.required' => 'Gia phong khong duoc de trong.',
                'giaphong.numeric' => 'Gia phong phai la so.',
                'soluongtoida.required' => 'So luong toi da khong duoc de trong.',
                'soluongtoida.numeric' => 'So luong toi da phai la so.',
                'soluongtoida.min' => 'So luong toi da phai lon hon hoac bang 1.',
                'succhuamax.required' => 'Suc chua toi da khong duoc de trong.',
                'succhuamax.same' => 'Suc chua toi da phai bang so luong toi da.',
                'gioitinh.required' => 'Gioi tinh khong duoc de trong.',
            ]
        );

        $dulieu['dango'] = 0;

        Phong::create($dulieu);

        return redirect()
            ->back()
            ->with('toast_loai', 'thanhcong')
            ->with('toast_noidung', 'Them phong thanh cong.');
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
                ->with('toast_noidung', 'Khong tim thay phong.');
        }

        $dulieu = $request->validate(
            [
                'tenphong' => ['required'],
                'tang' => ['required', 'numeric', 'min:1'],
                'giaphong' => ['required', 'numeric', 'min:0'],
                'soluongtoida' => ['required', 'numeric', 'min:1'],
                'succhuamax' => ['required', 'numeric', 'min:1', 'same:soluongtoida'],
                'mota' => ['nullable'],
                'gioitinh' => ['required', 'in:Nam,Nữ'],
            ],
            [
                'tenphong.required' => 'Ten phong khong duoc de trong.',
                'tang.required' => 'Tang khong duoc de trong.',
                'giaphong.required' => 'Gia phong khong duoc de trong.',
                'giaphong.numeric' => 'Gia phong phai la so.',
                'soluongtoida.required' => 'So luong toi da khong duoc de trong.',
                'soluongtoida.numeric' => 'So luong toi da phai la so.',
                'soluongtoida.min' => 'So luong toi da phai lon hon hoac bang 1.',
                'succhuamax.required' => 'Suc chua toi da khong duoc de trong.',
                'succhuamax.same' => 'Suc chua toi da phai bang so luong toi da.',
                'gioitinh.required' => 'Gioi tinh khong duoc de trong.',
            ]
        );

        $phong->update($dulieu);

        return redirect()
            ->back()
            ->with('toast_loai', 'thanhcong')
            ->with('toast_noidung', 'Cap nhat phong thanh cong.');
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
                ->with('toast_noidung', 'Khong tim thay phong.');
        }

        $thongdiepChan = $this->kiemTraDieuKienXoaPhong($phong);
        if ($thongdiepChan !== null) {
            return redirect()
                ->back()
                ->with('toast_loai', 'loi')
                ->with('toast_noidung', $thongdiepChan);
        }

        $phong->delete();

        return redirect()
            ->back()
            ->with('toast_loai', 'thanhcong')
            ->with('toast_noidung', 'Xoa phong thanh cong.');
    }

    private function kiemTraDieuKienXoaPhong(Phong $phong): ?string
    {
        $soSinhVienDangO = $phong->danhsachsinhvien()->count();
        if ($soSinhVienDangO > 0) {
            return 'Khong the xoa phong nay vi van con '.$soSinhVienDangO.' sinh vien dang o. Hay chuyen het sinh vien sang phong khac truoc.';
        }

        $soHopDongDangHieuLuc = $phong->danhsachhopdong()
            ->where('trang_thai', Hopdong::TRANGTHAI_DANG_HIEU_LUC)
            ->count();
        if ($soHopDongDangHieuLuc > 0) {
            return 'Khong the xoa phong nay vi con '.$soHopDongDangHieuLuc.' hop dong dang hieu luc. Hay thanh ly het hop dong truoc khi xoa.';
        }

        $soHopDongLichSu = $phong->danhsachhopdong()->count();
        if ($soHopDongLichSu > 0) {
            return 'Khong the xoa phong nay vi da co du lieu hop dong lich su. Viec xoa phong se lam mat lich su hop dong.';
        }

        $soHoaDonLichSu = $phong->danhsachhoadon()->count();
        if ($soHoaDonLichSu > 0) {
            return 'Khong the xoa phong nay vi da co du lieu hoa don lich su. Viec xoa phong se lam mat lich su hoa don.';
        }

        return null;
    }
}

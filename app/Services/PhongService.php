<?php

namespace App\Services;

use App\Interfaces\PhongServiceInterface;
use App\Models\Hopdong;
use App\Models\Phong;
use App\Models\Sinhvien;
use App\Traits\PhanHoiService;
use Illuminate\Http\Request;

class PhongService implements PhongServiceInterface
{
    use PhanHoiService;

    /**
     * Danh sách phòng cho admin.
     */
    public function listRooms(Request $request): array
    {
        $tuKhoa = $request->query('q', '');
        $tangLoc = $request->query('tang', '');
        $viewMode = $request->query('view', 'table');

        $danhsachphong = Phong::withCount('danhsachsinhvien')
            ->when($tuKhoa, function ($query, $tuKhoa) {
                return $query->where('tenphong', 'like', '%'.trim($tuKhoa).'%');
            })
            ->when($tangLoc, function ($query) use ($tangLoc) {
                return $query->where('tang', $tangLoc);
            })
            ->orderBy('tang')
            ->orderBy('tenphong')
            ->get();

        $soluongdango_theophong = $danhsachphong
            ->mapWithKeys(function ($phong) {
                return [$phong->id => $phong->so_nguoi_dang_o];
            })
            ->toArray();

        $phongTheoTang = $danhsachphong->groupBy('tang');
        $danhsachtang = Phong::select('tang')->distinct()->orderBy('tang')->pluck('tang');

        return compact('danhsachphong', 'phongTheoTang', 'soluongdango_theophong', 'tuKhoa', 'tangLoc', 'danhsachtang', 'viewMode');
    }

    /**
     * Danh sách phòng công khai (khách chưa đăng nhập).
     */
    public function listRoomsPublic(Request $request): array
    {
        $tuKhoa = $request->query('q', '');
        $tangLoc = $request->query('tang', '');
        $gioiTinhLoc = $request->query('gioitinh', '');

        $danhsachphong = Phong::withCount('danhsachsinhvien')
            ->when($tuKhoa, function ($query, $tuKhoa) {
                return $query->where('tenphong', 'like', '%'.trim($tuKhoa).'%');
            })
            ->when($tangLoc, function ($query) use ($tangLoc) {
                return $query->where('tang', $tangLoc);
            })
            ->when($gioiTinhLoc, function ($query) use ($gioiTinhLoc) {
                return $query->where('gioitinh', $gioiTinhLoc);
            })
            ->orderBy('tang')
            ->orderBy('tenphong')
            ->get();

        $soluongdango_theophong = $danhsachphong
            ->mapWithKeys(function ($phong) {
                return [$phong->id => $phong->so_nguoi_dang_o];
            })
            ->toArray();

        $phongTheoTang = $danhsachphong->groupBy('tang');
        $danhsachtang = Phong::select('tang')->distinct()->orderBy('tang')->pluck('tang');

        return compact('phongTheoTang', 'soluongdango_theophong', 'tuKhoa', 'tangLoc', 'gioiTinhLoc', 'danhsachtang');
    }

    /**
     * Danh sách phòng cho sinh viên đăng nhập chọn.
     */
    public function listStudentRooms(Request $request): array
    {
        $tuKhoa = $request->query('q', '');
        $sinhvien = Sinhvien::where('user_id', auth()->id())->first();
        $gioitinhSinhvien = optional($sinhvien?->taikhoan)->gioitinh ?? null;

        $danhsachphong = Phong::withCount('danhsachsinhvien')
            ->when($tuKhoa, function ($query, $tuKhoa) {
                return $query->where('tenphong', 'like', '%'.trim($tuKhoa).'%');
            })
            ->when($gioitinhSinhvien, function ($query) use ($gioitinhSinhvien) {
                return $query->where('gioitinh', $gioitinhSinhvien);
            })
            ->get();

        $soluongdango_theophong = $danhsachphong
            ->mapWithKeys(function ($phong) {
                return [$phong->id => $phong->so_nguoi_dang_o];
            })
            ->toArray();

        $danhsachphongtrong = $danhsachphong->filter(function ($phong) use ($soluongdango_theophong) {
            $soluonghientai = $phong->so_nguoi_dang_o;
            return $soluonghientai < (int) $phong->succhuamax;
        });

        return compact('danhsachphongtrong', 'soluongdango_theophong', 'tuKhoa');
    }

    /**
     * Chi tiết phòng (admin).
     *
     * @return array ['phong' => Phong, 'taisan' => Collection, 'vattu' => Collection] | ['error' => string]
     */
    public function viewRoom(int $id): array
    {
        $phong = Phong::find($id);
        if (! $phong) {
            return ['error' => 'Không tìm thấy phòng.'];
        }

        $taisan = $phong->danhsachtaisan()->get();
        $vattu = $phong->danhsachvattu()->get();

        return compact('phong', 'taisan', 'vattu');
    }

    /**
     * Thêm phòng mới (admin).
     */
    public function storeRoom(Request $request): array
    {
        try {
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

            return ['success' => true, 'message' => 'Them phong thanh cong.'];
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => 'Có lỗi xảy ra: ' . $e->getMessage()];
        }
    }

    /**
     * Cập nhật phòng (admin).
     */
    public function updateRoom(Request $request, int $id): array
    {
        try {
            $phong = Phong::find($id);
            if (! $phong) {
                return ['success' => false, 'message' => 'Khong tim thay phong.'];
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

            return ['success' => true, 'message' => 'Cap nhat phong thanh cong.'];
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => 'Có lỗi xảy ra: ' . $e->getMessage()];
        }
    }

    /**
     * Xóa phòng (admin).
     */
    public function destroyRoom(int $id): array
    {
        try {
            $phong = Phong::find($id);
            if (! $phong) {
                return ['success' => false, 'message' => 'Khong tim thay phong.'];
            }

            $thongdiepChan = $this->kiemTraDieuKienXoaPhong($phong);
            if ($thongdiepChan !== null) {
                return ['success' => false, 'message' => $thongdiepChan];
            }

            $phong->delete();

            return ['success' => true, 'message' => 'Xoa phong thanh cong.'];
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => 'Có lỗi xảy ra: ' . $e->getMessage()];
        }
    }

    /**
     * Kiểm tra điều kiện xóa phòng — trả về thông điệp chặn nếu không thể xóa.
     */
    private function kiemTraDieuKienXoaPhong(Phong $phong): ?string
    {
        // Dùng dango (cached by SinhvienObserver) làm source of truth
        $soSinhVienDangO = $phong->dango;
        if ($soSinhVienDangO > 0) {
            return 'Khong the xoa phong nay vi van con '.$soSinhVienDangO.' sinh vien dang o. Hay chuyen het sinh vien sang phong khac truoc.';
        }

        $soHopDongDangHieuLuc = $phong->danhsachhopdong()
            ->where('trang_thai', Hopdong::trangThaiDangHieuLuc())
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

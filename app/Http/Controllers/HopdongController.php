<?php

namespace App\Http\Controllers;

use App\Models\Hopdong;
use App\Models\Phong;
use App\Models\Sinhvien;
use Illuminate\Http\Request;

class HopdongController extends Controller
{
    public function index(Request $request)
    {
        $trangthai = $request->query('trangthai', 'Tất cả');
        $timkiem = $request->query('timkiem', '');

        $danhsachhopdong = Hopdong::with(['sinhvien.taikhoan', 'phong'])
            ->when($trangthai && $trangthai !== 'Tất cả', function ($query) use ($trangthai) {
                return $query->where('trang_thai', $trangthai);
            })
            ->when($timkiem, function ($query) use ($timkiem) {
                return $query->whereHas('sinhvien', function ($q) use ($timkiem) {
                    $q->where('masinhvien', 'like', "%{$timkiem}%");
                });
            })
            ->orderBy('id', 'desc')
            ->paginate(20);

        return view('admin.hopdong.danhsach', [
            'danhsachhopdong' => $danhsachhopdong,
            'trangthai' => $trangthai,
            'timkiem' => $timkiem,
        ]);
    }

    public function giahan(Request $request, int $id)
    {
        $hopdong = Hopdong::find($id);

        if (! $hopdong) {
            return redirect()->back()->with('toast_loai', 'loi')->with('toast_noidung', 'Không tìm thấy hợp đồng.');
        }

        $dulieu = $request->validate(
            [
                'ngay_ket_thuc' => ['required', 'date', 'after:ngay_bat_dau'],
            ],
            [
                'ngay_ket_thuc.required' => 'Bạn phải nhập ngày kết thúc.',
                'ngay_ket_thuc.date' => 'Ngày kết thúc không hợp lệ.',
                'ngay_ket_thuc.after' => 'Ngày kết thúc phải sau ngày bắt đầu.',
            ]
        );

        $phong = $hopdong->phong;
        $sinhvien = $hopdong->sinhvien;

        if (! $phong || ! $sinhvien) {
            return redirect()->back()->with('toast_loai', 'loi')->with('toast_noidung', 'Thiếu dữ liệu phòng hoặc sinh viên để gia hạn.');
        }

        $gioitinhphong = $phong->gioitinh ?? null;
        $gioitinhsv = $sinhvien->taikhoan->gioitinh ?? null;
        if ($gioitinhphong && $gioitinhsv && $gioitinhphong !== $gioitinhsv) {
            return redirect()->back()->with('toast_loai', 'loi')->with('toast_noidung', 'Phòng đã đổi giới tính, không thể gia hạn hợp đồng.');
        }

        $soluonghientai = Sinhvien::where('phong_id', $phong->id)->count();
        if ($soluonghientai > (int) $phong->soluongtoida) {
            return redirect()->back()->with('toast_loai', 'loi')->with('toast_noidung', 'Phòng đã đầy, không thể gia hạn hợp đồng.');
        }

        $hopdong->update([
            'ngay_ket_thuc' => $dulieu['ngay_ket_thuc'],
        ]);

        if ($sinhvien->phong_id === $phong->id) {
            $sinhvien->update(['ngay_het_han' => $dulieu['ngay_ket_thuc']]);
        }

        return redirect()->back()->with('toast_loai', 'thanhcong')->with('toast_noidung', 'Gia hạn hợp đồng thành công.');
    }

    public function thanhly(int $id)
    {
        $hopdong = Hopdong::find($id);

        if (! $hopdong) {
            return redirect()->back()->with('toast_loai', 'loi')->with('toast_noidung', 'Không tìm thấy hợp đồng.');
        }

        if ($hopdong->trang_thai === 'Đã thanh lý') {
            return redirect()->back()->with('toast_loai', 'loi')->with('toast_noidung', 'Hợp đồng đã được thanh lý trước đó.');
        }

        $sinhvien = $hopdong->sinhvien;

        $hopdong->update(['trang_thai' => 'Đã thanh lý']);

        if ($sinhvien) {
            $sinhvien->update([
                'phong_id' => null,
                'ngay_vao' => null,
                'ngay_het_han' => null,
            ]);

            Hopdong::where('sinhvien_id', $sinhvien->id)
                ->where('trang_thai', 'Đang hiệu lực')
                ->where('id', '<>', $hopdong->id)
                ->update(['trang_thai' => 'Đã thanh lý']);
        }

        return redirect()->back()->with('toast_loai', 'thanhcong')->with('toast_noidung', 'Đã thanh lý hợp đồng và giải phóng sinh viên khỏi phòng.');
    }

    public function hopdongcuaem(Request $request)
    {
        $sinhvien = Sinhvien::where('user_id', auth()->id())->first();

        if (! $sinhvien) {
            return redirect()->back()->with('toast_loai', 'loi')->with('toast_noidung', 'Không tìm thấy thông tin sinh viên.');
        }

        $danhsachhopdong = $sinhvien->danhsachhopdong()->with('phong')->orderBy('id', 'desc')->get();

        return view('student.hopdong.index', [
            'danhsachhopdong' => $danhsachhopdong,
        ]);
    }
}

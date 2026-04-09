<?php

namespace App\Http\Controllers;

use App\Models\Danhgia;
use App\Models\Hoadon;
use App\Models\Hopdong;
use App\Models\Phong;
use App\Models\Sinhvien;
use App\Models\Taisan;
use App\Models\Thongbao;
use App\Models\Vattu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PhongCuaToiController extends Controller
{
    /**
     * Trang tổng quan phòng của sinh viên.
     * Hiển thị tất cả thông tin về phòng hiện tại:
     * - Thông tin phòng, hợp đồng
     * - Danh sách bạn cùng phòng
     * - Tài sản, vật tư trong phòng
     * - Hóa đơn chưa thanh toán
     * - Đánh giá phòng
     */
    public function index()
    {
        $sinhvien = Sinhvien::where('user_id', Auth::id())
            ->with(['taikhoan', 'phong'])
            ->first();

        if (! $sinhvien) {
            return redirect()
                ->route('student.trangchu')
                ->with('toast_loai', 'loi')
                ->with('toast_noidung', 'Không tìm thấy thông tin sinh viên.');
        }

        // Nếu chưa có phòng
        if (! $sinhvien->phong_id) {
            return view('student.phongcuatoi.index', [
                'sinhvien' => $sinhvien,
                'coPhong' => false,
                'danhsachphongtrong' => $this->layDanhSachPhongPhuHop($sinhvien),
            ]);
        }

        $phong = Phong::with(['danhsachtaisan', 'danhsachvattu'])
            ->find($sinhvien->phong_id);

        // Bạn cùng phòng
        $banCungPhong = Sinhvien::where('phong_id', $sinhvien->phong_id)
            ->where('id', '<>', $sinhvien->id)
            ->with('taikhoan')
            ->get();

        // Hợp đồng hiện tại
        $hopdongHienTai = Hopdong::where('sinhvien_id', $sinhvien->id)
            ->where('trang_thai', 'Đang hiệu lực')
            ->with('phong')
            ->first();

        // Hóa đơn chưa thanh toán
        $hoadonChuaThanhToan = Hoadon::where('phong_id', $sinhvien->phong_id)
            ->where('trangthaithanhtoan', 'Chưa thanh toán')
            ->orderByDesc('nam')
            ->orderByDesc('thang')
            ->get();

        // Tổng nợ
        $tongNo = $hoadonChuaThanhToan->sum('tongtien');

        // Tài sản và vật tư phòng
        $taisan = Taisan::where('phong_id', $sinhvien->phong_id)->get();
        $vattu = Vattu::where('phong_id', $sinhvien->phong_id)->get();

        // Kiểm tra đã đánh giá phòng trong tháng chưa
        $daDanhGia = Danhgia::where('sinhvien_id', $sinhvien->id)
            ->where('phong_id', $sinhvien->phong_id)
            ->whereYear('ngaydanhgia', now()->year)
            ->whereMonth('ngaydanhgia', now()->month)
            ->exists();

        // Điểm đánh giá trung bình của phòng
        $diemTrungBinh = Danhgia::where('phong_id', $sinhvien->phong_id)->avg('diem');

        // Thông báo mới nhất liên quan đến phòng
        $thongbaoMoiNhat = Thongbao::where('doituong', 'sinhvien')
            ->orWhereNull('doituong')
            ->orderByDesc('ngaydang')
            ->limit(5)
            ->get();

        // Cảnh báo hợp đồng sắp hết hạn
        $canhBaoHetHan = null;
        if ($hopdongHienTai && $hopdongHienTai->ngay_ket_thuc) {
            $ngayHetHan = \Carbon\Carbon::parse($hopdongHienTai->ngay_ket_thuc);
            $soNgayConLai = now()->diffInDays($ngayHetHan, false);
            
            if ($soNgayConLai <= 30 && $soNgayConLai > 0) {
                $canhBaoHetHan = [
                    'so_ngay_con_lai' => $soNgayConLai,
                    'ngay_het_han' => $ngayHetHan->format('d/m/Y'),
                    'muc_do' => $soNgayConLai <= 7 ? 'nguy_hiểm' : ($soNgayConLai <= 15 ? 'cảnh_báo' : 'thông_báo'),
                ];
            }
        }

        return view('student.phongcuatoi.index', [
            'sinhvien' => $sinhvien,
            'coPhong' => true,
            'phong' => $phong,
            'banCungPhong' => $banCungPhong,
            'hopdongHienTai' => $hopdongHienTai,
            'hoadonChuaThanhToan' => $hoadonChuaThanhToan,
            'tongNo' => $tongNo,
            'taisan' => $taisan,
            'vattu' => $vattu,
            'daDanhGia' => $daDanhGia,
            'diemTrungBinh' => round($diemTrungBinh ?? 0, 1),
            'thongbaoMoiNhat' => $thongbaoMoiNhat,
            'canhBaoHetHan' => $canhBaoHetHan,
        ]);
    }

    /**
     * Lấy danh sách phòng phù hợp với sinh viên (giới tính, còn chỗ).
     */
    private function layDanhSachPhongPhuHop(Sinhvien $sinhvien)
    {
        $gioiTinhSinhVien = $sinhvien->taikhoan->gioitinh ?? null;

        return Phong::when($gioiTinhSinhVien, function ($query) use ($gioiTinhSinhVien) {
                return $query->where('gioitinh', $gioiTinhSinhVien);
            })
            ->get()
            ->filter(function ($phong) {
                $soNguoiDangO = Sinhvien::where('phong_id', $phong->id)->count();
                return $soNguoiDangO < $phong->succhuamax;
            })
            ->take(5);
    }

}

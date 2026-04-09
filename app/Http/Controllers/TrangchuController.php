<?php

namespace App\Http\Controllers;

use App\Models\Baohong;
use App\Models\Dangky;
use App\Models\Hoadon;
use App\Models\Hopdong;
use App\Models\Kyluat;
use App\Models\Phong;
use App\Models\Sinhvien;
use App\Models\Taisan;
use App\Models\Thongbao;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TrangchuController extends Controller
{
    /**
     * Route riêng cho dashboard admin để tiện kiểm thử độc lập.
     */
    public function hienthiAdmin()
    {
        return $this->hienthi();
    }

    /**
     * Route riêng cho dashboard sinh viên để tiện kiểm thử độc lập.
     */
    public function hienthiSinhvien()
    {
        return $this->hienthi();
    }
    /**
     * Hàm này hiển thị dashboard (trang chủ) theo vai trò.
     * - Vai trò lấy từ: Auth::user()->vaitro (bảng users, cột vaitro)
     * - Số liệu tổng quan lấy từ: bảng phong, sinhvien, hoadon
     */
    public function hienthi()
    {
        // Lấy vai trò người dùng đang đăng nhập
        $vaitro = Auth::user()->vaitro ?? 'sinhvien';

        // Lấy tổng số phòng từ bảng phong
        $tongphong = Phong::all()->count();

        // Lấy tổng số sinh viên từ bảng sinhvien
        $tongsinhvien = Sinhvien::all()->count();

        // Tính doanh thu trong tháng hiện tại (chỉ tính hóa đơn đã thanh toán)
        $thanghientai = (int) now()->format('m');
        $namhientai = (int) now()->format('Y');

        $doanhthuthang = Hoadon::where('thang', $thanghientai)
            ->where('nam', $namhientai)
            ->where('trangthaithanhtoan', 'Đã thanh toán')
            ->get()
            ->sum('tongtien');

        /**
         * Các thống kê riêng cho admin.
         * - Dữ liệu lấy từ: phong, sinhvien, dangky, baohong, hoadon
         */
        $tongphongtrong = 0;
        $dangkychoxuly = 0;
        $baohongchosua = 0;
        $hoadonchuathanhtoan = 0;
        $danhsachdangkygannhat = collect();
        $danhsachbaohonggannhat = collect();
        $doanhthugannhat = [];
        $nhan = [];
        $thongbao = collect();
        $hopdongsaphethan = collect();
        $diennuocbathuong = collect();

        if ($vaitro === 'admin') {
            $thongbao = Thongbao::orderByDesc('ngaydang')->limit(5)->get();
            // Đếm phòng trống dựa theo số sinh viên hiện tại và số lượng tối đa
            $danhsachphong = Phong::all();
            $tongphongtrong = $danhsachphong->filter(function ($phong) {
                $soluonghientai = Sinhvien::where('phong_id', $phong->id)->count();
                return $soluonghientai < (int) $phong->succhuamax;
            })->count();

            // Đếm đăng ký đang chờ xử lý
            $dangkychoxuly = Dangky::where('trangthai', 'Chờ xử lý')->count();

            // Đếm báo hỏng đang chờ sửa
            $baohongchosua = Baohong::where('trangthai', 'Chờ sửa')->count();

            // Đếm hóa đơn chưa thanh toán trong tháng hiện tại
            $hoadonchuathanhtoan = Hoadon::where('thang', $thanghientai)
                ->where('nam', $namhientai)
                ->where('trangthaithanhtoan', 'Chưa thanh toán')
                ->count();

            // CẢNH BÁO: Hợp đồng sắp hết hạn (trong vòng 30 ngày)
            $ngay30NgayToi = now()->addDays(30)->format('Y-m-d');
            $hopdongsaphethan = Hopdong::where('trang_thai', 'Đang hiệu lực')
                ->whereDate('ngay_ket_thuc', '<=', $ngay30NgayToi)
                ->whereDate('ngay_ket_thuc', '>=', now()->format('Y-m-d'))
                ->with(['sinhvien.taikhoan', 'phong'])
                ->orderBy('ngay_ket_thuc', 'asc')
                ->limit(10)
                ->get();

            // CẢNH BÁO: Điện nước tăng bất thường (>50% so với tháng trước)
            $thangTruoc = now()->subMonth();
            $hoadonThangTruoc = Hoadon::where('thang', $thangTruoc->format('m'))
                ->where('nam', $thangTruoc->format('Y'))
                ->get()
                ->keyBy('phong_id');

            $hoadonThangNay = Hoadon::where('thang', $thanghientai)
                ->where('nam', $namhientai)
                ->with('phong')
                ->get();

            $diennuocbathuong = [];
            foreach ($hoadonThangNay as $hoadon) {
                $phongId = $hoadon->phong_id;
                if (isset($hoadonThangTruoc[$phongId])) {
                    $hoadonTruoc = $hoadonThangTruoc[$phongId];

                    // Tính số điện, nước tiêu thụ
                    $dienTieuThuThangNay = $hoadon->chisodienmoi - $hoadon->chisodiencu;
                    $dienTieuThuThangTruoc = $hoadonTruoc->chisodienmoi - $hoadonTruoc->chisodiencu;
                    $nuocTieuThuThangNay = $hoadon->chisonuocmoi - $hoadon->chisonuoccu;
                    $nuocTieuThuThangTruoc = $hoadonTruoc->chisonuocmoi - $hoadonTruoc->chisonuoccu;

                    // Kiểm tra tăng >50%
                    if ($dienTieuThuThangTruoc > 0 && $dienTieuThuThangNay > $dienTieuThuThangTruoc * 1.5) {
                        $diennuocbathuong[] = [
                            'phong' => $hoadon->phong,
                            'loai' => 'Điện',
                            'thang_truoc' => $dienTieuThuThangTruoc,
                            'thang_nay' => $dienTieuThuThangNay,
                            'ty_le_tang' => round((($dienTieuThuThangNay - $dienTieuThuThangTruoc) / $dienTieuThuThangTruoc) * 100, 1),
                        ];
                    }

                    if ($nuocTieuThuThangTruoc > 0 && $nuocTieuThuThangNay > $nuocTieuThuThangTruoc * 1.5) {
                        $diennuocbathuong[] = [
                            'phong' => $hoadon->phong,
                            'loai' => 'Nước',
                            'thang_truoc' => $nuocTieuThuThangTruoc,
                            'thang_nay' => $nuocTieuThuThangNay,
                            'ty_le_tang' => round((($nuocTieuThuThangNay - $nuocTieuThuThangTruoc) / $nuocTieuThuThangTruoc) * 100, 1),
                        ];
                    }
                }
            }
            $diennuocbathuong = collect($diennuocbathuong);

            // Danh sách việc cần làm (lấy 5 mục gần nhất)
            $danhsachdangkygannhat = Dangky::where('trangthai', 'Chờ xử lý')
                ->orderByDesc('id')
                ->limit(5)
                ->get();

            $danhsachbaohonggannhat = Baohong::where('trangthai', 'Chờ sửa')
                ->orderByDesc('id')
                ->limit(5)
                ->get();

            // Thống kê doanh thu 6 tháng gần nhất (đã thanh toán) - Tách thành 2 cột
            $doanhthugannhat_tienphong = [];
            $doanhthugannhat_tiendichvu = [];
            $nhan = [];
            for ($i = 5; $i >= 0; $i--) {
                $m = now()->subMonths($i);
                $thang = (int)$m->format('m');
                $nam = (int)$m->format('Y');

                // Tính tiền phòng riêng
                $tongtienphong = Hoadon::where('thang', $thang)
                    ->where('nam', $nam)
                    ->where('trangthaithanhtoan', Hoadon::TRANGTHAI_DA_THANH_TOAN)
                    ->sum('tienphong');

                // Tính tiền dịch vụ (điện + nước + phí dịch vụ)
                $tongtiendichvu = Hoadon::where('thang', $thang)
                    ->where('nam', $nam)
                    ->where('trangthaithanhtoan', Hoadon::TRANGTHAI_DA_THANH_TOAN)
                    ->sum(DB::raw('tiendien + tiennuoc + phidichvu'));

                $doanhthugannhat_tienphong[] = (int)$tongtienphong;
                $doanhthugannhat_tiendichvu[] = (int)$tongtiendichvu;
                $nhan[] = $m->format('m/Y');
            }

            $doanhthugannhat = [
                'tienphong' => $doanhthugannhat_tienphong,
                'tiendichvu' => $doanhthugannhat_tiendichvu,
            ];
        }

        /**
         * Hàm này trả về view theo vai trò để phân tách thư mục giao diện:
         * - Admin: resources/views/admin/trangchu.blade.php
         * - Sinh viên: resources/views/student/trangchu.blade.php
         */
        if ($vaitro === 'admin') {
            return view('admin.trangchu', compact(
                'vaitro',
                'tongphong',
                'tongphongtrong',
                'tongsinhvien',
                'dangkychoxuly',
                'baohongchosua',
                'hoadonchuathanhtoan',
                'doanhthuthang',
                'danhsachdangkygannhat',
                'danhsachbaohonggannhat',
                'thanghientai',
                'namhientai',
                'doanhthugannhat',
                'nhan',
                'thongbao',
                'hopdongsaphethan',
                'diennuocbathuong'
            ));
        }

        $sinhvien = Sinhvien::where('user_id', Auth::id())->first();
        $thanhviencungphong = collect();
        $kyluatcuaem = collect();
        $hoadonchuathanhtoan = collect();
        $phonghientai = null;
        $taisanphong = collect();

        if ($sinhvien && $sinhvien->phong_id) {
            $phonghientai = Phong::find($sinhvien->phong_id);
            $thanhviencungphong = Sinhvien::where('phong_id', $sinhvien->phong_id)
                ->where('id', '<>', $sinhvien->id)
                ->get();

            $kyluatcuaem = Kyluat::where('sinhvien_id', $sinhvien->id)->orderByDesc('ngayvipham')->limit(5)->get();

            $hoadonchuathanhtoan = Hoadon::where('phong_id', $sinhvien->phong_id)
                ->where('trangthaithanhtoan', 'Chưa thanh toán')
                ->orderByDesc('nam')
                ->orderByDesc('thang')
                ->get();

            $taisanphong = Taisan::where('phong_id', $sinhvien->phong_id)->get();
        }

        $thongbao = Thongbao::query()
            ->where(function ($query) use ($sinhvien) {
                $query->where(function ($subQuery) {
                    $subQuery->where('doituong', 'sinhvien')
                        ->whereNull('phong_id')
                        ->whereNull('sinhvien_id');
                });

                if ($sinhvien && $sinhvien->phong_id) {
                    $query->orWhere(function ($subQuery) use ($sinhvien) {
                        $subQuery->where('doituong', 'sinhvien')
                            ->where('phong_id', $sinhvien->phong_id);
                    });
                }

                if ($sinhvien) {
                    $query->orWhere(function ($subQuery) use ($sinhvien) {
                        $subQuery->where('doituong', 'sinhvien')
                            ->where('sinhvien_id', $sinhvien->id);
                    });
                }
            })
            ->orderByDesc('ngaydang')
            ->limit(5)
            ->get();

        $lienhekhancap = [
            ['title' => 'Bảo vệ', 'phone' => '0900 111 222'],
            ['title' => 'Y tế', 'phone' => '0900 333 444'],
        ];

        return view('student.trangchu', compact(
            'vaitro',
            'thanghientai',
            'namhientai',
            'sinhvien',
            'phonghientai',
            'taisanphong',
            'thanhviencungphong',
            'kyluatcuaem',
            'hoadonchuathanhtoan',
            'lienhekhancap',
            'thongbao'
        ));
    }
}

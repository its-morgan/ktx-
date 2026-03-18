<?php

namespace App\Http\Controllers;

use App\Models\Baohong;
use App\Models\Dangky;
use App\Models\Hoadon;
use App\Models\Phong;
use App\Models\Sinhvien;
use Illuminate\Support\Facades\Auth;

class TrangchuController extends Controller
{
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

        if ($vaitro === 'admin') {
            // Đếm phòng trống dựa theo số sinh viên hiện tại và số lượng tối đa
            $danhsachphong = Phong::all();
            $tongphongtrong = $danhsachphong->filter(function ($phong) {
                $soluonghientai = Sinhvien::where('phong_id', $phong->id)->count();
                return $soluonghientai < (int) $phong->soluongtoida;
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

            // Danh sách việc cần làm (lấy 5 mục gần nhất)
            $danhsachdangkygannhat = Dangky::where('trangthai', 'Chờ xử lý')
                ->orderByDesc('id')
                ->limit(5)
                ->get();

            $danhsachbaohonggannhat = Baohong::where('trangthai', 'Chờ sửa')
                ->orderByDesc('id')
                ->limit(5)
                ->get();
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
                'namhientai'
            ));
        }

        return view('student.trangchu', compact(
            'vaitro',
            'thanghientai',
            'namhientai'
        ));
    }
}

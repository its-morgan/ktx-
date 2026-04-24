<?php

namespace App\Http\Controllers;

use App\Models\Lienhe;
use App\Models\Phong;
use App\Models\Sinhvien;
use App\Models\Thongbao;
use App\Traits\PhanHoiService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class LandingController extends Controller
{
    use PhanHoiService;
    public function index(): View
    {
        // Dùng Phong.dango được sync bởi SinhvienObserver làm source of truth
        $tongCho = \App\Models\Phong::sum('dango');
        
        // Lấy danh sách phòng
        $phongList = Phong::all();

        // Tính thống kê phòng
        $tongPhong = $phongList->count();
        $tongSucChua = $phongList->sum('succhuamax');
        $tongConTrong = $tongSucChua - $tongCho;
        
        // Lấy giá phòng trung bình
        $giaTrungBinh = $phongList->avg('giaphong') ?? 1200000;
        
        // Đếm số phòng hoàn toàn trống (không có sinh viên nào) - dùng query builder
        $phongHoanToanTrong = Phong::whereDoesntHave('danhsachsinhvien')->count();
        
        // Đếm số phòng còn chỗ (dango < succhuamax) - dùng dango field được sync bởi observer
        $phongConCho = Phong::whereColumn('dango', '<', 'succhuamax')->count();

        // Số liệu nổi bật
        $sinhVienDangO = $tongCho;
        $soTang = $phongList->pluck('tang')->unique()->count();

        return view('landing.index', [
            'tongPhong' => $tongPhong,
            'tongCho' => $tongCho,
            'tongConTrong' => $tongConTrong,
            'phongHoanToanTrong' => $phongHoanToanTrong,
            'phongConCho' => $phongConCho,
            'giaTrungBinh' => $giaTrungBinh,
            'sinhVienDangO' => $sinhVienDangO,
            'soTang' => $soTang,
        ]);
    }

    public function guiLienHe(Request $request): RedirectResponse
    {
        $duLieu = $request->validate(
            [
                'ho_ten' => ['required', 'string', 'max:100'],
                'email' => ['required', 'email', 'max:150'],
                'noi_dung' => ['required', 'string', 'max:2000'],
            ],
            [
                'ho_ten.required' => 'Vui lòng nhập họ và tên.',
                'email.required' => 'Vui lòng nhập email liên hệ.',
                'email.email' => 'Email không đúng định dạng.',
                'noi_dung.required' => 'Vui lòng nhập nội dung câu hỏi.',
            ]
        );

        DB::transaction(function () use ($duLieu) {
            Lienhe::create([
                ...$duLieu,
                'trang_thai' => Lienhe::TRANG_THAI_CHUA_XU_LY,
            ]);

            Thongbao::create([
                'tieude' => 'Liên hệ mới từ landing page',
                'noidung' => 'Họ tên: ' . $duLieu['ho_ten'] . ' | Email: ' . $duLieu['email'] . ' | Nội dung: ' . $duLieu['noi_dung'],
                'doituong' => 'admin',
                'ngaydang' => now(),
            ]);
        });

        return redirect()
            ->to(route('home').'#lien-he')
            ->with('lienhe_thanhcong', 'Cảm ơn bạn đã liên hệ. Ban quản lý sẽ phản hồi sớm.');
    }
}

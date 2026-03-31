<?php

use App\Http\Controllers\BaohongController;
use App\Http\Controllers\CauhinhController;
use App\Http\Controllers\DangkyController;
use App\Http\Controllers\HopdongController;
use App\Http\Controllers\HoadonController;
use App\Http\Controllers\KyluatController;
use App\Http\Controllers\PhongController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SinhvienController;
use App\Http\Controllers\ThongbaoController;
use App\Http\Controllers\TrangchuController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dieuhuong');
    }

    return redirect()->route('login');
});

/**
 * Nhóm route cho ADMIN:
 */
Route::prefix('admin')
    ->middleware(['auth', 'kiemtravaitro:admin'])
    ->name('admin.')
    ->group(function () {
        Route::get('/trangchu', [TrangchuController::class, 'hienthi'])->name('trangchu');

        Route::get('/quanlyphong', [PhongController::class, 'danhsachphongquantri'])->name('quanlyphong');
        Route::post('/themphong', [PhongController::class, 'themphong'])->name('themphong');
        Route::post('/capnhatphong/{id}', [PhongController::class, 'capnhatphong'])->name('capnhatphong');
        Route::post('/xoaphong/{id}', [PhongController::class, 'xoaphong'])->name('xoaphong');
        Route::get('/quanlysinhvien', [SinhvienController::class, 'danhsachsinhvien'])->name('quanlysinhvien');
        Route::post('/chuyenphong/{id}', [SinhvienController::class, 'chuyenphongsinhvien'])->name('chuyenphong');
        Route::post('/choroiophong/{id}', [SinhvienController::class, 'choroiophong'])->name('choroiophong');
        Route::post('/capnhatsinhvien/{id}', [SinhvienController::class, 'capnhatsinhvien'])->name('capnhatsinhvien');
        Route::get('/duyetdangky', [DangkyController::class, 'danhsachdangky'])->name('duyetdangky');
        Route::post('/duyetdangky/{id}', [DangkyController::class, 'duyetdangky'])->name('xulyduyetdangky');
        Route::post('/tuchoidangky/{id}', [DangkyController::class, 'tuchoidangky'])->name('xulytuchoidangky');
        Route::get('/quanlyhoadon', [HoadonController::class, 'danhsachhoadonquantri'])->name('quanlyhoadon');
        Route::post('/xulyhoadon', [HoadonController::class, 'xulyhoadon'])->name('xulyhoadon');
        Route::post('/xacnhanthanhtoan/{id}', [HoadonController::class, 'xacnhanthanhtoan'])->name('xacnhanthanhtoan');
        Route::get('/quanlybaohong', [BaohongController::class, 'danhsachbaohongquantri'])->name('quanlybaohong');
        Route::post('/capnhatbaohong/{id}', [BaohongController::class, 'capnhatbaohong'])->name('capnhatbaohong');
        // Quản lý kỷ luật
        Route::get('/quanlykyluat', [KyluatController::class, 'danhsachkyluat'])->name('quanlykyluat');
        Route::post('/them/kyluat', [KyluatController::class, 'themkyluat'])->name('themkyluat');
        Route::post('/capnhat/kyluat/{id}', [KyluatController::class, 'capnhatkyluat'])->name('capnhatkyluat');

        // Quản lý tài sản phòng
        Route::get('/quanlyphong/{id}', [PhongController::class, 'chitietphong'])->name('chitietphong');
        Route::post('/quanlyphong/{id}/themtaisan', [PhongController::class, 'themtaisan'])->name('themtaisan');
        Route::post('/quanlyphong/{id}/capnhattaisan/{taisanId}', [PhongController::class, 'capnhattaisan'])->name('capnhattaisan');
        Route::post('/quanlyphong/{id}/xoataisan/{taisanId}', [PhongController::class, 'xoataisan'])->name('xoataisan');
        Route::get('/quanlycauhinh', [CauhinhController::class, 'index'])->name('quanlycauhinh');
        Route::post('/quanlycauhinh', [CauhinhController::class, 'update'])->name('capnhatcauhinh');
        Route::get('/quanlythongbao', [ThongbaoController::class, 'index'])->name('quanlythongbao');
        Route::post('/quanlythongbao', [ThongbaoController::class, 'store'])->name('themthongbao');
        Route::post('/quanlythongbao/{id}', [ThongbaoController::class, 'update'])->name('capnhatthongbao');
        Route::post('/quanlythongbao/xoa/{id}', [ThongbaoController::class, 'destroy'])->name('xoathongbao');
        // Quản lý hợp đồng
        Route::get('/quanlyhopdong', [HopdongController::class, 'index'])->name('quanlyhopdong');
        Route::post('/hopdong/{id}/giahan', [HopdongController::class, 'giahan'])->name('hopdong.giahan');
        Route::post('/hopdong/{id}/thanhly', [HopdongController::class, 'thanhly'])->name('hopdong.thanhly');
    });

/**
 * Nhóm route cho SINH VIÊN:
 */
Route::prefix('student')
    ->middleware(['auth', 'kiemtravaitro:sinhvien'])
    ->name('student.')
    ->group(function () {
        Route::get('/trangchu', [TrangchuController::class, 'hienthi'])->name('trangchu');
        Route::get('/danhsachphong', [PhongController::class, 'danhsachphong'])->name('danhsachphong');
        Route::post('/dangkyphong', [DangkyController::class, 'themdangky'])->name('dangkyphong');
        Route::post('/yeucautraphong', [DangkyController::class, 'yeucautraphong'])->name('yeucautraphong');
        Route::get('/hoadoncuaem', [HoadonController::class, 'hoadoncuatoi'])->name('hoadoncuaem');
        Route::get('/hopdongcuatoi', [HopdongController::class, 'hopdongcuaem'])->name('hopdongcuatoi');
        Route::get('/baohong', [BaohongController::class, 'danhsachbaohong'])->name('danhsachbaohong');
        Route::post('/baohong', [BaohongController::class, 'thembaohong'])->name('thembaohong');
        Route::get('/taisanphong', [PhongController::class, 'taisanphong'])->name('taisanphong');
        Route::get('/kyluatcuaem', [KyluatController::class, 'kyluatcuaem'])->name('kyluatcuaem');
        Route::get('/thongbao/{id}', [ThongbaoController::class, 'chitiet'])->name('chitietthongbao');
    });

// Các route có sẵn của Breeze (profile)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Trạm điều hướng trung gian
Route::get('/dieuhuong', function () {
    // Lấy vai trò của người dùng vừa đăng nhập
    $vaitro = Auth::user()->vaitro;

    // Nếu là quản trị viên thì đẩy vào khu vực admin
    if ($vaitro === 'admin') {
        return redirect()->route('admin.trangchu');
    }

    // Nếu là sinh viên thì đẩy vào khu vực student
    return redirect()->route('student.trangchu');
})->middleware(['auth'])->name('dieuhuong');

// Route mặc định của Breeze: chuyển về dieuhuong
Route::get('/dashboard', function () {
    return redirect()->route('dieuhuong');
})->middleware(['auth'])->name('dashboard');
require __DIR__.'/auth.php';

<?php

use App\Http\Controllers\BaohongController;
use App\Http\Controllers\DangkyController;
use App\Http\Controllers\HoadonController;
use App\Http\Controllers\PhongController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SinhvienController;
use App\Http\Controllers\TrangchuController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return redirect()->route('login');
});

/**
 * Nhóm route cho ADMIN:
 * - URL luôn bắt đầu bằng /admin/...
 * - Middleware kiểm tra vai trò: kiemtravaitro:admin
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

        Route::get('/duyetdangky', [DangkyController::class, 'danhsachdangky'])->name('duyetdangky');
        Route::post('/duyetdangky/{id}', [DangkyController::class, 'duyetdangky'])->name('xulyduyetdangky');
        Route::post('/tuchoidangky/{id}', [DangkyController::class, 'tuchoidangky'])->name('xulytuchoidangky');

        Route::get('/quanlyhoadon', [HoadonController::class, 'danhsachhoadonquantri'])->name('quanlyhoadon');
        Route::post('/xulyhoadon', [HoadonController::class, 'xulyhoadon'])->name('xulyhoadon');

        Route::get('/quanlybaohong', [BaohongController::class, 'danhsachbaohongquantri'])->name('quanlybaohong');
        Route::post('/capnhatbaohong/{id}', [BaohongController::class, 'capnhatbaohong'])->name('capnhatbaohong');
    });

/**
 * Nhóm route cho SINH VIÊN:
 * - URL luôn bắt đầu bằng /student/...
 * - Middleware kiểm tra vai trò: kiemtravaitro:sinhvien
 */
Route::prefix('student')
    ->middleware(['auth', 'kiemtravaitro:sinhvien'])
    ->name('student.')
    ->group(function () {
        Route::get('/trangchu', [TrangchuController::class, 'hienthi'])->name('trangchu');

        Route::get('/danhsachphong', [PhongController::class, 'danhsachphong'])->name('danhsachphong');
        Route::post('/dangkyphong', [DangkyController::class, 'themdangky'])->name('dangkyphong');

        Route::get('/hoadoncuaem', [HoadonController::class, 'hoadoncuatoi'])->name('hoadoncuaem');

        Route::get('/baohong', [BaohongController::class, 'danhsachbaohong'])->name('danhsachbaohong');
        Route::post('/baohong', [BaohongController::class, 'thembaohong'])->name('thembaohong');
    });

// Các route có sẵn của Breeze (profile)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Trạm điều hướng trung gian: Fix lỗi 404 và phân luồng sau khi đăng nhập
Route::get('/dieuhuong', function () {
    // Lấy vai trò của người dùng vừa đăng nhập
    $vaitro = Auth::user()->vaitro;

    // Nếu là quản trị viên thì đẩy vào khu vực admin
    if ($vaitro === 'admin') {
        return redirect()->route('admin.trangchu');
    }

    // Nếu là sinh viên thì đẩy vào khu vực student
    return redirect()->route('student.trangchu');
})->middleware(['auth']);
require __DIR__.'/auth.php';

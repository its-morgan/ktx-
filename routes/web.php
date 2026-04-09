<?php

use App\Http\Controllers\BaohongController;
use App\Http\Controllers\CauhinhController;
use App\Http\Controllers\CongnoController;
use App\Http\Controllers\DanhgiaController;
use App\Http\Controllers\DangkyController;
use App\Http\Controllers\HopdongController;
use App\Http\Controllers\HoadonController;
use App\Http\Controllers\KyluatController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\LienheController;
use App\Http\Controllers\LichsubaotriController;
use App\Http\Controllers\PhongController;
use App\Http\Controllers\PhongCuaToiController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SinhvienController;
use App\Http\Controllers\ThongbaoController;
use App\Http\Controllers\TrangchuController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', [LandingController::class, 'index'])->name('home');
Route::post('/lien-he', [LandingController::class, 'guiLienHe'])->name('landing.lienhe');

/**
 * ROUTE CÔNG KHAI (Public Access) - Không cần đăng nhập
 * Cho phép khách xem danh sách phòng và vật tư trước khi thuê
 */
Route::get('/phong', [PhongController::class, 'listRoomsPublic'])->name('public.danhsachphong');
Route::get('/phong/{id}/vattu', [PhongController::class, 'viewRoomAssetsPublic'])->name('public.chitietvattu');

/**
 * Nhóm route cho ADMIN:
 */
Route::prefix('admin')
    ->middleware(['auth', 'kiemtravaitro:admin,admin_truong,admin_toanha,le_tan'])
    ->name('admin.')
    ->group(function () {
        Route::get('/trangchu', [TrangchuController::class, 'showAdminDashboard'])->name('trangchu');

        Route::get('/quanlyphong', [PhongController::class, 'listRooms'])->name('quanlyphong');
        Route::post('/themphong', [PhongController::class, 'storeRoom'])->name('themphong');
        Route::post('/capnhatphong/{id}', [PhongController::class, 'updateRoom'])->name('capnhatphong');
        Route::post('/xoaphong/{id}', [PhongController::class, 'destroyRoom'])->name('xoaphong');
        Route::get('/quanlysinhvien', [SinhvienController::class, 'listStudents'])->name('quanlysinhvien');
        Route::post('/chuyenphong/{id}', [SinhvienController::class, 'assignRoom'])->name('chuyenphong');
        Route::post('/choroiophong/{id}', [SinhvienController::class, 'removeFromRoom'])->name('choroiophong');
        Route::post('/capnhatsinhvien/{id}', [SinhvienController::class, 'updateStudent'])->name('capnhatsinhvien');
        Route::get('/duyetdangky', [DangkyController::class, 'listRegistrations'])->middleware('can:dangky.review')->name('duyetdangky');
        Route::post('/duyetdangky/{id}', [DangkyController::class, 'approveRegistration'])->middleware('can:dangky.review')->name('xulyduyetdangky');
        Route::post('/tuchoidangky/{id}', [DangkyController::class, 'rejectRegistration'])->middleware('can:dangky.review')->name('xulytuchoidangky');
        Route::get('/quanlyhoadon', [HoadonController::class, 'listInvoicesAdmin'])->middleware('can:hoadon.manage')->name('quanlyhoadon');
        Route::post('/xulyhoadon', [HoadonController::class, 'processInvoices'])->middleware('can:hoadon.manage')->name('xulyhoadon');
        Route::post('/xacnhanthanhtoan/{id}', [HoadonController::class, 'confirmPayment'])->middleware('can:hoadon.manage')->name('xacnhanthanhtoan');
        Route::get('/hoadon/{id}/pdf', [HoadonController::class, 'downloadInvoicePDF'])->middleware('can:hoadon.manage')->name('hoadon.pdf');
        Route::get('/baocaocongno', [CongnoController::class, 'showArrearage'])->name('baocaocongno');
        Route::post('/guinhacnho/{phongId}', [CongnoController::class, 'sendReminderNotification'])->name('guinhacnho');
        Route::get('/quanlybaohong', [BaohongController::class, 'listMaintenanceRequestsAdmin'])->name('quanlybaohong');
        Route::post('/capnhatbaohong/{id}', [BaohongController::class, 'updateMaintenance'])->name('capnhatbaohong');
        Route::get('/quanlybaotri', [LichsubaotriController::class, 'listMaintenanceHistory'])->name('quanlybaotri');
        // Quản lý kỷ luật
        Route::get('/quanlykyluat', [KyluatController::class, 'listDisciplines'])->middleware('can:kyluat.manage')->name('quanlykyluat');
        Route::post('/them/kyluat', [KyluatController::class, 'storeDiscipline'])->middleware('can:kyluat.manage')->name('themkyluat');
        Route::post('/capnhat/kyluat/{id}', [KyluatController::class, 'updateDiscipline'])->middleware('can:kyluat.manage')->name('capnhatkyluat');

        // Quản lý tài sản phòng
        Route::get('/quanlyphong/{id}', [PhongController::class, 'viewRoom'])->name('chitietphong');
        Route::post('/quanlyphong/{id}/themtaisan', [PhongController::class, 'storeAsset'])->name('themtaisan');
        Route::post('/quanlyphong/{id}/capnhattaisan/{taisanId}', [PhongController::class, 'updateAsset'])->name('capnhattaisan');
        Route::post('/quanlyphong/{id}/xoataisan/{taisanId}', [PhongController::class, 'destroyAsset'])->name('xoataisan');
        Route::post('/quanlyphong/{id}/themvattu', [PhongController::class, 'storeSupply'])->name('themvattu');
        Route::post('/quanlyphong/{id}/capnhatvattu/{vattuId}', [PhongController::class, 'updateSupply'])->name('capnhatvattu');
        Route::post('/quanlyphong/{id}/xoavattu/{vattuId}', [PhongController::class, 'destroySupply'])->name('xoavattu');
        Route::get('/quanlyphong/{id}/danhgia', [DanhgiaController::class, 'listReviews'])->name('phong.danhgia');
        Route::post('/vattu/{id}/baotri', [LichsubaotriController::class, 'storeMaintenanceRecord'])->name('vattu.baotri');
        Route::get('/quanlycauhinh', [CauhinhController::class, 'showSettings'])->middleware('can:cauhinh.manage')->name('quanlycauhinh');
        Route::post('/quanlycauhinh', [CauhinhController::class, 'updateSettings'])->middleware('can:cauhinh.manage')->name('capnhatcauhinh');
        Route::get('/quanlythongbao', [ThongbaoController::class, 'listAnnouncementsAdmin'])->name('quanlythongbao');
        Route::post('/quanlythongbao', [ThongbaoController::class, 'storeAnnouncement'])->name('themthongbao');
        Route::post('/quanlythongbao/xoa/{id}', [ThongbaoController::class, 'destroyAnnouncement'])->whereNumber('id')->name('xoathongbao');
        Route::post('/quanlythongbao/{id}', [ThongbaoController::class, 'updateAnnouncement'])->whereNumber('id')->name('capnhatthongbao');
        Route::get('/quanlylienhe', [LienheController::class, 'listInquiries'])->name('quanlylienhe');
        Route::post('/quanlylienhe/{id}/trangthai', [LienheController::class, 'updateStatus'])->whereNumber('id')->name('capnhattrangthailienhe');
        // Quản lý hợp đồng
        Route::get('/quanlyhopdong', [HopdongController::class, 'listContracts'])->middleware('can:hopdong.manage')->name('quanlyhopdong');
        Route::post('/taohopdong', [HopdongController::class, 'store'])->middleware('can:hopdong.manage')->name('taohopdong');
        Route::post('/hopdong/{id}/giahan', [HopdongController::class, 'extend'])->middleware('can:hopdong.manage')->name('hopdong.giahan');
        Route::post('/hopdong/{id}/thanhly', [HopdongController::class, 'close'])->middleware('can:hopdong.manage')->name('hopdong.thanhly');
        Route::get('/hopdong/{id}/pdf', [HopdongController::class, 'downloadPDF'])->middleware('can:hopdong.manage')->name('hopdong.pdf');
    });

/**
 * Nhóm route cho SINH VIÊN:
 */
Route::prefix('student')
    ->middleware(['auth', 'kiemtravaitro:sinhvien'])
    ->name('student.')
    ->group(function () {
        Route::get('/trangchu', [TrangchuController::class, 'showStudentDashboard'])->name('trangchu');

        // Trang phòng của tôi - Tổng quan
        Route::get('/phongcuatoi', [PhongCuaToiController::class, 'index'])->name('phongcuatoi');
        Route::get('/hoadoncuaem', [HoadonController::class, 'myInvoices'])->name('hoadoncuaem');
        Route::get('/phongcuatoi/hoadon', [HoadonController::class, 'myInvoices'])->name('phongcuatoi.hoadon');
        Route::get('/phongcuatoi/hoadon/{id}', [HoadonController::class, 'viewMyInvoiceDetails'])->name('phongcuatoi.hoadon.chitiet');

        // Danh sách phòng và đăng ký
        Route::get('/danhsachphong', [PhongController::class, 'listStudentRooms'])->name('danhsachphong');
        Route::post('/dangkyphong', [DangkyController::class, 'storeRegistration'])->name('dangkyphong');
        Route::post('/yeucautraphong', [DangkyController::class, 'requestLeaveRoom'])->name('yeucautraphong');
        Route::post('/yeucaudoiphong', [DangkyController::class, 'requestRoomChange'])->name('yeucaudoiphong');

        // Hóa đơn và hợp đồng
        Route::get('/hopdongcuatoi', [HopdongController::class, 'myContracts'])->name('hopdongcuatoi');

        // Báo hỏng và tài sản
        Route::get('/baohong', [BaohongController::class, 'listMaintenanceRequests'])->name('danhsachbaohong');
        Route::post('/baohong', [BaohongController::class, 'storeMaintenance'])->name('thembaohong');
        Route::get('/taisanphong', [PhongController::class, 'studentAssets'])->name('taisanphong');

        // Thông tin cá nhân
        Route::get('/kyluatcuaem', [KyluatController::class, 'myDisciplines'])->name('kyluatcuaem');
        Route::get('/danhgia', [DanhgiaController::class, 'showReviewForm'])->name('danhgia');
        Route::post('/danhgia', [DanhgiaController::class, 'storeReview'])->name('themdanhgia');

        // Thông báo
        Route::get('/thongbao', [ThongbaoController::class, 'listAnnouncements'])->name('thongbao');
        Route::get('/thongbao/{id}', [ThongbaoController::class, 'viewAnnouncement'])->name('chitietthongbao');
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
    if (in_array($vaitro, ['admin', 'admin_truong', 'admin_toanha', 'le_tan'], true)) {
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

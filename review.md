# Báo cáo Toàn diện: Lộ trình từ 73/100 → 100/100
## Hệ thống Quản lý KTX — Backend, Frontend & UX

---

## PHẦN A: LỖI NGHIÊM TRỌNG (CRITICAL — Gây crash/sai logic)

### A1. 🔴 Route gọi method `confirmPayment` không tồn tại → 500 Error

**File:** `routes/web.php` dòng 79, 87

**Lỗi:**
```php
// Dòng 79: DangkyController
Route::post('/xacnhanthanhtoan-dangky/{id}', 'confirmPayment')

// Dòng 87: HoadonController
Route::post('/xacnhanthanhtoan/{id}', 'confirmPayment')
```

Nhưng cả `DangkyController` lẫn `HoadonController` đều KHÔNG có method `confirmPayment`. Chỉ có `xacNhanThanhToan`.

**Hậu quả:** Khi admin bấm "Xác nhận thanh toán" trên trang duyệt đăng ký hoặc hóa đơn → lỗi 500 (Method not found).

**Đề xuất fix:**
```php
// routes/web.php dòng 79
Route::post('/xacnhanthanhtoan-dangky/{id}', 'xacNhanThanhToan')->name('dangky.xacnhanthanhtoan');

// routes/web.php dòng 87
Route::post('/xacnhanthanhtoan/{id}', 'xacNhanThanhToan')->name('xacnhanthanhtoan');
```

---

### A2. 🔴 State Machine ALLOWED_TRANSITIONS không khớp DB values — Luôn trả `false`

**File:** `app/Models/Dangky.php`, `app/Models/Hopdong.php`, `app/Models/Hoadon.php`

**Lỗi chi tiết:**

**Dangky:**
- `ALLOWED_TRANSITIONS` dùng key: `"Chờ xử lý"`, `"Chờ thanh toán"`, `"Đã duyệt"`, `"Hoàn tất"`, `"Từ chối"`
- DB lưu Enum values: `"pending"`, `"approved_pending_payment"`, `"approved"`, `"completed"`, `"rejected"`
- `normalizeState()` chỉ map từ tiếng Việt KHÔNG dấu (VD: `"Cho xu ly"` → `"pending"`) — nhưng DB lưu `"pending"` đi vào `default` case → trả về `"pending"`.
- `"pending"` KHÔNG phải key trong `ALLOWED_TRANSITIONS` → `canTransitionTo()` luôn return `false`!

**Hopdong:**
- `ALLOWED_TRANSITIONS` dùng key: `"Dang hieu luc"`, `"Het han"`, `"Da thanh ly"`
- DB lưu: `"active"`, `"expired"`, `"terminated"`
- Cùng lỗi: `"active"` đi vào `default` → `"active"` không phải key → luôn `false`.

**Hoadon:**
- `ALLOWED_TRANSITIONS` dùng key: `"Chờ xác nhận"`, `"Chưa thanh toán"`, `"Đã thanh toán"`, `"Quá hạn"`
- DB lưu: `"pending_confirmation"`, `"pending"`, `"paid"`
- Cùng lỗi tương tự.

**Hậu quả:** Tất cả method gọi `transitionTo()` và `canTransitionTo()` đều LUÔN thất bại.

Một số method bypass bằng `$model->update(['trangthai' => ...])` trực tiếp nên vẫn hoạt động, nhưng mất ý nghĩa của state machine (không kiểm tra transition hợp lệ).

**Đề xuất fix — Thống nhất ALLOWED_TRANSITIONS dùng Enum values:**

```php
// Dangky.php
private const ALLOWED_TRANSITIONS = [
    'pending' => ['approved_pending_payment', 'approved', 'rejected'],
    'approved_pending_payment' => ['completed', 'rejected'],
    'approved' => ['completed'],
    'completed' => [],
    'rejected' => [],
];

// Bỏ normalizeState() hoặc đơn giản hóa
private function normalizeState(string $state): string
{
    return $state; // DB values đã là source of truth
}
```

Tương tự cho `Hopdong.php` và `Hoadon.php`.

---

### A3. 🔴 Admin Dashboard — CSS inline bị lỗi cú pháp

**File:** `resources/views/admin/trangchu.blade.php`

**Dòng 73:** (Progress bar công suất)
```html
style="---{L{{Day }}%; widh}}v; width: var(--w);ar(--w);"
```
→ Cú pháp Blade bị hỏng, progress bar không hiển thị.

**Dòng 111:** (Biểu đồ doanh thu)
```html
style="ieightght: {{ $h m['label'] }}: {{ number_format($item['value']) }}đ"
```
→ Cú pháp bị hỏng, bar chart không hiển thị.

**Đề xuất fix:**
```html
{{-- Dòng 73 --}}
style="width: {{ $tyLeLapDay }}%"

{{-- Dòng 111 --}}
style="height: {{ $h }}%"
title="{{ $item['label'] }}: {{ number_format($item['value']) }}đ"
```

---

## PHẦN B: LỖI LOGIC NGHIỆP VỤ (HIGH — Sai luồng hoạt động)

### B1. 🟠 Guest flow thiếu tạo hóa đơn thế chân (deposit)

**File:** `app/Services/Admin/DangkyService.php` → method `xacNhanThanhToan()`

**Lỗi:** Khi Guest hoàn tất đăng ký (xác nhận thanh toán), hệ thống tạo User + Sinhvien + Hopdong nhưng KHÔNG tạo hóa đơn thế chân. Trong khi Student flow (`duyetDangKy`) gọi cả `taoHoaDonTheChan()` + `taoHoaDonHangThang()`.

**Đề xuất fix:** Thêm vào method `xacNhanThanhToan()`:
```php
// Sau khi tạo Hopdong
$invoiceService = app(HoadonService::class);
$invoiceService->taoHoaDonTheChan($sinhvien);
$invoiceService->taoHoaDonHangThang($sinhvien, (int)now()->month, (int)now()->year);
```

---

### B2. 🟠 Hóa đơn monthly thiếu `sinhvien_id`

**File:** `app/Services/Admin/HoadonService.php` → method `xuLyHoaDon()`

**Lỗi:** Khi admin tạo hóa đơn hàng tháng qua `xuLyHoaDon()`, chỉ set `phong_id` mà KHÔNG set `sinhvien_id`. Hóa đơn monthly không gắn với sinh viên cụ thể, gây khó khăn khi truy vết.

**Đề xuất fix:** Vì hóa đơn monthly là theo phòng (chia đều cho các SV), nên giữ nguyên `sinhvien_id = null` nhưng cần thêm comment giải thích và đảm bảo UI hiển thị đúng logic "chia đều". Hoặc tạo nhiều hóa đơn, mỗi SV một hóa đơn đã chia.

---

### B3. 🟠 `UserRole` Enum không khớp với thực tế

**File:** `app/Enums/UserRole.php` vs `app/Models/User.php`

**Lỗi:**
- `UserRole` Enum chỉ có 3 giá trị: `admin`, `student`, `manager`
- `User` model khai báo 6 vai trò: `admin`, `admin_truong`, `admin_toanha`, `le_tan`, `sinhvien`, `cuu_sinhvien`
- Enum hoàn toàn vô dụng — không được sử dụng ở bất kỳ đâu.

**Đề xuất fix:**
```php
enum UserRole: string
{
    case Admin = 'admin';
    case AdminTruong = 'admin_truong';
    case AdminToaNha = 'admin_toanha';
    case LeTan = 'le_tan';
    case SinhVien = 'sinhvien';
    case CuuSinhVien = 'cuu_sinhvien';
    
    // ... labels, helpers
}
```
Sau đó dùng Enum trong `User.php` thay cho string constants.

---

### B4. 🟠 `DongBoHopDong` command không được schedule

**File:** `app/Console/Kernel.php`

**Lỗi:** Command `dongbo:hopdong` (đồng bộ hợp đồng từ bảng sinh viên) tồn tại nhưng KHÔNG được schedule trong Kernel. Chỉ có `notifications:prune` được schedule daily.

**Đề xuất fix:**
```php
protected function schedule(Schedule $schedule): void
{
    $schedule->command('notifications:prune')->daily();
    $schedule->command('dongbo:hopdong')->dailyAt('02:00');
}
```

---

### B5. 🟠 Admin Dashboard — Dữ liệu công suất theo tòa là FAKE

**File:** `resources/views/admin/trangchu.blade.php` dòng 26-32

**Lỗi:**
```php
$congSuatTheoToa = collect([
    ['toa' => 'Tòa A', 'value' => min(100, max(0, $tyLeLapDay + 8))],
    ['toa' => 'Tòa B', 'value' => min(100, max(0, $tyLeLapDay + 3))],
    ...
]);
```
Dữ liệu công suất theo tòa chỉ là tỷ lệ tổng +/- một offset cứng. Không phản ánh thực tế.

**Đề xuất fix:** Query thực từ DB:
```php
$congSuatTheoToa = Phong::selectRaw('toa, SUM(dango) as dang_o, SUM(succhuamax) as suc_chua')
    ->groupBy('toa')
    ->get()
    ->map(fn($t) => ['toa' => $t->toa, 'value' => $t->suc_chua > 0 ? round(($t->dang_o / $t->suc_chua) * 100) : 0]);
```

---

### B6. 🟠 Dangky thiếu SoftDeletes

**File:** `app/Models/Dangky.php`

**Lỗi:** Dangky chứa PII (ho_ten, email, so_dien_thoai, so_cccd) nhưng không có SoftDeletes. Nếu xóa đăng ký → mất dữ liệu vĩnh viễn, vi phạm audit trail.

**Đề xuất fix:** Thêm `use SoftDeletes;` vào Dangky model và tạo migration thêm cột `deleted_at`.

---

## PHẦN C: LỖI AN NINH & HIỆU NĂNG (MEDIUM)

### C1. 🟡 Không có Rate Limiting trên routes

**File:** `routes/web.php`

**Lỗi:** Không có `throttle` middleware trên bất kỳ route nào. Đặc biệt nguy hiểm cho:
- Guest đăng ký (`guest.dangky.store`) — có thể spam đăng ký
- Login (`/login`) — brute force
- Liên hệ (`/lienhe`) — spam form

**Đề xuất fix:**
```php
// Guest routes
Route::middleware('throttle:5,1')->group(function () {
    Route::post('/dang-ky-ktx', ...);
    Route::post('/lien-he', ...);
});

// Auth routes
Route::middleware('throttle:10,1')->group(function () {
    Route::post('/login', ...);
});
```

---

### C2. 🟡 Validation messages không có dấu tiếng Việt

**Files:** `LuuPhongRequest.php`, `DuyetDangKyRequest.php`

**Lỗi:** Messages validation dùng tiếng Việt không dấu:
```php
'tenphong.required' => 'Ten phong khong duoc de trong.'
```

**Đề xuất fix:**
```php
'tenphong.required' => 'Tên phòng không được để trống.'
```

---

### C3. 🟡 SinhvienService methods dùng tiếng Anh — Vi phạm STANDARDS.md

**File:** `app/Services/Shared/SinhvienService.php`, `app/Contracts/Shared/SinhvienServiceInterface.php`

**Lỗi:** Methods `listStudents`, `updateStudent`, `assignRoom`, `removeFromRoom`, `terminateActiveContracts` dùng tiếng Anh. STANDARDS.md yêu cầu tiếng Việt camelCase.

**Đề xuất fix:**
```
listStudents → lietKeSinhVien
updateStudent → capNhatSinhVien
assignRoom → xepPhong
removeFromRoom → choRoiOPhong
terminateActiveContracts → chamDutHopDongHienTai
```

---

### C4. 🟡 Thiếu Hợp đồng hết hạn tự động

**Hiện trạng:** Không có scheduled command nào tự động chuyển hợp đồng từ `active` → `expired` khi `ngay_ket_thuc` qua ngày hiện tại.

**Đề xuất fix:** Tạo command `KiemTraHopDongHetHan`:
```php
// Chạy daily
Hopdong::where('trang_thai', ContractStatus::Active->value)
    ->where('ngay_ket_thuc', '<', now())
    ->update(['trang_thai' => ContractStatus::Expired->value]);
```
Và schedule trong Kernel.

---

### C5. 🟡 Gate authorization thiếu nhất quán

**File:** `app/Providers/AuthServiceProvider.php` + routes

**Lỗi:**
- Có Gate cho `dangky.review`, `hopdong.manage`, `hoadon.manage`, `cauhinh.manage`, `kyluat.manage`
- KHÔNG có Gate cho: phòng, sinh viên, thông báo, báo hỏng, bảo trì, liên hệ, công nợ
- Các route này chỉ dùng middleware `kiemtravaitro` mà không có fine-grained authorization

**Đề xuất fix:** Thêm Gate cho các module còn thiếu, hoặc tạo Policy classes.

---

## PHẦN D: LỖI GIAO DIỆN & UX (UI/UX)

### D1. 🟡 Trạng thái đăng ký hiển thị raw Enum value

**File:** `resources/views/admin/dangky/danhsach.blade.php` dòng 81

**Lỗi:**
```html
{{ $dangky->trangthai }}
```
Hiển thị `pending`, `approved_pending_payment` thay vì "Chờ xử lý", "Chờ thanh toán".

**Đề xuất fix:**
```html
{{ \App\Enums\RegistrationStatus::from($dangky->trangthai)->label() }}
```

---

### D2. 🟡 Loại đăng ký hiển thị raw value

**File:** `resources/views/admin/dangky/danhsach.blade.php` dòng 66

**Lỗi:**
```html
{{ $dangky->loaidangky }}
```
Hiển thị `rental` thay vì "Thuê phòng".

**Đề xuất fix:**
```html
{{ \App\Enums\RegistrationType::from($dangky->loaidangky)->label() }}
```

---

### D3. 🟡 Hóa đơn SV — Loại hóa đơn chỉ check 2 loại

**File:** `resources/views/student/trangchu.blade.php` dòng 102-106

**Lỗi:** Chỉ check `dien_nuoc` và fallback. Nhưng hệ thống có 3 loại: `monthly`, `deposit`, `penalty`. Loại `dien_nuoc` không tồn tại trong constants.

**Đề xuất fix:**
```php
@if($hoadon->loai_hoadon === 'monthly')
    // Icon điện nước
@elseif($hoadon->loai_hoadon === 'deposit')
    // Icon tiền cọc
@elseif($hoadon->loai_hoadon === 'penalty')
    // Icon phạt
@endif
```

---

### D4. 🟡 Thiếu loading states / skeleton

**Hiện trạng:** Không có loading spinner hay skeleton screen khi tải dữ liệu. Các trang dùng Blade full-page reload → không cần JS loading. Tuy nhiên, các bảng dài nên có skeleton khi paginate.

**Đề xuất fix:** Thêm Livewire hoặc Alpine.js loading states cho các bảng có pagination. Hoặc tối thiểu là thêm `wire:loading` indicators nếu dùng Livewire.

---

### D5. 🟡 Thiếu xác nhận (confirmation) cho hành động quan trọng

**File:** `resources/views/admin/dangky/danhsach.blade.php` dòng 101-103

**Lỗi:** Có `$dispatch('open-confirm')` nhưng KHÔNG thấy component xử lý event `open-confirm` trong layout. Nếu component confirm dialog chưa tồn tại → nút bấm không có hiệu ứng, form không submit.

**Đề xuất fix:** Đảm bảo có Alpine.js component `confirm-dialog` trong layout `admin.blade.php`.

---

### D6. 🟡 Landing page — SĐT PII hiện trên trang đăng ký công khai

**File:** `resources/views/admin/dangky/danhsach.blade.php` dòng 53

**Lỗi:**
```html
{{ $dangky->so_dien_thoai ?? $dangky->sinhvien?->sodienthoai ?? '' }}
```
SĐT (PII) hiển thị toàn bộ trên trang admin. Nên mask bớt.

**Đề xuất fix:** Mask SĐT: `0912***456` → chỉ hiển thị 4 số đầu + 3 số cuối.

---

### D7. 🟡 Hóa đơn student — hardcode status check

**File:** `resources/views/student/trangchu.blade.php` dòng 116

**Lỗi:**
```php
$isPaid = $hoadon->trangthaithanhtoan === 'paid';
```
Nên dùng Enum thay vì hardcode string.

**Đề xuất fix:**
```php
$isPaid = $hoadon->trangthaithanhtoan === \App\Enums\InvoiceStatus::Paid->value;
```

---

## PHẦN E: LỖI KIẾN TRÚC (ARCHITECTURE)

### E1. 🟡 Tỉ lệ Interface/Class thấp (2.3%)

**Hiện trạng:** 22 Interfaces cho ~129 classes. Mục tiêu >10%.

**Đề xuất fix:** Tạo interfaces cho các services còn thiếu (Core services, Helper services).

---

### E2. 🟡 PhongController — God Class (16 methods)

**File:** `app/Http/Controllers/Admin/PhongController.php`

**Hiện trạng:** Đã inject 5 services riêng biệt (TruyVanPhong, NghiepVuPhong, KhoPhong, TaiSanPhong, VatTuPhong). Nhưng controller vẫn có 16 methods: CRUD phòng + CRUD tài sản + CRUD vật tư + map.

**Đề xuất fix:** Tách thành 3 controllers:
- `PhongController` (index, show, store, update, destroy, map) — 6 methods
- `TaiSanPhongController` (storeAsset, updateAsset, destroyAsset) — 3 methods
- `VatTuPhongController` (storeSupply, updateSupply, destroySupply) — 3 methods

---

### E3. 🟡 Dangky model constants không khớp Enum

**File:** `app/Models/Dangky.php` dòng 19-21

**Lỗi:**
```php
public const LOAI_THUE_PHONG = 'Thuê phòng';
public const LOAI_TRA_PHONG = 'Trả phòng';
public const LOAI_DOI_PHONG = 'Doi phong';
```
- `LOAI_DOI_PHONG` dùng tiếng Việt không dấu `'Doi phong'` trong khi 2 cái kia có dấu.
- Ngoài ra, `RegistrationType` Enum đã có: `Rental = 'rental'`, `Return = 'return'`, `Change = 'change'`. Các constants này là LEGACY code, cần xóa và dùng Enum.

**Đề xuất fix:** Xóa 3 constants, thay bằng `RegistrationType::Rental->value` ở mọi nơi.

---

### E4. 🟡 Hoadon static methods trả về Vietnamese strings, Enum trả English

**File:** `app/Models/Hoadon.php` dòng 25-36

**Lỗi:**
```php
public static function trangThaiChuaThanhToan(): string {
    return 'Chưa thanh toán'; // Vietnamese
}
```
Nhưng `InvoiceStatus::Pending->value` = `'pending'` (English).

→ Hai hệ thống song song, dùng lẫn lộn.

**Đề xuất fix:** Thống nhất dùng Enum everywhere:
```php
public static function trangThaiChuaThanhToan(): string {
    return InvoiceStatus::Pending->value; // 'pending'
}
```

---

## PHẦN F: CẢI TIẾN ĐỂ ĐẠT 100/100

### F1. Thêm Tests (PHPUnit / Pest)

**Hiện trạng:** Không thấy test files cho business logic.

**Đề xuất:**
- Unit tests cho state machine transitions (Dangky, Hopdong, Hoadon)
- Feature tests cho luồng đăng ký (Guest + Student)
- Feature tests cho hóa đơn (tạo, thanh toán, pro-rata)
- Architecture tests (controller max methods, interfaces required)

---

### F2. Thêm API Documentation

**Hiện trạng:** Có cài `l5-swagger` (vendor views), nhưng không rõ đã viết API docs chưa.

**Đề xuất:** Viết OpenAPI annotations cho tất cả routes.

---

### F3. Thêm Job Queue cho email

**Hiện trạng:** Dùng `Mail::to()->queue()` — OK nhưng cần đảm bảo queue worker chạy.

---

### F4. Thêm caching cho dữ liệu landing page

**File:** `app/Services/Core/TrangChuService.php`

**Đề xuất:** Cache `layDuLieuTrangChu()` 5 phút:
```php
return Cache::remember('trang_chu_data', 300, fn() => [/* query */]);
```

---

### F5. Thêm Audit Log cho login/logout

**Hiện trạng:** Chỉ có audit cho CRUD operations qua Observers. Không log login/logout.

---

## TÓM TẮT TÍNH ĐIỂM

| Hạng mục | Trừ điểm hiện tại | Sau khi fix |
|----------|-------------------|-------------|
| God Class (PhongController) | -5 | 0 (tách thành 3 controller) |
| Interface/Class < 10% | -10 | 0 (thêm interfaces) |
| Data Sync (Phong.dango) | -12 | 0 (Observer đã handle, chỉ cần fix approveLeaveRoomLogic) |
| Route 500 errors (confirmPayment) | (chưa tính) | 0 (fix route names) |
| State Machine broken | (chưa tính) | 0 (thống nhất ALLOWED_TRANSITIONS) |
| Dashboard CSS broken | (chưa tính) | 0 (fix inline styles) |
| Missing rate limiting | (chưa tính) | 0 (thêm throttle) |
| Missing SoftDeletes Dangky | (chưa tính) | 0 (thêm SoftDeletes) |
| UserRole Enum mismatch | (chưa tính) | 0 (sync với User model) |
| Missing tests | (chưa tính) | 0 (viết tests) |

---

## ƯU TIÊN FIX (Thứ tự thực hiện)

| # | Việc cần làm | Mức độ | Thời gian ước tính |
|---|-------------|--------|-------------------|
| 1 | Fix route `confirmPayment` → `xacNhanThanhToan` | CRITICAL | 5 phút |
| 2 | Fix State Machine ALLOWED_TRANSITIONS (3 models) | CRITICAL | 30 phút |
| 3 | Fix Admin Dashboard CSS (dòng 73, 111) | CRITICAL | 10 phút |
| 4 | Thêm deposit invoice cho Guest flow | HIGH | 15 phút |
| 5 | Fix UserRole Enum | HIGH | 20 phút |
| 6 | Thêm SoftDeletes cho Dangky | HIGH | 10 phút |
| 7 | Schedule DongBoHopDong command | HIGH | 5 phút |
| 8 | Thêm rate limiting | MEDIUM | 15 phút |
| 9 | Fix validation messages (thêm dấu) | MEDIUM | 20 phút |
| 10 | Fix Blade hiển thị raw enum values | MEDIUM | 15 phút |
| 11 | Rename SinhvienService methods → tiếng Việt | MEDIUM | 20 phút |
| 12 | Tách PhongController | MEDIUM | 30 phút |
| 13 | Thống nhất Hoadon static methods | MEDIUM | 15 phút |
| 14 | Xóa Dangky legacy constants | MEDIUM | 10 phút |
| 15 | Thêm Gate authorization cho modules còn thiếu | MEDIUM | 30 phút |
| 16 | Fix dashboard fake data (congSuatTheoToa) | MEDIUM | 20 phút |
| 17 | Thêm scheduled command kiểm tra HĐ hết hạn | MEDIUM | 20 phút |
| 18 | Mask PII trên admin views | MEDIUM | 15 phút |
| 19 | Thêm cache cho landing page | LOW | 10 phút |
| 20 | Viết tests (Unit + Feature) | LOW (nhưng quan trọng) | 2-3 ngày |
| 21 | Fix route `confirmPenalty` → `xacNhanViPham` (Student) | CRITICAL | 5 phút |
| 22 | Fix Lookup page hardcode Vietnamese status checks | CRITICAL | 15 phút |
| 23 | Thêm xác thực giường đã bị chiếm trước khi đăng ký (Guest) | HIGH | 20 phút |
| 24 | Thêm CTA "Đăng ký ngay" cho SV chưa có phòng (dashboard) | MEDIUM | 15 phút |
| 25 | Thêm bước "Chọn giường" vào SV đăng ký phòng mới | HIGH | 20 phút |
| 26 | Thêm flow xem chi tiết hóa đơn từ Dashboard | MEDIUM | 10 phút |
| 27 | Thêm trang SV xem lịch sử đăng ký | MEDIUM | 30 phút |

---
---

# PHẦN G: PHÂN TÍCH WORKFLOW NGƯỜI DÙNG (User Journey)

> Phần này bổ sung theo yêu cầu — phân tích toàn bộ luồng hoạt động từ góc nhìn người dùng (Guest, Student, Admin), chỉ ra các lỗi/thiếu sót trong từng bước và đề xuất cải thiện.

---

## G1. WORKFLOW GUEST (Khách — Chưa có tài khoản)

### Sơ đồ luồng hiện tại:
```
Landing Page → Xem danh sách phòng → Chọn phòng → Form đăng ký (thông tin + CCCD)
   → Submit → Nhận email với token tra cứu
   → Tra cứu đơn → Xem tiến trình → (Admin duyệt) → Thanh toán → Hoàn tất
```

### G1.1 🔴 CRITICAL: Trang tra cứu so sánh trạng thái bằng Vietnamese string, DB lưu Enum English

**File:** `resources/views/landing/lookup.blade.php` dòng 63, 135-137, 174

**Lỗi:**
```php
// Dòng 63 — Badge hiển thị
$dangky->trangthai === 'Hoàn tất'  // ← DB lưu 'completed', KHÔNG BAO GIỜ true

// Dòng 135 — Progress bar
in_array($dangky->trangthai, ['Đã duyệt', 'Chờ thanh toán', 'Hoàn tất'])
// ← DB lưu 'approved', 'approved_pending_payment', 'completed' → luôn false

// Dòng 174 — Hướng dẫn thanh toán
$dangky->trangthai === 'Chờ thanh toán'  // ← DB lưu 'approved_pending_payment' → luôn false
```

**Hậu quả:**
- Guest tra cứu đơn → Progress bar LUÔN chỉ ở bước 1 "Đã tiếp nhận", dù admin đã duyệt
- Badge trạng thái KHÔNG BAO GIỜ xanh (vì 'Hoàn tất' ≠ 'completed')
- Hướng dẫn thanh toán KHÔNG BAO GIỜ hiển thị (vì 'Chờ thanh toán' ≠ 'approved_pending_payment')
- Guest không biết phải thanh toán → đơn hết hạn → mất quyền đăng ký

**Đề xuất fix:**
```php
// Dòng 63
$dangky->trangthai === \App\Enums\RegistrationStatus::Completed->value

// Dòng 135-137 — Progress bar
$statuses = [
    ['label' => 'Đã tiếp nhận', 'desc' => '...', 'active' => true],
    ['label' => 'Đang thẩm định', 'desc' => '...', 'active' => in_array($dangky->trangthai, [
        RegistrationStatus::Approved->value,
        RegistrationStatus::ApprovedPendingPayment->value,
        RegistrationStatus::Completed->value
    ])],
    ['label' => 'Chờ thanh toán', 'desc' => '...', 'active' => in_array($dangky->trangthai, [
        RegistrationStatus::ApprovedPendingPayment->value,
        RegistrationStatus::Completed->value
    ])],
    ['label' => 'Hoàn tất', 'desc' => '...', 'active' => $dangky->trangthai === RegistrationStatus::Completed->value],
];

// Dòng 174
$dangky->trangthai === RegistrationStatus::ApprovedPendingPayment->value
```

---

### G1.2 🔴 CRITICAL: Route Student `confirmPenalty` không tồn tại → 500 Error

**File:** `routes/web.php` dòng 158

**Lỗi:**
```php
Route::post('/hoadon/{id}/xac-nhan-loi', 'confirmPenalty')->name('hoadon.confirm_penalty');
```
Nhưng `Student\HoadonController` KHÔNG có method `confirmPenalty`. Chỉ có `xacNhanViPham`.

**Hậu quả:** Khi sinh viên xác nhận vi phạm trên hóa đơn kỷ luật → lỗi 500.

**Đề xuất fix:**
```php
Route::post('/hoadon/{id}/xac-nhan-loi', 'xacNhanViPham')->name('hoadon.confirm_penalty');
```

---

### G1.3 🟠 HIGH: Giường đã chọn có thể bị chiếm giữa lúc xem và lúc submit

**File:** `resources/views/landing/dangky.blade.php` dòng 64 + `app/Services/Admin/DangkyService.php`

**Lỗi:**
Khi Guest chọn phòng → form hiển thị dropdown giường từ 1 đến `succhuamax`. Không kiểm tra giường nào đã bị đăng ký bởi Guest khác hoặc đã có SV ở. Nhiều Guest có thể chọn cùng 1 giường → race condition.

**Đề xuất fix:**
- Trong `layDuLieuFormDangKyKhach()`: query danh sách giường đã bị chiếm (qua `Dangky` pending + `Sinhvien` đang ở)
- Chỉ hiển thị giường còn trống trong dropdown
- Validate lại tại `store()` để double-check

---

### G1.4 🟡 MEDIUM: Form đăng ký Guest thiếu feedback thành công rõ ràng

**File:** `app/Http/Controllers/Guest/DangkyController.php` dòng 56

**Lỗi:** Sau khi submit form thành công, `redirect()->back()` với flash message. Guest quay lại form cũ → không rõ đã đăng ký xong chưa. Không hiển thị token tra cứu, không redirect đến trang xác nhận.

**Đề xuất fix:**
- Redirect đến trang tra cứu với token: `redirect()->route('guest.lookup', ['token' => $token])`
- Hoặc hiển thị trang "Đăng ký thành công" với token + QR code thanh toán

---

### G1.5 🟡 MEDIUM: Hiển thị PII đầy đủ trên trang tra cứu công khai

**File:** `resources/views/landing/lookup.blade.php` dòng 79, 86, 93

**Lỗi:** Trang tra cứu (chỉ cần token) hiển thị đầy đủ:
- Họ tên: `{{ $dangky->ho_ten }}`
- Email: `{{ $dangky->email }}`
- SĐT: `{{ $dangky->so_dien_thoai }}`

Bất kỳ ai có token (8 chars hex) đều đọc được toàn bộ PII.

**Đề xuất fix:**
- Mask PII: `Nguyễn ***` , `n***@email.com`, `091***456`
- Hoặc yêu cầu xác thực thêm (nhập 4 số cuối CCCD) trước khi hiển thị chi tiết

---

### G1.6 🟡 MEDIUM: Trang tra cứu — trạng thái "Từ chối" không có hướng dẫn

**File:** `resources/views/landing/lookup.blade.php`

**Lỗi:** Khi đơn bị từ chối, trang chỉ hiển thị badge "Từ chối" nhưng KHÔNG có:
- Lý do từ chối (`ghichu` từ admin)
- Hướng dẫn tiếp theo (đăng ký lại? liên hệ ai?)

**Đề xuất fix:** Thêm block hiển thị khi trạng thái = rejected:
```html
@if($dangky->trangthai === RegistrationStatus::Rejected->value)
    <div class="bg-rose-50 border border-rose-200 p-6">
        <h5>Lý do từ chối</h5>
        <p>{{ $dangky->ghichu ?? 'Không có ghi chú.' }}</p>
        <a href="{{ route('guest.dangky.create', ['phong_id' => $dangky->phong_id]) }}">Đăng ký lại →</a>
    </div>
@endif
```

---

## G2. WORKFLOW STUDENT (Sinh viên đã có tài khoản)

### Sơ đồ luồng hiện tại:
```
Đăng nhập → Dashboard (KPI cards: phòng, tài chính, trạng thái)
   ├── Xem hóa đơn → Chi tiết hóa đơn
   ├── Phòng của tôi → Thành viên + tài sản + yêu cầu đổi/trả phòng
   ├── Đăng ký phòng (nếu chưa có) → Xem danh sách phòng → Gửi đăng ký
   ├── Báo hỏng → Gửi yêu cầu mới → Theo dõi tiến trình
   ├── Kỷ luật → Xem lịch sử vi phạm
   ├── Đánh giá → Gửi đánh giá phòng
   ├── Hợp đồng → Xem hợp đồng hiện tại
   └── Thông báo → Xem chi tiết
```

### G2.1 🟠 HIGH: SV đăng ký phòng mới → không chọn được giường

**File:** `resources/views/student/phong/danhsach.blade.php` dòng 69-76

**Lỗi:** Khi SV (chưa có phòng) xem danh sách phòng trống → bấm "Gửi đăng ký", form chỉ gửi `phong_id` mà KHÔNG có `giuong_no`. Khác với Guest flow có dropdown chọn giường.

**Hậu quả:** Đăng ký phòng của SV thiếu thông tin giường → admin phải tự gán → thêm bước thủ công.

**Đề xuất fix:**
- Thêm modal/dropdown chọn giường (giống Guest flow)
- Hoặc tạo trang riêng `student/dangky/{phong_id}` với full form chọn giường

---

### G2.2 🟠 HIGH: Dashboard SV chưa có phòng — thiếu CTA rõ ràng

**File:** `resources/views/student/trangchu.blade.php` dòng 45

**Lỗi:** Khi SV chưa được xếp phòng:
- Card phòng hiển thị "Chờ xếp" nhưng KHÔNG có link/button đến trang đăng ký phòng
- SV phải tự tìm menu "Đăng ký phòng" trong sidebar

**Đề xuất fix:**
```html
@if(!$phonghientai)
    <a href="{{ route('student.danhsachphong') }}" class="mt-2 text-xs font-bold text-brand-emerald">
        Đăng ký phòng ngay →
    </a>
@endif
```

---

### G2.3 🟡 MEDIUM: Hóa đơn trên Dashboard không có link chi tiết

**File:** `resources/views/student/trangchu.blade.php` dòng 98-128

**Lỗi:** Mỗi hóa đơn trong danh sách gần nhất KHÔNG có link/button để xem chi tiết hoặc thanh toán. Phải bấm "Tất cả" rồi tìm lại trong danh sách đầy đủ.

**Đề xuất fix:** Wrap mỗi item bằng `<a>` link đến chi tiết:
```html
<a href="{{ route('student.phongcuatoi.hoadon.chitiet', $hoadon->id) }}" class="group flex items-center ...">
```

---

### G2.4 🟡 MEDIUM: Hóa đơn check loại `dien_nuoc` nhưng DB không có loại này

**File:** `resources/views/student/trangchu.blade.php` dòng 102, 109

**Lỗi:**
```php
$hoadon->loai_hoadon === 'dien_nuoc'    // ← Không tồn tại trong InvoiceType Enum
$hoadon->loai_hoadon === 'dien_nuoc' ? 'Tiền điện nước' : 'Phí cư trú'   // fallback sai
```
Hệ thống có 3 loại hóa đơn: `monthly`, `deposit`, `penalty`. `'dien_nuoc'` KHÔNG tồn tại.

**Hậu quả:** Mọi hóa đơn đều fallback thành "Phí cư trú" vì điều kiện `dien_nuoc` luôn false.

**Đề xuất fix:**
```php
@switch($hoadon->loai_hoadon)
    @case('monthly')  Tiền phòng + dịch vụ @break
    @case('deposit')  Tiền thế chân @break
    @case('penalty')  Phạt kỷ luật @break
    @default Khác
@endswitch
```

---

### G2.5 🟡 MEDIUM: SV trả phòng — không thấy tiến trình sau khi gửi yêu cầu

**File:** Student DangkyController → `yeuCauTraPhong()`

**Lỗi:** Khi SV gửi yêu cầu trả phòng, chỉ nhận flash message "Gửi yêu cầu trả phòng thành công." rồi quay lại trang cũ. Không có:
- Trang theo dõi tiến trình yêu cầu trả phòng
- Danh sách "Đăng ký của tôi" để xem status

**Đề xuất fix:** Tạo trang `student/dangky/lichsu` hiển thị tất cả đơn đăng ký (thuê phòng, trả phòng, đổi phòng) với status.

---

### G2.6 🟡 MEDIUM: SV đổi phòng — validate trùng phòng hiện tại nhưng UX không rõ

**File:** `app/Services/Admin/DangkyService.php` → `yeuCauDoiPhong()` dòng ~130

**Lỗi:** Backend validate `phong_id_moi == sinhvien->phong_id` → trả về "Bạn đã ở phòng này." Tuy nhiên, trong form đổi phòng, dropdown hiển thị TẤT CẢ phòng kể cả phòng hiện tại → SV chọn nhầm phòng mình đang ở → nhận lỗi mơ hồ.

**Đề xuất fix:**
- Ẩn phòng hiện tại khỏi dropdown (`WHERE id != :phong_id_hien_tai`)
- Hoặc hiển thị "(Phòng hiện tại)" bên cạnh tên phòng và disable option

---

### G2.7 🟡 MEDIUM: Phòng của tôi — Empty state cho SV chưa có phòng rất tốt

**File:** `resources/views/student/phongcuatoi/index.blade.php` dòng 6-57

**Nhận xét tích cực:** Page "Phòng của tôi" có empty state rất tốt — hiển thị danh sách phòng gợi ý với nút "Gửi đăng ký" ngay bên trong. Đây là UX pattern tốt, nên áp dụng cho các trang khác.

---

## G3. WORKFLOW ADMIN (Quản trị viên)

### Sơ đồ luồng hiện tại:
```
Đăng nhập → Admin Dashboard (KPI: công suất, doanh thu, đăng ký chờ, sự cố)
   ├── Duyệt đăng ký → Duyệt / Từ chối / XN Thanh toán
   ├── Quản lý phòng → CRUD phòng + tài sản + vật tư + sơ đồ
   ├── Quản lý SV → Chuyển phòng / Cho rời ở
   ├── Quản lý hóa đơn → Tạo hóa đơn hàng tháng / XN thanh toán
   ├── Báo cáo công nợ → Gửi nhắc nhở
   ├── Quản lý báo hỏng → Cập nhật trạng thái
   ├── Quản lý bảo trì → CRUD lịch sử bảo trì
   ├── Quản lý kỷ luật → Thêm / Sửa vi phạm
   ├── Quản lý hợp đồng → Tạo / Gia hạn / Thanh lý
   ├── Quản lý thông báo → CRUD
   ├── Quản lý liên hệ → Cập nhật trạng thái
   └── Cấu hình → Đơn giá điện/nước/phí
```

### G3.1 🔴 CRITICAL: Admin bấm "XN Tiền" / "XN Thanh toán" → 500 Error

**Đã nêu ở A1.** Route `confirmPayment` không khớp method `xacNhanThanhToan` ở cả `DangkyController` (dòng 79) và `HoadonController` (dòng 87).

**Impact trên workflow:**
- Luồng "Duyệt đăng ký → Duyệt hồ sơ → XN Thanh toán → Hoàn tất" bị CHẶN ở bước cuối
- Luồng "Quản lý hóa đơn → XN Thanh toán" bị CHẶN
- Cả hai workflow quan trọng nhất đều bị block

---

### G3.2 🟠 HIGH: Admin duyệt đăng ký — luồng 2 bước không nhất quán

**File:** `app/Services/Admin/DangkyService.php`

**Luồng hiện tại:**
```
1. Admin bấm "Duyệt" → DangkyService::duyetDangKy()
   → Tạo User + Sinhvien + Hopdong + Hóa đơn
   → Trạng thái Đăng ký: pending → approved
   → Gửi email thông báo

2. Admin bấm "Duyệt hồ sơ" → DangkyService::duyetHoSo()
   → Trạng thái: pending → approved_pending_payment
   → Set expires_at
   → Gửi email yêu cầu thanh toán
```

**Vấn đề:** Cả 2 luồng đều chuyển từ `pending`, nhưng:
- `duyetDangKy()` chuyển thẳng `pending → approved` (skip bước thanh toán)
- `duyetHoSo()` chuyển `pending → approved_pending_payment` (yêu cầu thanh toán)

Admin có 2 nút khác nhau cho 2 luồng nhưng không rõ khi nào dùng cái nào. UI hiển thị nút "Duyệt" khi `pending` nhưng KHÔNG có nút "Duyệt hồ sơ" trên danh sách → route `duyethoso` tồn tại nhưng không có UI để gọi.

**Đề xuất fix:**
- Thống nhất thành 1 luồng: `pending → approved_pending_payment → completed`
- Bỏ `duyetDangKy()` (chuyển thẳng approved), chỉ dùng `duyetHoSo()` → `xacNhanThanhToan()`
- Hoặc thêm nút "Duyệt hồ sơ" trên UI và giải thích rõ sự khác biệt

---

### G3.3 🟠 HIGH: Admin duyệt trả phòng — `Phong.dango` không giảm

**Đã nêu ở phần trước (B mục cũ).** Khi `approveLeaveRoomLogic()` set `phong_id = null`, Observer SinhvienObserver sẽ fire `updating` event → gọi `syncRoomOccupancy()` cho **old** phong_id. 

Tuy nhiên, `syncRoomOccupancy()` đếm `Sinhvien::where('phong_id', $roomId)->count()`. Vấn đề: tại thời điểm Observer fires, `$sinhvien->phong_id` đã = null (dirty attribute), nhưng chưa commit DB. Cần kiểm tra Observer có dùng `$sinhvien->getOriginal('phong_id')` hay không.

**File check:** `app/Observers/SinhvienObserver.php` dòng 43-50

```php
public function updating(Sinhvien $sinhvien): void {
    $phongCuId = (int) ($sinhvien->getOriginal('phong_id') ?? 0);
    $phongMoiId = (int) ($sinhvien->phong_id ?? 0);
    // ...
    $this->capNhatMatDoPhong($phongCuId, $phongMoiId);
}
```

→ Observer CÓ dùng `getOriginal()`. Nhưng `capNhatMatDoPhong` trong Observer gọi `$this->syncRoomOccupancy()` → count Sinhvien in DB. Tại thời điểm Observer fires (trước khi DB commit), DB vẫn chưa update → count CHƯA giảm → `dango` CHƯA giảm.

**Kết luận:** Observer gọi sync TRƯỚC khi DB commit → dango vẫn bằng giá trị cũ. Sync chỉ đúng SAU khi commit.

**Đề xuất fix:** Chuyển sync logic sang `updated` event (thay vì `updating`) hoặc dùng `DB::afterCommit()`:
```php
public function updated(Sinhvien $sinhvien): void {
    $phongCuId = (int) ($sinhvien->getOriginal('phong_id') ?? 0);
    $phongMoiId = (int) ($sinhvien->phong_id ?? 0);
    if ($phongCuId !== $phongMoiId) {
        $this->syncRoomOccupancy($phongCuId);
        $this->syncRoomOccupancy($phongMoiId);
    }
}
```

---

### G3.4 🟡 MEDIUM: Dashboard doanh thu bar chart bị hỏng CSS

**Đã nêu ở A3.** Dòng 111:
```html
style="ieightght: {{ $h m['label'] }}: {{ number_format($item['value']) }}đ"
```
Bar chart 6 tháng không render → admin không xem được xu hướng doanh thu.

---

### G3.5 🟡 MEDIUM: Admin quản lý hóa đơn — thiếu filter theo trạng thái

**File:** `app/Services/Admin/HoadonService.php` dòng 33

**Lỗi:** Trang hóa đơn admin chỉ hiển thị tất cả, sắp theo ngày tạo mới nhất, KHÔNG có filter theo:
- Trạng thái thanh toán (chưa trả / đã trả / quá hạn)
- Loại hóa đơn (monthly / deposit / penalty)
- Tháng/năm cụ thể

**Đề xuất fix:** Thêm filter tabs giống trang duyệt đăng ký (`RegistrationStatus` tabs).

---

### G3.6 🟡 MEDIUM: Admin quản lý SV — cho rời ở nhưng không kiểm tra hóa đơn

**File:** `app/Http/Controllers/Admin/SinhvienController.php` → `choRoiOPhong()`

**Lỗi:** Admin có thể cho SV rời ở ngay cả khi SV còn hóa đơn chưa thanh toán. Không có validation/cảnh báo.

**Đề xuất fix:** Check hóa đơn outstanding trước khi cho rời:
```php
$hoadonChuaTra = Hoadon::where('phong_id', $sinhvien->phong_id)
    ->where('trangthaithanhtoan', InvoiceStatus::Pending->value)
    ->exists();
if ($hoadonChuaTra) {
    return back()->with('toast_loai', 'canh_bao')
        ->with('toast_noidung', 'SV còn hóa đơn chưa thanh toán. Vui lòng xử lý trước.');
}
```

---

### G3.7 🟡 MEDIUM: Private files chỉ Admin truy cập — SV không xem được CCCD của mình

**File:** `routes/web.php` dòng 202-205

**Lỗi:**
```php
Route::get('/private-files/{path}', [FileController::class, 'showPrivateFile'])
    ->middleware(['auth', 'kiemtravaitro:admin,admin_truong,admin_toanha,le_tan'])
```
Middleware chỉ cho phép admin roles. Nếu SV muốn xem ảnh CCCD/ảnh thẻ đã upload → bị 403.

**Đề xuất fix:** Thêm logic authorization: SV chỉ xem được file của chính mình:
```php
// Thêm 'sinhvien' vào middleware
->middleware(['auth', 'kiemtravaitro:admin,admin_truong,admin_toanha,le_tan,sinhvien'])
// Trong FileController: check ownership
```

---

## G4. WORKFLOW CHÉO (Cross-Role)

### G4.1 🟠 HIGH: Không có flow hoàn chỉnh Guest → Student

**Vấn đề:** Khi Guest đăng ký và admin duyệt:
1. `duyetDangKy()` tạo User account với random password → gửi email
2. Email chứa tài khoản + mật khẩu để SV đăng nhập

**Thiếu:**
- Không có flow đổi mật khẩu bắt buộc lần đầu đăng nhập
- Không có onboarding guide cho SV mới (hướng dẫn tính năng)
- Guest sau khi hoàn tất → tra cứu chỉ hiện "Hoàn tất" nhưng không có link đăng nhập

**Đề xuất fix:**
- Trong trang tra cứu khi status = `completed`: hiển thị link đăng nhập + tài khoản
- Middleware kiểm tra `is_first_login` → redirect đổi mật khẩu
- Tạo trang onboarding với quick tour

---

### G4.2 🟡 MEDIUM: Thông báo không real-time

**Hiện trạng:** Thông báo dùng Laravel Notifications + database driver. SV phải refresh trang để xem thông báo mới.

**Đề xuất fix:**
- Polling: JS `setInterval` mỗi 30s kiểm tra thông báo mới
- Hoặc: Laravel Echo + Pusher/Soketi cho real-time

---

### G4.3 🟡 MEDIUM: Cựu sinh viên bị block mọi route Student

**File:** `routes/web.php` dòng 145

**Lỗi:**
```php
->middleware(['auth', 'kiemtravaitro:sinhvien'])
```
Cựu sinh viên (`cuu_sinhvien`) KHÔNG có quyền truy cập bất kỳ route Student nào. Nhưng trong `trangchu.blade.php` có check `$isAlumni` và hiển thị "Cựu sinh viên".

**Hậu quả:** Cựu SV đăng nhập → redirect đến `/student/trangchu` → bị 403 vì middleware check `sinhvien` mà role là `cuu_sinhvien`.

**Đề xuất fix:**
```php
->middleware(['auth', 'kiemtravaitro:sinhvien,cuu_sinhvien'])
```
Và thêm read-only restrictions cho cuu_sinhvien (chỉ xem, không đăng ký/báo hỏng).

---

## TÓM TẮT WORKFLOW ISSUES

| # | Lỗi | Severity | Nhóm | Bước bị ảnh hưởng |
|---|------|----------|------|-------------------|
| G1.1 | Lookup page hardcode Vietnamese status | CRITICAL | Guest | Tra cứu đơn |
| G1.2 | Route `confirmPenalty` 500 error | CRITICAL | Student | Xác nhận vi phạm |
| G1.3 | Race condition giường chưa validate | HIGH | Guest | Đăng ký phòng |
| G1.4 | Form submit → redirect back thay vì confirmation | MEDIUM | Guest | Đăng ký phòng |
| G1.5 | PII lộ trên trang tra cứu | MEDIUM | Guest | Tra cứu đơn |
| G1.6 | Từ chối → không có lý do + hướng dẫn | MEDIUM | Guest | Tra cứu đơn |
| G2.1 | SV đăng ký phòng thiếu chọn giường | HIGH | Student | Đăng ký phòng |
| G2.2 | Dashboard SV chưa có phòng thiếu CTA | MEDIUM | Student | Dashboard |
| G2.3 | Hóa đơn dashboard không có link chi tiết | MEDIUM | Student | Dashboard |
| G2.4 | Check loại hóa đơn `dien_nuoc` không tồn tại | MEDIUM | Student | Dashboard |
| G2.5 | Không có trang theo dõi yêu cầu trả/đổi phòng | MEDIUM | Student | Trả/đổi phòng |
| G2.6 | Dropdown đổi phòng hiện cả phòng hiện tại | MEDIUM | Student | Đổi phòng |
| G3.1 | Route confirmPayment 500 (= A1) | CRITICAL | Admin | Duyệt + Hóa đơn |
| G3.2 | 2 luồng duyệt không nhất quán | HIGH | Admin | Duyệt đăng ký |
| G3.3 | dango sync sai timing (updating vs updated) | HIGH | Admin | Duyệt trả phòng |
| G3.4 | Dashboard chart CSS hỏng (= A3) | MEDIUM | Admin | Dashboard |
| G3.5 | Trang hóa đơn thiếu filter | MEDIUM | Admin | Hóa đơn |
| G3.6 | Cho rời ở không check hóa đơn | MEDIUM | Admin | Quản lý SV |
| G3.7 | Private files block SV | MEDIUM | Student | Xem file |
| G4.1 | Guest→Student flow thiếu onboarding | HIGH | Cross | Chuyển đổi role |
| G4.2 | Thông báo không real-time | MEDIUM | Cross | Mọi vai trò |
| G4.3 | Cựu SV bị 403 tất cả routes | MEDIUM | Cross | Cựu SV login |

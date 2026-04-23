# Standards & Known Issues

## Naming Conventions

| Controllers | PascalCase + Controller | `SinhvienController` |
| Models | **Tiếng Việt** (PascalCase, singular) | `Sinhvien`, `Phong`, `Hopdong` |
| Variables/Methods | **Tiếng Việt** (camelCase) | `$tuKhoa`, `$danhSachPhong` |
| Database Tables | **Tiếng Việt** (snake_case, singular) | `sinhvien`, `phong` |
| Enums (Strict) | **Tiếng Anh** (PascalCase) | `ContractStatus`, `RegistrationType` (Bắt buộc dùng English để chuẩn hóa logic) |
| Views | kebab-case folders | `admin/sinhvien/danhsach.blade.php` |
| Observers | PascalCase | `SinhvienObserver` |
## UI & Localization Rules

- **Code DNA:** Giữ nguyên phong cách **Tiếng Việt** cho Models, Variables và DB Tables. Chỉ dùng **Tiếng Anh** cho Enums.
- **Giao diện người dùng (Blade templates):** Tiếng Việt **có dấu**, giọng chuyên nghiệp
- **Styling:** Tailwind utility classes ưu tiên, kết hợp Flowbite components
- **Design tokens:** Xem `.planning/CONTEXT.md` — font Quicksand, màu Indigo

## Nếp gấp kiến trúc (Phát hiện từ GitNexus)
***Dành cho AI Agent làm việc:***
- **Linguistic Strategy:** Dự án sử dụng mô hình "Hybrid": Logic điều hướng/trạng thái (Enums) dùng English, Nghiệp vụ thực thể (Models/Variables) dùng Vietnamese.
- **Duplicate Logic Checks:** Luôn chạy `gitnexus_query("concept")` để tránh tạo trùng các Class trùng tên khác ngôn ngữ (e.g., Student vs Sinhvien).

## Patterns

- **Validation:** Trong Controller methods hoặc Form Request classes
- **Business Logic:** Dùng Observers (không nhét vào Controller)
- **Route Groups:** Nhóm theo middleware role (`admin`, `sinhvien`)
- **Auth Guards:** Middleware `ThanhVienQT` (admin) + `auth` (student)

## Known Issues & Technical Debt

| Vấn đề | Mức độ | Ghi chú |
|--------|--------|---------|
| `repomix-output.xml` tracked trong Git | Thấp | Chỉ tạo khi cần, không commit thường |
| GitNexus index có thể stale | Thấp | Chạy `npx gitnexus analyze` sau khi commit |
| gsd-sdk CLI `MODULE_NOT_FOUND` | Thấp | Agent tự thực thi thủ công GSD protocols |
| Phân mảnh ngôn ngữ (Enums/Class) | Rất Cao | Hệ thống mix English (`UserRole`) và Vietnamese (`LoaiDangKy`). Cần làm sạch ở Phase tiếp theo. |
| Trùng lặp Observers | Cao | Tồn tại cả `StudentObserver` và `SinhvienObserver`. Cần audit để tránh data loop. |

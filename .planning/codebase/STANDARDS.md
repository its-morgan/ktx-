# Standards & Known Issues

## Naming Conventions

| Controllers | PascalCase + Controller | `SinhvienController` |
| Models | PascalCase, singular | `Phong`, `HopDong` |
| Views | kebab-case folders | `admin/sinhvien/danhsach.blade.php` |
| Variables/Methods | camelCase | `$soPhong`, `getDanhSach()` |
| Database Tables | snake_case, plural | `sinh_viens`, `hop_dongs` |
| Enums (Strict) | **Luôn dùng English** | `UserRole`, `ContractStatus` (TUYỆT ĐỐI CẤM dùng tiếng Việt như `LoaiDangKy` để tránh phân mảnh) |
| Observers (Strict)| Tương ứng 1-1 với Model | CẤM tạo Observer trùng lặp concept (e.g. `StudentObserver` vs `SinhvienObserver`) |
## UI & Localization Rules

- **Code (classes, variables, DB):** Tiếng Anh
- **Giao diện người dùng (Blade templates):** Tiếng Việt **có dấu**, giọng chuyên nghiệp
- **Styling:** Tailwind utility classes ưu tiên, kết hợp Flowbite components
- **Design tokens:** Xem `.planning/CONTEXT.md` — font Quicksand, màu Indigo

## Nếp gấp kiến trúc (Phát hiện từ GitNexus)
***Dành cho AI Agent làm việc:***
- **Linguistic Fragmentation:** Hệ thống đang bị phân mảnh Enum (`ContractStatus` vs `LoaiDangKy`). Khi sinh code mới, phải ƯU TIÊN tìm và dùng hoặc tạo Enum tiếng Anh. Trưởng nhóm sẽ tiến hành refactor các class tiếng Việt dần dần.
- **Duplicate Logic Checks:** Dự án có sự nhập nhằng giữa tiếng Anh và tiếng Việt trong tên bảng/tên file (e.g., Sinhvien vs Student). Phải luôn chạy `gitnexus_query("concept")` trước khi tạo Model/Observer/Controller mới để tránh làm duplicate tính năng.

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

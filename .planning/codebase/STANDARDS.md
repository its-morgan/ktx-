# Standards & Known Issues

## Naming Conventions

| Element | Convention | Example |
|---------|-----------|---------|
| Controllers | PascalCase + Controller | `SinhvienController` |
| Models | PascalCase, singular | `Phong`, `HopDong` |
| Views | kebab-case folders | `admin/sinhvien/danhsach.blade.php` |
| Variables/Methods | camelCase | `$soPhong`, `getDanhSach()` |
| Database Tables | snake_case, plural | `sinh_viens`, `hop_dongs` |
| Enums | PascalCase | `LoaiDangKy`, `TrangThaiPhong` |

## UI & Localization Rules

- **Code (classes, variables, DB):** Tiếng Anh
- **Giao diện người dùng (Blade templates):** Tiếng Việt **có dấu**, giọng chuyên nghiệp
- **Styling:** Tailwind utility classes ưu tiên, kết hợp Flowbite components
- **Design tokens:** Xem `.planning/CONTEXT.md` — font Quicksand, màu Indigo

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
| Vietnamese UI chưa đồng nhất 100% | Trung bình | Phase 2 ROADMAP sẽ audit toàn bộ |

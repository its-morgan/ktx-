---
name: feature
description: "Skill for the Feature area of hethongquanlyktx. 6 symbols across 1 files."
---

# Feature

6 symbols | 1 files | Cohesion: 73%

## When to Use

- Working with code in `tests/`
- Understanding how taoAdmin, taoSinhVienVaPhong, test_admin_duyet_dangky_tao_hopdong work
- Modifying feature-related functionality

## Key Files

| File | Symbols |
|------|---------|
| `tests/Feature/HopdongTest.php` | taoAdmin, taoSinhVienVaPhong, test_admin_duyet_dangky_tao_hopdong, test_admin_giahan_hopdong, test_admin_thanhly_hopdong_va_giai_phong (+1) |

## Entry Points

Start here when exploring this area:

- **`taoAdmin`** (Method) — `tests/Feature/HopdongTest.php:16`
- **`taoSinhVienVaPhong`** (Method) — `tests/Feature/HopdongTest.php:26`
- **`test_admin_duyet_dangky_tao_hopdong`** (Method) — `tests/Feature/HopdongTest.php:54`
- **`test_admin_giahan_hopdong`** (Method) — `tests/Feature/HopdongTest.php:86`
- **`test_admin_thanhly_hopdong_va_giai_phong`** (Method) — `tests/Feature/HopdongTest.php:117`

## Key Symbols

| Symbol | Type | File | Line |
|--------|------|------|------|
| `taoAdmin` | Method | `tests/Feature/HopdongTest.php` | 16 |
| `taoSinhVienVaPhong` | Method | `tests/Feature/HopdongTest.php` | 26 |
| `test_admin_duyet_dangky_tao_hopdong` | Method | `tests/Feature/HopdongTest.php` | 54 |
| `test_admin_giahan_hopdong` | Method | `tests/Feature/HopdongTest.php` | 86 |
| `test_admin_thanhly_hopdong_va_giai_phong` | Method | `tests/Feature/HopdongTest.php` | 117 |
| `test_admin_chuyen_phong_cap_nhat_hopdong_da_thanh_ly` | Method | `tests/Feature/HopdongTest.php` | 148 |

## Connected Areas

| Area | Connections |
|------|-------------|
| Controllers | 6 calls |

## How to Explore

1. `gitnexus_context({name: "taoAdmin"})` — see callers and callees
2. `gitnexus_query({query: "feature"})` — find related execution flows
3. Read key files listed above for implementation details

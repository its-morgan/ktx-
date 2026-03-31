---
name: controllers
description: "Skill for the Controllers area of hethongquanlyktx. 35 symbols across 24 files."
---

# Controllers

35 symbols | 24 files | Cohesion: 93%

## When to Use

- Working with code in `app/`
- Understanding how TrangchuController, ThongbaoController, SinhvienController work
- Modifying controllers-related functionality

## Key Files

| File | Symbols |
|------|---------|
| `app/Http/Controllers/DangkyController.php` | DangkyController, themdangky, yeucautraphong, duyetdangky |
| `app/Http/Controllers/PhongController.php` | PhongController, themtaisan, themphong |
| `app/Http/Controllers/HoadonController.php` | HoadonController, layGiaCauHinh, xulyhoadon |
| `app/Http/Controllers/ThongbaoController.php` | ThongbaoController, store |
| `app/Http/Controllers/KyluatController.php` | KyluatController, themkyluat |
| `app/Http/Controllers/BaohongController.php` | BaohongController, thembaohong |
| `app/Http/Controllers/Auth/NewPasswordController.php` | NewPasswordController, create |
| `app/Http/Controllers/TrangchuController.php` | TrangchuController |
| `app/Http/Controllers/SinhvienController.php` | SinhvienController |
| `app/Http/Controllers/ProfileController.php` | ProfileController |

## Entry Points

Start here when exploring this area:

- **`TrangchuController`** (Class) — `app/Http/Controllers/TrangchuController.php:14`
- **`ThongbaoController`** (Class) — `app/Http/Controllers/ThongbaoController.php:7`
- **`SinhvienController`** (Class) — `app/Http/Controllers/SinhvienController.php:9`
- **`ProfileController`** (Class) — `app/Http/Controllers/ProfileController.php:11`
- **`PhongController`** (Class) — `app/Http/Controllers/PhongController.php:9`

## Key Symbols

| Symbol | Type | File | Line |
|--------|------|------|------|
| `TrangchuController` | Class | `app/Http/Controllers/TrangchuController.php` | 14 |
| `ThongbaoController` | Class | `app/Http/Controllers/ThongbaoController.php` | 7 |
| `SinhvienController` | Class | `app/Http/Controllers/SinhvienController.php` | 9 |
| `ProfileController` | Class | `app/Http/Controllers/ProfileController.php` | 11 |
| `PhongController` | Class | `app/Http/Controllers/PhongController.php` | 9 |
| `KyluatController` | Class | `app/Http/Controllers/KyluatController.php` | 8 |
| `HopdongController` | Class | `app/Http/Controllers/HopdongController.php` | 9 |
| `HoadonController` | Class | `app/Http/Controllers/HoadonController.php` | 11 |
| `DangkyController` | Class | `app/Http/Controllers/DangkyController.php` | 10 |
| `Controller` | Class | `app/Http/Controllers/Controller.php` | 8 |
| `CauhinhController` | Class | `app/Http/Controllers/CauhinhController.php` | 7 |
| `BaohongController` | Class | `app/Http/Controllers/BaohongController.php` | 11 |
| `VerifyEmailController` | Class | `app/Http/Controllers/Auth/VerifyEmailController.php` | 9 |
| `RegisteredUserController` | Class | `app/Http/Controllers/Auth/RegisteredUserController.php` | 16 |
| `PasswordResetLinkController` | Class | `app/Http/Controllers/Auth/PasswordResetLinkController.php` | 11 |
| `PasswordController` | Class | `app/Http/Controllers/Auth/PasswordController.php` | 10 |
| `NewPasswordController` | Class | `app/Http/Controllers/Auth/NewPasswordController.php` | 16 |
| `EmailVerificationPromptController` | Class | `app/Http/Controllers/Auth/EmailVerificationPromptController.php` | 9 |
| `EmailVerificationNotificationController` | Class | `app/Http/Controllers/Auth/EmailVerificationNotificationController.php` | 8 |
| `ConfirmablePasswordController` | Class | `app/Http/Controllers/Auth/ConfirmablePasswordController.php` | 11 |

## How to Explore

1. `gitnexus_context({name: "TrangchuController"})` — see callers and callees
2. `gitnexus_query({query: "controllers"})` — find related execution flows
3. Read key files listed above for implementation details

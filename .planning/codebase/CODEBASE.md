# Codebase Overview

## Tech Stack

| Layer | Technology |
|-------|-----------|
| Language | PHP ^8.1 |
| Framework | Laravel ^10.10 |
| Database | MySQL + Eloquent ORM |
| Frontend CSS | Tailwind CSS ^3.1 + Flowbite ^1.6 |
| Frontend JS | Alpine.js ^3.4 |
| Asset Bundler | Vite ^5.0 |
| Auth | Laravel Breeze + Sanctum |
| Platform | Windows (Laragon) |

## Architecture

**Pattern:** MVC chuẩn Laravel.
- **Routes** → `routes/web.php` / `routes/api.php`
- **Controllers** → `app/Http/Controllers/` (Admin + Student)
- **Models** → `app/Models/` + **Observers** → `app/Observers/`
- **Views** → `resources/views/` (subfolders: `admin/`, `student/`, `public/`)
- **Migrations** → `database/migrations/`

**Đặc biệt:**
- Observers xử lý business logic sau model events (e.g., `SinhvienObserver`)
- Enums (`app/Enums/`) quản lý trạng thái hệ thống

## Key Directories

```
app/
  Http/Controllers/   # Request handlers (Admin*, Sinhvien*, Landing*)
  Models/             # Eloquent models
  Observers/          # Model event listeners
  Enums/              # Typed constants
database/
  migrations/         # Schema definitions
  seeders/            # Initial/test data
resources/views/
  admin/              # Admin panel templates
  student/            # Student portal templates
  public/ / landing/  # Public-facing pages
routes/
  web.php             # All web routes (grouped by middleware)
```

## Integrations

- **Auth:** Laravel Sanctum (API) + Breeze (web)
- **Email:** Blade mail templates (`emails/dangky-da-duyet.blade.php`)
- **Client:** Vite-bundled Tailwind + Flowbite + Alpine.js
- **Testing:** PHPUnit ^10.1 + Mockery + FakerPHP

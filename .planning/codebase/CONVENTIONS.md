# Coding Conventions

## Naming Standards
- **Controllers:** PascalCase, ending in `Controller` (e.g., `SinhvienController`).
- **Models:** PascalCase, singular (e.g., `Phong`).
- **Views:** kebab-case folder and file names (e.g., `admin/sinhvien/danhsach.blade.php`).
- **Variables/Methods:** camelCase (standard PHP/Laravel).
- **Database Tables:** snake_case, plural (standard Laravel).

## UI/Localization
- **Language:** English for code (classes, variables, database).
- **Interface:** Vietnamese (accented) for all user-facing strings in Blade templates.
- **Styling:** Tailwind utility classes preferred, integrated with Flowbite.

## Patterns
- **Validation:** Often handled within Controller methods or Request classes.
- **Model Hooks:** Use of Observers for background logic (e.g., updating stats on student save).
- **Route Grouping:** Middleware-protected routes grouped by role (`admin`, `student`).

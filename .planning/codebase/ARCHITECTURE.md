# Architecture

## Pattern
- **MVC (Model-View-Controller):** Standard Laravel architectural pattern.
- **Request Lifecycle:** HTTP requests are routed through `routes/web.php` or `routes/api.php` to Controllers in `app/Http/Controllers/`.

## Data Layer
- **Eloquent ORM:** Models in `app/Models/` interact with the database.
- **Migrations:** Database schema managed via `database/migrations/`.
- **Seeders:** Initial data and unit testing data managed via `database/seeders/`.

## UI & Logic
- **Blade Templates:** Server-side rendering using Blade in `resources/views/`.
- **Interactivity:** Alpine.js for lightweight frontend logic.
- **Components:** Flowbite for pre-built UI components.
- **Observers:** Business logic hooks (e.g., `SinhvienObserver.php`) for model events.

# Project Structure

## Key Directories
- `app/`: Core application code.
  - `Http/Controllers/`: Request handlers.
  - `Models/`: Eloquent models.
  - `Observers/`: Model event listeners.
- `database/`: Database management.
  - `migrations/`: Schema definitions.
  - `seeders/`: Data population.
- `resources/`: Frontend assets.
  - `views/`: Blade templates (subfolders: `admin`, `student`, `public`).
  - `css/` & `js/`: Asset sources.
- `routes/`: Routing definitions (`web.php`, `api.php`).
- `public/`: Publicly accessible files (images, entry point).
- `config/`: Application configuration files.

## Special Files
- `.agent/`: GSD framework skills and agents.
- `.planning/`: Project roadmap, research, and phase tracking.
- `.gitnexus/`: Code intelligence metadata.
- `repomix-output.xml`: Repository context dump.

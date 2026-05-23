# Repository Guidelines

## Project Structure & Module Organization
- `app/` holds Laravel application logic (controllers, services, exceptions, traits).
- `routes/` contains HTTP entry points (`routes/api/`, `routes/web/`). Versioned API routes live in `routes/api/v1.php`.
- `database/` contains migrations, factories, and seeders.
- `tests/` is split into `Feature/` and `Unit/` suites.
- `public/` serves the web entrypoint and assets; `resources/` contains views and front-end assets.
- `docs/` tracks project documentation (see `docs/overview.md` and `docs/TASKS.md`).

## Build, Test, and Development Commands
- `cd ../blog.workspace && make up local` starts the Docker stack for local development.
- `composer dev` runs the Laravel server, queue, log tailing (Pail), and Vite in parallel.
- `npm run dev` starts the Vite dev server only.
- `npm run build` creates production assets via Vite.
- `php artisan <command>` runs standard Laravel commands (e.g., `php artisan migrate`).
- `../blog.workspace/scripts/artisan.sh migrate` runs Artisan inside the Docker container.

## Coding Style & Naming Conventions
- PHP follows PSR-12 conventions and is auto-formatted with Laravel Pint (`./vendor/bin/pint`).
- Use Laravel naming conventions: `StudlyCase` classes, `camelCase` methods, `snake_case` migration files, and `*Controller` suffix for controllers.
- Keep files organized by domain in `app/` and keep route definitions grouped by version under `routes/api/`.

## Testing Guidelines
- PHPUnit is the default runner: `php artisan test` or `composer test`.
- Place unit tests in `tests/Unit` and integration/HTTP tests in `tests/Feature`.
- Name test files with the `*Test.php` suffix.

## Commit & Pull Request Guidelines
- Commit messages in this repo are short, single-line summaries (often in Korean). Keep them concise and action-oriented.
- PRs should include: a clear summary, the problem/goal, tests run, and any related task links (see `docs/TASKS.md`).
- For API changes, include example requests/responses or updated route notes.

## Security & Configuration Tips
- Do not commit `.env` files. Use `.env.example` as the template.
- Keep local and production `.env` files out of Git. Update them manually when values change.

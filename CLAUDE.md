# CLAUDE.md

Guide for Claude Code (claude.ai/code) in this repo.

## Common Commands

```bash
# Setup from scratch
composer setup

# Start dev environment (server + queue + logs + vite, runs concurrently)
composer dev

# Build frontend assets
composer run build    # production (runs `vite build`)
npm run dev           # dev-only Vite with HMR

# Run all tests
composer test

# Run single test file or method
php artisan test --filter=ExampleTest
php artisan test --filter=testBasicExample

# Format PHP code (Laravel Pint)
php artisan pint
./vendor/bin/pint --test   # check only, don't fix

# Other artisan commands
php artisan migrate
php artisan make:model ModelName -m   # model + migration
php artisan tinker
```

## Architecture

### Stack
- **Laravel 13** on PHP 8.3+
- **Vite 8** with `laravel-vite-plugin` for asset bundling
- **Tailwind CSS 4** via `@tailwindcss/vite` plugin (no PostCSS config needed)
- **MySQL** database (default: `cms_sja`)
- Testing: **PHPUnit 12** with SQLite in-memory (`:memory:`) for test isolation
- Code style: **Laravel Pint** (Laravel's opinionated PHP-CS-Fixer wrapper)

### Directory Conventions

Standard Laravel 13 structure. Key Laravel 13 specifics:

- **Model attributes** use PHP 8.x attributes instead of traditional `$fillable`/`$hidden` arrays:
  ```php
  #[Fillable(['name', 'email', 'password'])]
  #[Hidden(['password', 'remember_token'])]
  class User extends Authenticatable
  ```
- **Casts** use a `casts()` method returning an array instead of the `$casts` property.

### Frontend
- Entry points: `resources/css/app.css` (imports Tailwind), `resources/js/app.js` (empty stub)
- `laravel-vite-plugin` handles asset hashing and Blade `@vite()` directive
- Font: Instrument Sans loaded via Bunny CDN through the Vite font plugin
- Views use Blade with `@vite` and `@fonts` directives

### Testing
- Tests run against SQLite `:memory:` with env overrides in `phpunit.xml`
- Test suites: `tests/Unit/` and `tests/Feature/`
- `tests/TestCase.php` extends Laravel's base test case

### Composer Scripts
- `setup` — full first-time init (install deps, env, key gen, migrate, npm build)
- `dev` — runs `artisan serve`, `queue:listen`, `pail` (log tail), and `vite dev` concurrently
- `test` — clears config cache then runs `artisan test`

### Key Config (.env)
- `DB_CONNECTION=mysql`, database `cms_sja`
- `QUEUE_CONNECTION=database` (jobs table migration included)
- `SESSION_DRIVER=database`
- `CACHE_STORE=database`
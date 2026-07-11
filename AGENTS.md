# AGENTS.md - CMS Website Monorepo

## Communication Style

- **Explain every step like a tutor teaching a student** — break down what you're doing and why before doing it
- Use simple, clear language
- Don't assume the user knows the context — walk them through it
- **Show file diff before asking permission** — let the user see what changes will be made before approving
- **Always ask for permission before creating or editing files** — never make changes without user approval

## Platform

This project is developed on **Windows** (not Linux). All commands in this repo use **PowerShell syntax** (e.g., `Get-ChildItem` instead of `ls`, `New-Item` instead of `mkdir -p`, `;` instead of `&&`).

## Project Structure (Monorepo)

```
cms-website/
├── api/           # Laravel API (Backend)
├── admin/         # Vue 3 + Pinia + PrimeVue (Admin Panel)
├── public-site/   # Vue 3 + Nuxt 3 (Public Frontend)
```

## Quick Start

```bash
# Install all dependencies (run in each directory)
cd api && composer install
cd ../admin && npm install
cd ../public-site && npm install
```

## Development Commands

| Project | Dev Server | Build | Test | Lint |
|---------|-----------|-------|------|------|
| `api` | `php artisan serve` | `php artisan build` | `php artisan test` | `./vendor/bin/pint` |
| `admin` | `npm run dev` | `npm run build` | `npm run test` | `npm run lint` |
| `public-site` | `npm run dev` | `npm run build` | `npm run test` | `npm run lint` |

## Architecture Notes

- **API (Laravel)**: REST API with Sanctum auth, serves admin + public site
- **Admin (Vue 3 SPA)**: Pinia state, PrimeVue UI, Vite, protected routes
- **Public Site (Nuxt 3)**: SSR/SSG, consumes API, public-facing

## Environment

Each project needs its own `.env`:
- `api/.env` - DB, Sanctum, CORS for admin/public domains
- `admin/.env` - `VITE_API_URL=http://localhost:8000/api`
- `public-site/.env` - `NUXT_API_URL=http://localhost:8000/api`

## Database

```bash
cd api && php artisan migrate --seed
```

## Common Tasks

```bash
# Run all dev servers (use concurrently or separate terminals)
cd api && php artisan serve      # :8000
cd admin && npm run dev          # :3001
cd public-site && npm run dev    # :3000
```

## Git Workflow

- Monorepo: single repo, separate folders
- Feature branches per project or cross-cutting
- Conventional commits: `feat(api):`, `fix(admin):`, etc.

## Gotchas

- CORS: Configure in `api/config/cors.php` for `admin` + `public-site` origins
- Sanctum: Set `SESSION_DOMAIN` and `SANCTUM_STATEFUL_DOMAINS` in `api/.env`
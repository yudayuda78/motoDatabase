# motoDB

**motoDB** is a motorcycle catalog web application for the Indonesian market, built with the
**Nette Framework**, **Latte**, and **PostgreSQL**. It uses a *dark racing* visual theme powered by
**Tailwind CSS v4** with a red accent. Visitors can browse a featured motorcycle, the full catalog,
detail pages with specs and pricing, and the list of brands. All data lives in PostgreSQL and is
managed through **Phinx** migrations.

> 🇮🇩 **Versi Bahasa Indonesia:** [README.id.md](README.id.md)
>
> Dokumentasi teknis (Bahasa Indonesia): [`docs/`](docs/)

---

## Features

- **Hero showcase** — a featured motorcycle (currently the Kawasaki Ninja 250) rendered as a large
  inline SVG illustration on a diagonal red racing layout.
- **Motor Unggulan** — card grid of all registered motorcycles with type and OTR price.
- **Aksesoris Pilihan** — four accessories with generated SVG icons (tire, rim, exhaust, brake disc).
- **Catalog** — list of all motorcycles and a detail page per motorcycle.
- **Brands** — brand list and per-brand detail page with country and registration date.
- **Custom error pages** — dedicated 4xx and 5xx presenters/templates.
- **Database-backed** — repositories read from PostgreSQL through `Nette\Database\Explorer`.
- **Versioned schema** — Phinx migrations + idempotent seeders.

## Tech stack

| Layer | Technology | Version |
|---|---|---|
| Language | PHP | 8.3+ (developed on 8.3.30) |
| Framework | Nette Application / Bootstrap / DI | 3.3.0 / 3.3.0 / 3.2.7 |
| Templating | Latte | 3.1.6 |
| Database layer | nette/database (`Nette\Database\Explorer`) | 3.2.9 |
| Database | PostgreSQL | 15.13 |
| Migrations | Phinx | 0.16.12 (dev) |
| CSS | Tailwind CSS v4 + `@tailwindcss/cli` | 4.3.3 |
| Debugging | Tracy | 2.12.1 |
| Package managers | Composer 2.8, npm | — |

## Prerequisites

- PHP **8.3+** with `pdo_pgsql`
- Composer 2.x
- Node.js **18+** (tested on 20.20) and npm
- PostgreSQL **15+** running locally

## Quick start

```bash
# 1. PHP dependencies
composer install

# 2. Environment file
cp .env.example .env
#    then adjust DB_* values

# 3. Frontend dependencies + CSS build
npm install
npm run build

# 4. Create the database (once)
psql -h 127.0.0.1 -U postgres -c "CREATE USER moto WITH PASSWORD 'moto';"
psql -h 127.0.0.1 -U postgres -c "CREATE DATABASE moto OWNER moto;"

# 5. Schema + seed data
composer migrate
composer seed

# 6. Run the dev server
composer serve
```

Open **<http://localhost:8000>**.

### Environment variables

`.env` (never committed — it is listed in `.gitignore`):

```ini
DB_HOST=127.0.0.1
DB_PORT=5432
DB_NAME=moto
DB_USER=moto
DB_PASSWORD=moto
DB_DSN=pgsql:host=127.0.0.1;port=5432;dbname=moto
```

`DB_DSN`, `DB_USER`, and `DB_PASSWORD` are consumed by `config/common.neon`; `phinx.php` reads
`DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, and `DB_PASSWORD` from the same file.

## Routes

| URL | Presenter action | Description |
|---|---|---|
| `/` | `Home:default` | Hero, featured motorcycles, accessories, banner |
| `/catalog` | `Catalog:default` | All motorcycles |
| `/catalog/<id>` | `Catalog:detail` | One motorcycle (404 when missing) |
| `/brand` | `Brand:default` | All brands |
| `/brand/<id>` | `Brand:detail` | One brand (404 when missing) |
| `/<presenter>/<action>[/<id>]` | fallback | Generic Nette route |
| — | `Error:Error4xx` / `Error:Error5xx` | Error pages |

Details: [`docs/rute-dan-halaman.md`](docs/rute-dan-halaman.md).

## Useful commands

| Command | What it does |
|---|---|
| `composer serve` | Dev server at `http://localhost:8000` (`php -S` + `router.php`) |
| `composer migrate` | Apply pending migrations |
| `composer migrate:status` | Show migration status |
| `composer migrate:rollback` | Roll back the **last** migration |
| `vendor/bin/phinx rollback -t 0` | Roll back **all** migrations |
| `composer seed` | Run all seeders |
| `vendor/bin/phinx create NamaMigrasi` | Create a new empty migration |
| `npm run build` | Build + minify `www/assets/css/app.css` |
| `npm run watch` | Rebuild CSS on change |

## Project structure

```text
motoDB/
├── app/
│   ├── Bootstrap.php                 # env loading, Tracy, RobotLoader, DI container
│   ├── Core/
│   │   ├── AccessoryRepository.php   # accessories, ordered by id
│   │   ├── BrandRepository.php       # brands, ordered by name
│   │   ├── CatalogRepository.php     # motors: findAll / find / findFeatured
│   │   └── RouterFactory.php         # all routes
│   ├── css/
│   │   └── app.css                   # Tailwind entry: @source + @theme brand palette
│   └── Presentation/
│       ├── @layout.latte             # shared nav, flashes, main, footer
│       ├── Accessory/
│       │   └── LatteExtension.php    # Latte extension (placeholder, no filters yet)
│       ├── Brand/                    # BrandPresenter + default/detail templates
│       ├── Catalog/                  # CatalogPresenter + default/detail templates
│       ├── Error/                    # Error4xx and Error5xx presenters/templates
│       └── Home/                     # HomePresenter + default.latte (hero, cards, banner)
├── config/
│   ├── common.neon                   # application, database, latte, di config
│   └── services.neon                 # router service + autowiring by class suffix
├── db/
│   ├── migrations/                   # Phinx migrations (brands, motors, accessories)
│   └── seeds/                        # Phinx seeders (Brand, Motor, Accessory)
├── docs/                             # technical documentation (Indonesian)
├── log/                              # Tracy logs
├── temp/                             # Nette cache
├── vendor/                           # Composer packages
├── www/
│   ├── assets/css/app.css            # built Tailwind output (generated)
│   ├── .htaccess                     # Apache rewrite for production
│   └── index.php                     # front controller
├── .env.example
├── composer.json
├── package.json
├── phinx.php                         # Phinx config, reads .env
└── router.php                        # router for PHP's built-in server
```

## Database

Three application tables plus Phinx's bookkeeping table:

| Table | Purpose |
|---|---|
| `motors` | Motorcycles: name, brand (text), model, type, price, featured flag |
| `brands` | Brands: unique name, country |
| `accessories` | Accessories: name, category, price, icon key |
| `phinxlog` | Applied migration versions (managed by Phinx) |

Schema, ERD, and migration workflow: [`docs/database.md`](docs/database.md).

## Documentation

| Document | Contents |
|---|---|
| [`docs/arsitektur.md`](docs/arsitektur.md) | Request lifecycle, layers, DI, configuration |
| [`docs/database.md`](docs/database.md) | ERD, table columns, migrations, seeders |
| [`docs/rute-dan-halaman.md`](docs/rute-dan-halaman.md) | Routes, presenters, page-by-page breakdown |
| [`docs/desain-ui.md`](docs/desain-ui.md) | Color palette, layout, reusable SVG components, Tailwind |
| [`docs/pengembangan.md`](docs/pengembangan.md) | Dev workflow, how-to guides, current status & TODO |

## Status

This is a learning/portfolio project. The home and brand pages are fully styled; the catalog pages
are still plain HTML. See the honest TODO list in
[`docs/pengembangan.md`](docs/pengembangan.md#status--todo-jujur).

## License

No license has been declared yet (`composer.json` warns about this). Treat the code as
proprietary until a license is chosen.

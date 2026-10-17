# motoDB

**motoDB** adalah aplikasi web katalog sepeda motor Indonesia yang dibangun dengan **Nette
Framework**, **Latte**, dan **PostgreSQL**. Tampilannya bergaya *dark racing* memakai **Tailwind CSS
v4** dengan aksen merah. Pengunjung dapat menjelajahi motor unggulan, katalog lengkap, halaman detail
berisi tipe & harga, serta daftar merek. Seluruh data disimpan di PostgreSQL dan dikelola melalui
migrasi **Phinx**.

> 🇬🇧 **English version:** [README.md](README.md)
>
> Dokumentasi teknis: [`docs/`](docs/)

---

## Fitur

- **Hero showcase** — satu motor unggulan (saat ini Kawasaki Ninja 250) digambar sebagai ilustrasi
  SVG inline besar di atas layout racing diagonal merah.
- **Motor Unggulan** — grid kartu seluruh motor terdaftar beserta tipe dan harga OTR.
- **Aksesoris Pilihan** — empat aksesoris dengan ikon SVG yang dibuat sendiri (ban, velg, knalpot,
  cakram rem).
- **Katalog** — daftar semua motor dan halaman detail per motor.
- **Brand** — daftar merek dan halaman detail per merek berisi negara serta tanggal pendaftaran.
- **Halaman error khusus** — presenter/template terpisah untuk 4xx dan 5xx.
- **Berbasis database** — repository membaca data dari PostgreSQL lewat `Nette\Database\Explorer`.
- **Skema berversi** — migrasi Phinx + seeder yang idempoten.

## Teknologi

| Lapisan | Teknologi | Versi |
|---|---|---|
| Bahasa | PHP | 8.3+ (dikembangkan di 8.3.30) |
| Framework | Nette Application / Bootstrap / DI | 3.3.0 / 3.3.0 / 3.2.7 |
| Templating | Latte | 3.1.6 |
| Lapisan database | nette/database (`Nette\Database\Explorer`) | 3.2.9 |
| Database | PostgreSQL | 15.13 |
| Migrasi | Phinx | 0.16.12 (dev) |
| CSS | Tailwind CSS v4 + `@tailwindcss/cli` | 4.3.3 |
| Debugging | Tracy | 2.12.1 |
| Package manager | Composer 2.8, npm | — |

## Prasyarat

- PHP **8.3+** dengan ekstensi `pdo_pgsql`
- Composer 2.x
- Node.js **18+** (diuji pada 20.20) dan npm
- PostgreSQL **15+** yang berjalan lokal

## Instalasi cepat

```bash
# 1. Dependensi PHP
composer install

# 2. File environment
cp .env.example .env
#    lalu sesuaikan nilai DB_*

# 3. Dependensi frontend + build CSS
npm install
npm run build

# 4. Buat database (sekali saja)
psql -h 127.0.0.1 -U postgres -c "CREATE USER moto WITH PASSWORD 'moto';"
psql -h 127.0.0.1 -U postgres -c "CREATE DATABASE moto OWNER moto;"

# 5. Skema + data awal
composer migrate
composer seed

# 6. Jalankan dev server
composer serve
```

Buka **<http://localhost:8000>**.

### Variabel environment

`.env` (jangan di-commit — sudah masuk `.gitignore`):

```ini
DB_HOST=127.0.0.1
DB_PORT=5432
DB_NAME=moto
DB_USER=moto
DB_PASSWORD=moto
DB_DSN=pgsql:host=127.0.0.1;port=5432;dbname=moto
```

`DB_DSN`, `DB_USER`, dan `DB_PASSWORD` dipakai oleh `config/common.neon`; `phinx.php` membaca
`DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, dan `DB_PASSWORD` dari file yang sama.

## Rute

| URL | Action presenter | Keterangan |
|---|---|---|
| `/` | `Home:default` | Hero, motor unggulan, aksesoris, banner |
| `/catalog` | `Catalog:default` | Semua motor |
| `/catalog/<id>` | `Catalog:detail` | Satu motor (404 bila tidak ada) |
| `/brand` | `Brand:default` | Semua merek |
| `/brand/<id>` | `Brand:detail` | Satu merek (404 bila tidak ada) |
| `/<presenter>/<action>[/<id>]` | fallback | Rute generik Nette |
| — | `Error:Error4xx` / `Error:Error5xx` | Halaman error |

Rincian: [`docs/rute-dan-halaman.md`](docs/rute-dan-halaman.md).

## Perintah penting

| Perintah | Fungsi |
|---|---|
| `composer serve` | Dev server di `http://localhost:8000` (`php -S` + `router.php`) |
| `composer migrate` | Jalankan migration yang belum diterapkan |
| `composer migrate:status` | Lihat status migration |
| `composer migrate:rollback` | Batalkan **satu** migration terakhir |
| `vendor/bin/phinx rollback -t 0` | Batalkan **semua** migration |
| `composer seed` | Jalankan semua seeder |
| `vendor/bin/phinx create NamaMigrasi` | Buat file migration baru |
| `npm run build` | Build + minify `www/assets/css/app.css` |
| `npm run watch` | Build ulang CSS setiap ada perubahan |

## Struktur project

```text
motoDB/
├── app/
│   ├── Bootstrap.php                 # load .env, Tracy, RobotLoader, container DI
│   ├── Core/
│   │   ├── AccessoryRepository.php   # aksesoris, urut id
│   │   ├── BrandRepository.php       # merek, urut nama
│   │   ├── CatalogRepository.php     # motor: findAll / find / findFeatured
│   │   └── RouterFactory.php         # seluruh rute
│   ├── css/
│   │   └── app.css                   # entry Tailwind: @source + @theme palet brand
│   └── Presentation/
│       ├── @layout.latte             # nav, flashes, main, footer bersama
│       ├── Accessory/
│       │   └── LatteExtension.php    # ekstensi Latte (placeholder, belum ada filter)
│       ├── Brand/                    # BrandPresenter + template default/detail
│       ├── Catalog/                  # CatalogPresenter + template default/detail
│       ├── Error/                    # presenter/template Error4xx dan Error5xx
│       └── Home/                     # HomePresenter + default.latte (hero, kartu, banner)
├── config/
│   ├── common.neon                   # konfigurasi application, database, latte, di
│   └── services.neon                 # service router + autowiring berdasarkan akhiran kelas
├── db/
│   ├── migrations/                   # migration Phinx (brands, motors, accessories)
│   └── seeds/                        # seeder Phinx (Brand, Motor, Accessory)
├── docs/                             # dokumentasi teknis
├── log/                              # log Tracy
├── temp/                             # cache Nette
├── vendor/                           # paket Composer
├── www/
│   ├── assets/css/app.css            # hasil build Tailwind (generated)
│   ├── .htaccess                     # rewrite Apache untuk produksi
│   └── index.php                     # front controller
├── .env.example
├── composer.json
├── package.json
├── phinx.php                         # konfigurasi Phinx, membaca .env
└── router.php                        # router untuk built-in server PHP
```

## Database

Tiga tabel aplikasi plus tabel pembukuan Phinx:

| Tabel | Kegunaan |
|---|---|
| `motors` | Motor: nama, merek (teks), model, tipe, harga, flag unggulan |
| `brands` | Merek: nama unik, negara |
| `accessories` | Aksesoris: nama, kategori, harga, kunci ikon |
| `phinxlog` | Versi migration yang sudah diterapkan (dikelola Phinx) |

Skema, ERD, dan alur migrasi: [`docs/database.md`](docs/database.md).

## Dokumentasi

| Dokumen | Isi |
|---|---|
| [`docs/arsitektur.md`](docs/arsitektur.md) | Siklus request, lapisan, DI, konfigurasi |
| [`docs/database.md`](docs/database.md) | ERD, kolom tabel, migration, seeder |
| [`docs/rute-dan-halaman.md`](docs/rute-dan-halaman.md) | Rute, presenter, rincian tiap halaman |
| [`docs/desain-ui.md`](docs/desain-ui.md) | Palet warna, layout, komponen SVG, Tailwind |
| [`docs/pengembangan.md`](docs/pengembangan.md) | Alur kerja dev, panduan menambah fitur, status & TODO |

## Status

Ini project belajar/portofolio. Halaman Home dan Brand sudah di-styling penuh; halaman katalog
masih HTML polos. Lihat daftar TODO yang jujur di
[`docs/pengembangan.md`](docs/pengembangan.md#status--todo-jujur).

## Lisensi

Belum ada lisensi yang dideklarasikan (`composer.json` memberi peringatan soal ini). Anggap kode ini
*proprietary* sampai lisensi dipilih.

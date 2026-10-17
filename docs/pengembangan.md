# Pengembangan — motoDB

> **English abstract.** This guide covers the day-to-day dev loop (dev server + Tailwind watch),
> how to add/edit seed data, how to write and run a new migration, how to add a new page and a new
> Latte component, project conventions, and an honest **status & TODO** list with known sharp edges.

## Alur kerja harian

Buka dua terminal:

```bash
# Terminal 1 — server dev
composer serve                       # → http://localhost:8000

# Terminal 2 — build CSS otomatis
npm run watch
```

Ubah `.latte` / `.php` → muat ulang browser → CSS ikut ter-rebuild otomatis.

> Pastikan `npm run watch` berjalan sebelum menambah **kelas Tailwind baru**, karena
> Tailwind v4 harus memindai `.latte` (`@source`) untuk menghasilkan utilitasnya. Jika CSS tidak
> muncul, jalankan `npm run build` sekali secara manual.

## Migration — membuat, menjalankan, memutar balik

```bash
# 1. Buat file migration baru (skeleton)
vendor/bin/phinx create TambahKolomStok

# 2. Isi db/migrations/<timestamp>_tambah_kolom_stok.php
#    gunakan change() bila reversibel, atau up()/down() untuk SQL manual

# 3. Terapkan
composer migrate
composer migrate:status   # cek status up/down

# Rollback: satu langkah terakhir, atau semua
composer migrate:rollback
vendor/bin/phinx rollback -t 0
```

Contoh isi `change()`:

```php
$this->table('motors')
	->addColumn('stok', 'integer', ['default' => 0])
	->update();
```

> Ingat: `phinx rollback` **tanpa argumen hanya membatalkan satu migration terakhir**, bukan
> seluruh batch.

## Seeder — menambah/mengubah data awal

Seeder: `db/seeds/*Seeder.php`, semuanya idempoten (berhenti bila tabel sudah berisi).

```bash
composer seed                       # jalankan semua seeder
vendor/bin/phinx seed:run -s MotorSeeder   # seeder tertentu
```

Untuk menambah data:

1. Ubah array `insert()` pada seeder terkait (Brand/Motor/Accessory).
2. Untuk tabel yang sudah terisi, kosongkan lebih dulu lalu seed ulang, atau sisipkan langsung
   via psql:

```sql
INSERT INTO motors (nama, merek, model, tipe, harga, featured)
VALUES ('Yamaha R15', 'Yamaha', 'R15', 'Sport', 36000000, FALSE);
```

## Menambah halaman baru

1. **Presenter** — `app/Presentation/<Nama>/<Nama>Presenter.php` (mengikuti pola Home/Brand/Catalog,
   injeksi repository sesuai kebutuhan).
2. **Template** — `app/Presentation/<Nama>/default.latte` dimulai dengan `{block content}` (tanpa
   blok tersebut halaman render kosong).
3. **Rute** — tambahkan di `RouterFactory` **sebelum** rute fallback
   `"<presenter>/<action>[/<id>]"`, mis. `$router->addRoute('about', 'About:default');`.
4. (opsional) tautkan di `@layout.latte`.
5. Cek: `php -l` + buka URL.

## Menambah komponen Latte/SVG baru

- `{define nama, $param = default}` di template penyedia.
- Panggil `{include nama, param: nilai}` (argumen bernama didukung Latte 3).
- Bila komponen berisi `id` SVG (mis. gradient), beri parameter `$uid` dan panggil dengan nilai unik
  per instance, karena Latte mengompilasi template sekali untuk semua pemakaian.

## Konvensi project

- Repository mengembalikan **array** (`Row::toArray()`), bukan objek `ActiveRow` — pertahankan agar
  template tidak bergantung ke bentuk DB. *(Cetusan: `(array)` cast untuk object Row tidak reliable,
  gunakan `toArray()`.)*
- `declare(strict_types=1)` di semua file PHP.
- Template memakai `n:href` untuk link.
- Jangan edit `www/assets/css/app.css` (hasil build).
- Bahasa template: Indonesia (class/teks UI), kode bareng Nette convention.

## Status & TODO (jujur)

### Selesai
- Home & Brand di-styling; migrasi + seeder + repository PostgreSQL; error 4xx/5xx; Tailwind theme.

### TODOs
| Item | Catatan |
|---|---|
| **Katalog belum di-styling** | `Catalog/default.latte` (11 baris) & `detail.latte` (5 baris) masih HTML polos, tidak konsisten dengan Home/Brand. |
| **`LatteExtension` masih kosong** | Terdaftar di `common.neon` tapi `getFilters()` / `getFunctions()` mengembalikan `[]`. |
| **Belum ada relasi brand** | `motors.merek` teks; tidak ada FK `brand_id → brands.id`. Diusulkan roadmap di `docs/database.md`. |
| **Belum ada tes otomatis** | Saat ini hanya `php -l` + verifikasi HTTP manual. |
| **Belum ada ikon sosial footer** | Footer referensi (share/mail/globe/facebook) belum dibuat. |

### Edge cases yang diketahui
- `phinx rollback` default = 1 migration (bukan seluruh batch).
- `n:href` tidak bisa diuji dengan compile Latte standalone — jalankan server untuk verifikasi link.
- Tracy mengeluarkan dump DI container di HTML pada mode dev — normal untuk development.
- `npm install` melaporkan beberapa *high severity* vulnerabilities (rantai toolchain Tailwind) —
  bersifat dev-only, tidak menyentuh runtime PHP.
- `.env` ada di `.gitignore`; `phinx.php` membaca `.env` — kredensial tidak ter-commit.
- Database kosong total → halaman cache lama bisa menampilkan data usang; hapus `temp/` bila perlu.

## Debug & verify

```bash
php -l app/Core/CatalogRepository.php        # cek sintaks
vendor/bin/phinx status                       # skema
psql -d moto -c 'select * from motors;'       # data
curl -s -o /dev/null -w '%{http_code}' http://localhost:8000/catalog
```

## Roadmap singkat

1. Styling ulang halaman katalog agar seragam dengan Home/Brand.
2. Normalisasi `motors.brand_id` FK + daftar motor per brand.
3. Isi `LatteExtension` (filter `rupiah`, inline SVG, dsb.).
4. Ikon sosial footer.
5. Tes otomatis (PHPUnit/Codeception) untuk repository & presenter.
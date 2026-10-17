# Rute & halaman — motoDB

> **English abstract.** All routes are defined in `RouterFactory::createRouter()`: pretty URLs
> `catalog`, `catalog/<id>`, `brand`, `brand/<id>` plus a generic Nette fallback and error
> presenters. Every page is a Presenter + a Latte template. Home and Brand pages are fully styled
> with the dark/racing theme; the Catalog pages are intentionally left as plain HTML for now.

## Definisi rute (`app/Core/RouterFactory.php`)

```php
$router->addRoute("catalog", "Catalog:default");
$router->addRoute("catalog/<id>", "Catalog:detail");
$router->addRoute("brand", "Brand:default");
$router->addRoute("brand/<id>", "Brand:detail");
$router->addRoute("<presenter>/<action>[/<id>]", "Home:default");
// rute kosong (/) → Home:default secara implisit
```

Urutan penting: rute spesifik didaftarkan **lebih dulu**, rute fallback generik terakhir.

## Tabel rute

| URL | Presenter:action | Template | 404? | Styling |
|---|---|---|---|---|
| `/` | `Home:default` | `Home/default.latte` | — | ✅ dark racing |
| `/catalog` | `Catalog:default` | `Catalog/default.latte` | — | ⚠️ polos |
| `/catalog/<id>` | `Catalog:detail` | `Catalog/detail.latte` | ya | ⚠️ polos |
| `/brand` | `Brand:default` | `Brand/default.latte` | — | ✅ dark racing |
| `/brand/<id>` | `Brand:detail` | `Brand/detail.latte` | ya | ✅ dark racing |
| `/<presenter>/<action>[/<id>]` | fallback → default | tergantung presenter | — | — |
| bebas | `Error:Error4xx` | `Error/Error4xx/4xx.latte` (+ `404.latte`) | — | — |
| bebas | `Error:Error5xx` | `Error/Error5xx/500.phtml` | — | — |

## Home (`Home:default`)

Presenter menyuntikkan `CatalogRepository` dan `AccessoryRepository`, lalu mengisi tiga variabel
template:

```php
$this->template->motors      = $this->catalogs->findAll();       // array
$this->template->featured    = $this->catalogs->findFeatured();  // motor feature (hero)
$this->template->accessories = $this->accessories->findAll();    // array
```

Template `Home/default.latte` (321 baris) berisi:

| Bagian | Komentar |
|---|---|
| **Hero** | Panel terang kanan + strip merah diagonal (`-skew-x-12`), judul besar `{$featured['model']}` + bar merek, `{$featured['tipe']}` + harga, CTA "Lihat Detail" (`Catalog:detail $featured['id']`) dan "Semua Motor". Termasuk `{include bike, body: '#e5342b', bodyDark: '#9f1a1a', uid: 'hero'}`. |
| **Strip statistik** | `{count($motors)}` motor terdaftar, `$brandCount` = `count(array_unique(array_column($motors, 'merek')))` merek, dan "100% Gratis". |
| **Motor Unggulan** | Kartu `foreach $motors` → link ke `Catalog:detail`. Ilustrasi bike diberi warna berbeda per kartu: kartu 1 biru (`#2563eb`), kartu 2 gelap (`#1f2937`), kartu lain merah HTML (`#e5342b`). Menampilkan nama, tipe, harga (`number_format`). |
| **Aksesoris Pilihan** | `foreach $accessories` → `{include accessoryIcon, type: $item['ikon'], uid: 'acc'.iterator}` lalu nama, kategori, harga, tombol "Pesan Sekarang". |
| **Banner** | Panel gelap dengan irisan merah `md:[clip-path:polygon(...)]` berjudul "Aman, Nyaman & Menyenangkan", CTA "Mulai Telusuri", ilustrasi `{include bike, ..., uid: 'banner'}`. Di bawah `{block content}` ada tiga `{define}` reusable: `wheel`, `accessoryIcon`, `bike`. |

Variabel template yang dipakai: `$motors`, `$featured`, `$accessories`, `$iterator->counter`.

## Katalog (`Catalog:default`, `Catalog:detail`)

```php
public function renderDefault(): void
{
	$this->template->motors = $this->catalogs->findAll();
}

public function renderDetail(int $id): void
{
	$motor = $this->catalogs->find($id);
	if ($motor === null) {
		$this->error('Motor tidak ditemukan', 404);
	}
	$this->template->motor = $motor;
}
```

- `Catalog/default.latte` — daftar `<ul>` polos: nama motor sebagai link ke `Catalog:detail` + harga.
- `Catalog/detail.latte` — satu `<h1>` nama, harga, link "← Kembali ke katalog".
- **Status:** belum di-styling (HTML default Nette starter). Ini TODO yang disengaja — targetnya
  kartu grid konsisten dengan halaman Home/Brand.

## Brand (`Brand:default`, `Brand:detail`)

```php
public function renderDefault(): void
{
	$this->template->brands = $this->brands->findAll();   // urut nama
}

public function renderDetail(int $id): void
{
	$brand = $this->brands->find($id);
	if ($brand === null) {
		$this->error('Brand tidak ditemukan', 404);
	}
	$this->template->brand = $brand;
}
```

- `Brand/default.latte` — kartu grid: nama brand (`uppercase`), negara, panah ikon
  `SVG` di kotak merah `bg-brand-600`, hover `border-brand-500/70`. Empty state bila tabel kosong.
- `Brand/detail.latte` — border kiri merah `border-l-4 border-brand-500`, nama besar, negara;
  grid 3 kolom berisi ID (`#{$brand['id']}`), Negara, dan Terdaftar
  (`{$brand['created_at']|date:'d M Y'}`); CTA "Lihat Katalog Motor".

## Error (`Error:Error4xx`, `Error:Error5xx`)

- `app/Presentation/Error/Error4xx/404.latte` + `4xx.latte` — halaman 404 / error klien.
- `app/Presentation/Error/Error5xx/500.phtml` — halaman error server (mulai ulang container untuk
  menerapkan perubahan template).
- `common.neon` menunjuk `4xx: Error:Error4xx`, `5xx: Error:Error5xx`. Presenter memberi `$this->error(…)`
  untuk 404, mis. brand/motor yang tidak ditemukan.

## Layout bersama (`@layout.latte`)

- `<html lang="id">`, judul `<title>{ifset title}… │ {/ifset}motoDB</title>`.
- Stylesheet `/assets/css/app.css` (hasil build Tailwind).
- `body.bg-neutral-950.text-neutral-100.antialiased` — tema gelap global.
- Nav sticky (blur): logo `moto` (merah) + `DB`, link `Home`, `Katalog`, `Brand`.
- Loop `$flashes` untuk pesan flash.
- `<main>{include content}</main>` — semua halaman menimpa `{block content}`.
- Footer: `© {date('Y')} motoDB — database motor Indonesia` dan "Dibangun dengan Nette + Tailwind CSS".

## Menambahkan halaman baru (ringkas)

1. Buat `app/Presentation/<Nama>/<Nama>Presenter.php` dengan `renderDefault()` dsb.
2. Template `<Nama>/default.latte` diawali `{block content}` … `{/block}`.
3. Tambah rute di `RouterFactory` (sebelum rute fallback).
4. `composer serve` lalu cek URL.

Rincian lengkap: [pengembangan.md](pengembangan.md).
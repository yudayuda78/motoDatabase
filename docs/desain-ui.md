# Desain UI — motoDB

> **English abstract.** motoDB has a *dark racing* UI: near-black surfaces (`neutral-*`), a custom
> red accent palette called `brand` defined with Tailwind v4 `@theme`, hard (non-rounded) edges,
> uppercase bold typography, skewed diagonal ribbons, and reusable **inline-SVG Latte macros**
> (`{define bike}` / `{define wheel}` / `{define accessoryIcon}`) instead of image files.

## Prinsip

1. **Gelap terlebih dahulu** — `bg-neutral-950` di `body` (via `@layout.latte`), teks `neutral-100`,
   kartu `neutral-100` (terang) dengan shadow gelap untuk kontras.
2. **Aksen merah** — seluruh aksen memakai palet `brand-*`.
3. **Gaya racing** — pita/panel diagonal (`-skew-x-12`), kotak bersudut tajam, tipografi
   `uppercase` + `font-black`, shadow merah pada hover.
4. **Tanpa aset foto** — semua benda digambar sebagai SVG inline.

## Palet `brand` (`app/css/app.css`)

| Token | Hex |
|---|---|
| `brand-50` | `#fdf3f2` |
| `brand-100` | `#fbe3e1` |
| `brand-200` | `#f7c5c1` |
| `brand-300` | `#f29a93` |
| `brand-400` | `#ea6459` |
| `brand-500` | `#e5342b` |
| `brand-600` | `#cc221c` |
| `brand-700` | `#ab1a18` |
| `brand-800` | `#8c1a1a` |
| `brand-900` | `#771c1b` |
| `brand-950` | `#420d0d` |

Dipilih dengan memakai utilitas Tailwind langsung di template, mis. `text-brand-500`,
`bg-brand-600`, `border-brand-500/50`, `shadow-brand-600/30`.

## Cara kerja Tailwind v4 di project ini

`app/css/app.css`:

```css
@import "tailwindcss";
@source "../../app/Presentation/**/*.latte";

@theme { … palet brand … }
```

- `@source` memberi tahu Tailwind *scan* folder template — semua kelas yang muncul di `.latte`
  otomatis dihasilkan.
- Build: `npm run build` (sekali) atau `npm run watch` (otomatis saat develop).
- Hasil akhir: `www/assets/css/app.css` (**jangan diedit manual**).

## Layout (`@layout.latte`)

| Bagian | Detail |
|---|---|
| `head` | UTF-8, viewport, judul `<title>… │ motoDB</title>`, link CSS `/assets/css/app.css` |
| `nav` | sticky, `border-b`, `backdrop-blur`; logo <span class="text-brand-400">moto</span>DB; link Home/Katalog/Brand |
| flash | `<div n:foreach="$flashes as $flash">` |
| `<main>` | `{include content}` — halaman mengisi `{block content}` |
| `footer` | `© {date('Y')} motoDB — database motor Indonesia` + "Dibangun dengan Nette + Tailwind CSS" |

## Komponen SVG (Latte `{define}`, ada di `Home/default.latte`)

### `{define bike, $body = '#e5342b', $bodyDark = '#9f1a1a', $uid = 'a'}`

Ilustrasi motor utuh (viewBox 640×400):
- `$body` → warna tangki (gradient atas), `$bodyDark` → warna bawah tangki + tail.
- `$uid` → **suffix untuk id gradient `tank-{$uid}`** sehingga boleh dipakai banyak kali dalam satu
  halaman tanpa id duplikat.
- Memanggil `{include wheel, cx: 150}` dan `{include wheel, cx: 490}`.

Contoh pemakaian di Home:

```latte
{include bike, body: '#e5342b', bodyDark: '#9f1a1a', uid: 'hero'}
{include bike, body: '#2563eb', bodyDark: '#1e40af', uid: 'card1'}
{include bike, body: '#1f2937', bodyDark: '#030712', uid: 'card2'}
```

> ⚠️ **Wajib**: `uid` tiap instance harus unik dalam satu halaman (karena `id="tank-{$uid}"`).

### `{define wheel, $cx = 150}`

Satu roda pada koordinat `($cx, 290)`: ban hitam, velg, jari-jari, hub. Dipanggil dua kali dari
`bike`.

### `{define accessoryIcon, $type = 'ban', $uid = 'a'}`

Ikon aksesoris berdasarkan `$type`:

| `$type` | Ikon |
|---|---|
| `ban` | Ban tubeless (lingkaran dengan garis aksial) |
| `velg` | Velg racing (ring + 5 ruji) |
| `knalpot` | Knalpot racing (rotated) |
| `rem` (default/else) | Cakram rem (kaliper + cakram merah) |

Pemakaian:

```latte
{include accessoryIcon, type: $item['ikon'], uid: 'acc' . $iterator->counter}
```

## Pola dekoratif khas

- **Pita merah diagonal**: abs `div` + `-skew-x-12` + `bg-gradient-to-b from-brand-500 via-brand-600 to-brand-800`.
- **Hero split**: panel terang `neutral-200` di kanan (desktop) + pita merah + strip tipis gelap.
- **Banner miring**: `md:[clip-path:polygon(0_0,100%_0,86%_100%,0_100%)]` di panel `brand-600`.
- **Nomor kartu**: badge `font-mono` di sudut (dipakai di Motor/Aksesoris/Brand).

## Contoh palet netral

Hampir semuanya `neutral-100` (terang) dan `neutral-800/900/950` (gelap), plus `neutral-700`,
`neutral-500`, `neutral-400` untuk garis/teks kedua.
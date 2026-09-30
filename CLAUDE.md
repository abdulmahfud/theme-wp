# CLAUDE.md — Tema WordPress Portal Berita (standar bersama)

Repo ini berisi **banyak tema WordPress untuk portal berita**. Satu folder per tema (`m-nata/`, dst.). File ini berlaku untuk semua tema; aturan khusus tema ada di `<tema>/CLAUDE.md`.

## Prioritas utama: KECEPATAN
Portal berita hidup dari trafik mobile dan organik, jadi **kecepatan mengalahkan kemewahan visual**. Setiap fitur harus lolos anggaran performa (lihat skill 6). Jika sebuah fitur menambah JS/CSS/request/query, cari cara yang lebih ringan dulu; kalau tidak ada, jadikan opsional dan default-nya mati.

## Struktur folder

```
project-theme-wp/
  CLAUDE.md              # standar bersama (file ini)
  m-nata/
    CLAUDE.md            # aturan khusus tema M-Nata
    referensi/           # HANYA BACAAN: tema Nomina + screenshot. Jangan edit, jangan salin mentah.
    m-nata/              # kode tema (folder ini yang di-zip -> m-nata.zip untuk WordPress)
  bakar/                 # bukan bagian tema, jangan disentuh
```

Tema baru = folder baru di root dengan pola yang sama (`<tema>/referensi`, `<tema>/<tema>`, `<tema>/CLAUDE.md`).

## Bahasa

- **UI admin (Customizer, widget, label, deskripsi): Bahasa Indonesia**, lewat text domain tema (`__()`, `esc_html__()`), siap diterjemahkan.
- **Kode, nama fungsi/class/variabel, komentar: Bahasa Inggris.**
- Komunikasi dengan pengguna: Bahasa Indonesia.

## Skill yang dibutuhkan (dan aturan praktisnya)

### 1. Pengembangan tema WordPress
- Ikuti template hierarchy; template hanya menyusun struktur, logika ada di `inc/` dan `template-parts/`.
- Query lewat `WP_Query` / `pre_get_posts`; jangan `query_posts`. Selalu `wp_reset_postdata()`.
- Aset lewat `wp_enqueue_style/script` (versi = `wp_get_theme()->get('Version')`), script `defer` (`strategy`), tanpa jQuery.
- Query berat (populer, trending, related) dan output widget di-cache dengan transient dan di-invalidate pada `save_post`/`deleted_post`/perubahan term.
- Friendly untuk child theme: fungsi dibungkus `if ( ! function_exists() )` hanya untuk template tag yang memang boleh di-override.

### 2. Customizer API
- Semua opsi lewat `WP_Customize_Manager`, dikelompokkan dalam satu panel tema.
- Setiap setting **wajib** punya `sanitize_callback`. Setting yang hanya mengubah tampilan memakai `transport => 'postMessage'` (JS preview kecil) atau selective refresh.
- Custom control untuk: pilih kategori/tag, preset ukuran banner (ukuran + rasio), urutan jaringan sosial, swatch gradient.
- Warna/font dirender **sekali** sebagai CSS variables di `:root` (`wp_head`, inline kecil). Dilarang if/else panjang per pilihan warna.

### 3. Widget API
- Widget = class turunan `WP_Widget` (dibungkus abstract class tema agar form/sanitasi/cache seragam).
- Semua konten sidebar dan home adalah widget; **tidak ada blok hardcode** di template.
- `update()` mensanitasi semua input; `widget()` meng-escape semua output; dukung selective refresh (`customize_selective_refresh`).
- Satu renderer bersama untuk daftar berita + `template-parts/card-*.php` per layout. Menambah layout baru = menambah satu template part + satu opsi.
- Output widget di-cache (transient per instance+argumen) dan tidak dicache untuk preview Customizer.
- Setiap widget area punya `description` yang menyebut fungsi dan ukuran/rasio yang disarankan.

### 4. Front-end portal berita
- Mobile-first, CSS variables, CSS Grid/Flexbox, `aspect-ratio` untuk semua thumbnail dan slot iklan.
- **JS vanilla, tanpa jQuery, tanpa library slider.** Slider/carousel = CSS `scroll-snap` + JS kecil (dots, autoplay opsional). Tidak ada Swiper/Owl/Flexslider.
- Gambar: `loading="lazy"` + `decoding="async"` kecuali gambar LCP (`fetchpriority="high"`), `width`/`height` selalu diisi, `srcset/sizes` benar, jumlah `add_image_size` minimum (target ≤ 6).
- Ikon = SVG inline/sprite tunggal, bukan file gambar per ikon dan bukan icon font.
- Referensi pola UI berita: header + menu + ticker breaking news, slider headline, list berita bertumpuk, blok kategori, sidebar trending bernomor, banner samping, single dengan share + related + "Baca Juga".

### 5. SEO dan markup berita
- JSON-LD `NewsArticle` + `BreadcrumbList` di single; Open Graph + Twitter Card; `<title>` lewat `add_theme_support('title-tag')`.
- Deteksi plugin SEO (Yoast, Rank Math, dsb.) dan **jangan menduplikasi** meta/schema jika plugin aktif.
- Heading benar: satu `<h1>` per halaman; judul kartu berita `<h2>/<h3>`; `<time datetime>`; breadcrumb semantik.

### 6. Performa (Core Web Vitals) — anggaran wajib
Target mobile (Lighthouse/PageSpeed): **LCP < 2,5 s, CLS < 0,1, INP < 200 ms**, skor Performance ≥ 90 dengan plugin cache standar.

Anggaran per halaman (tema saja, di luar iklan/plugin):
- CSS ≤ 30 KB gzip, **satu** file render-blocking. JS ≤ 10 KB gzip, semua `defer`, **tanpa jQuery**.
- Request font eksternal = 0 secara default: system font stack. Font kustom hanya self-host `woff2` subset, `font-display: swap`, `preload` untuk 1 file saja. Dilarang Google Fonts dari CDN.
- Tanpa library pihak ketiga (slider, animasi, ikon). Tanpa CDN.

Aturan:
- **Bersihkan bloat WordPress** di front-end: emoji script/style, `wp-embed`, `wp-block-library`/global styles/`classic-theme-styles` (jika tema tidak memakai block), `jquery-migrate`, `wlwmanifest`, `rsd_link`, `wp_generator`, `shortlink`. Semua dilakukan di `inc/performance.php`, dengan opsi untuk mengembalikan bila perlu.
- **Query hemat**: `no_found_rows => true` bila tidak ada pagination, `ignore_sticky_posts => true`, `update_post_meta_cache => false`, ambil hanya jumlah post yang ditampilkan. Satu widget daftar berita = paling banyak satu query. Home tanpa cache ≤ 30 query total (cek dengan Query Monitor).
- **Cache**: output widget dan query populer/related/trending di transient (atau object cache jika ada); invalidasi saat post berubah. Kompatibel dengan plugin page cache (tidak ada nonce/data per-user di HTML publik).
- **Views/populer**: jangan menulis ke DB di setiap request halaman ter-cache. Gunakan `comment_count`/sumber plugin, atau hit counter asinkron (`sendBeacon`) yang di-batch.
- **LCP**: gambar LCP (slider utama / foto artikel) tanpa `lazy`, dengan `fetchpriority="high"`; tidak ada CSS/JS yang menunda render.
- **Tanpa CLS**: slot iklan, thumbnail, embed punya rasio/min-height tetap; font fallback metrik seimbang.
- **Muat sesuai kebutuhan**: JS/CSS komponen berat hanya di halaman yang memakainya (mis. JS share hanya di single).
- **Iklan**: slot di bawah lipatan (below the fold) memuat kode iklan lazy (IntersectionObserver); ruangnya tetap dicadangkan.
- Resource hint (`preconnect`/`preload`) hanya untuk host yang benar-benar dipakai di halaman itu.
- Setiap PR fitur baru mencatat dampak ukuran CSS/JS dan jumlah query.

### 7. Iklan (banner)
- Ukuran standar: 728×90, 970×90, 970×250, 300×250, 336×280, 300×600, 160×600, 320×100, 320×50, 468×60.
- Setiap slot menampilkan **ukuran + rasio** di Customizer/widget (mis. `300×250 · 6:5`), menyimpan gambar + URL + alt **atau** kode HTML/AdSense (disanitasi hanya untuk user dengan `unfiltered_html`).
- Link iklan `rel="sponsored noopener"`; wadah memakai `aspect-ratio`/`min-height` agar tidak menyebabkan CLS; opsi tampil desktop/mobile (CSS `display:none` **tidak** cukup untuk kode iklan — render server-side hanya bila perlu atau lazy).

### 7a. Lisensi tema
- Tema yang dijual berbayar **wajib menegakkan lisensi**: tanpa lisensi aktif, situs publik menampilkan halaman "lisensi diperlukan" (bukan tampil normal) dan fitur tema mati; wp-admin tetap bisa dipakai untuk mengaktifkan. Jawaban server ditandatangani (Ed25519) dan diverifikasi di tema agar server palsu tidak bisa membuka tema begitu saja.
- Re-check status ke server **harian** (WP-Cron) dengan masa toleransi gangguan beberapa hari (server turun sesaat tidak boleh langsung mengunci pelanggan sah). Host pengembangan (`*.test`, `*.local`, `localhost`, ...) dibebaskan dari pemeriksaan.
- Pembaruan tema otomatis (cek versi, unduh, verifikasi checksum) hanya untuk lisensi aktif — ini nilai jual utama lisensi selain aktivasi itu sendiri.
- Batasan jujur untuk dicatat di setiap dokumen lisensi tema: tema WordPress berlisensi GPL, siapa pun yang memegang kodenya bisa menghapus pemeriksaan ini secara teknis; nilai nyata lisensi ada pada pembaruan dan dukungan yang dikendalikan server, bukan pada ketidakmungkinan mutlak untuk dibobol.
- Lihat `m-nata/CLAUDE.md` (bagian Lisensi/Pembaruan tema) dan `m-nata/wp-be/permintaan-ke-backend.md` sebagai contoh kontrak yang sudah dibangun dan diuji.

### 8. Keamanan WordPress
- **Escape saat output, sanitasi saat input.** `esc_html`, `esc_attr`, `esc_url`, `wp_kses_post`; jangan `echo get_theme_mod()` mentah (pola buruk di referensi).
- Aksi admin/AJAX: nonce + `current_user_can()`. Tidak ada `eval`, tidak ada `$_GET/$_POST` tanpa sanitasi.
- Tidak ada credential/API key di kode tema.

### 9. Aksesibilitas (WCAG AA)
- Skip link, landmark (`header/nav/main/aside/footer`), fokus terlihat, kontras cukup (teks di atas gradient dihitung otomatis), `aria-label` di slider/menu/hamburger, tombol bisa dioperasikan keyboard, `prefers-reduced-motion` untuk animasi/ticker.

### 10. i18n
- Text domain = slug tema. Tidak ada string UI tanpa fungsi terjemahan. File `.pot` di `languages/`.

### 11. Standar kode & QA
- WordPress Coding Standards (PHPCS ruleset `WordPress`), prefix fungsi/hook/handle/opsi = prefix tema (M-Nata: `mnata_`), class `MNata_*`.
- File kecil satu tanggung jawab; tidak ada `functions.php` raksasa (Nomina: 115 KB — jangan ditiru). `functions.php` hanya `require` file di `inc/`.
- Cek: `php -l` semua file, PHPCS, Theme Check / Plugin Check, `WP_DEBUG` + `SCRIPT_DEBUG` tanpa notice, uji PHP 7.4 dan 8.x, Lighthouse mobile.

## Cara memakai referensi
- Tujuan: mempelajari **layout, fitur, dan alur** dari tema referensi. Tulis ulang secara bersih.
- Jangan menyalin kode, CSS, gambar, ikon, atau nama/merek dari referensi (Nomina/Baturetno Studio). Semua aset (ikon SVG, logo) dibuat sendiri atau berlisensi GPL/MIT yang dicatat di `readme.txt` tema.
- Perilaku hardcode di referensi (blok home dengan urutan post tetap, banyak file sidebar banner, kategori di setiap block via Customizer, jQuery + 4 library slider) **diganti** oleh widget + widget area + CSS scroll-snap.

## Definition of Done (per fitur)
- Semua output di-escape, semua input disanitasi.
- Bekerja di 1920 px dan 412 px; diuji dengan data kosong, judul sangat panjang, tanpa featured image.
- Tidak ada PHP notice/warning dengan `WP_DEBUG`; PHPCS bersih.
- **Lolos anggaran performa** (skill 6): ukuran CSS/JS dan jumlah query tidak melewati batas.
- Opsi Customizer/widget berlabel Indonesia dan punya default masuk akal (situs baru langsung tampak jadi).
- Perubahan tampilan yang terlihat diverifikasi di browser (bukan hanya `php -l`).

## Membuat tema baru
1. Salin skeleton dari `m-nata/m-nata/` ke `<tema>/<tema>/`.
2. Ganti slug, text domain, prefix `mnata_`/`MNata_`/`--mn-`, nama tema di `style.css`.
3. Ubah design tokens (warna, gradient preset, font, radius) dan default widget; jangan ubah arsitektur `inc/`.
4. Buat `<tema>/CLAUDE.md` yang hanya memuat perbedaan dari file ini.

## Dokumentasi library
Gunakan MCP **Context7** (`resolve-library-id` lalu `query-docs`) untuk dokumentasi terbaru library/framework (WordPress, dsb.) sebelum menulis kode yang bergantung pada API-nya.

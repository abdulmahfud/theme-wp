# CLAUDE.md — Tema M-Nata

Tema portal berita WordPress **yang mengutamakan kecepatan**. Standar umum, daftar skill, anggaran performa, dan Definition of Done ada di `../CLAUDE.md` — file ini hanya memuat hal yang spesifik untuk M-Nata.

- Referensi: `referensi/nomina/` (tema Nomina, hanya bacaan), `referensi/screenshot/` (home & single, desktop & mobile), demo https://demo.baturetnostudio.com/nomina/
- Kode tema: `m-nata/m-nata/` (folder proyek `m-nata`, subfolder tema `m-nata`; di-zip menjadi `m-nata.zip` lewat `python build-zip.py`)
- Uji lokal: WordPress di `C:\laragon\www\mnata` (DB MySQL Laragon `mnata`, root tanpa password), URL **http://mnata.test** (vhost otomatis Laragon; `home`/`siteurl` = `http://mnata.test`). Login dan detail lain ada di `note.md`. Tema di-*junction* ke folder tema sehingga edit langsung terlihat; bila folder proyek diganti nama, buat ulang: `mklink /J C:\laragon\www\mnata\wp-content\themes\m-nata <path-proyek>\m-nata\m-nata`.
  WP-CLI: `php <path>/wp-cli.phar` dari folder WordPress. Screenshot: Edge headless; untuk mobile bungkus dalam iframe 412 px (jendela headless minimal ±500 px).
  Wajib untuk AMP: plugin resmi AMP terpasang di situs uji (mode Transitional); `wp amp validation run --limit=3` harus menghasilkan **0 issue**.

## Identitas
- Theme Name `M-Nata`, slug & text domain `m-nata`, prefix fungsi/hook/opsi `mnata_`, class `MNata_*`, konstanta `MNATA_*`, CSS variable `--mn-*`, CSS class `mn-*`.
- Requires WP 6.3+ (butuh `strategy => defer` pada `wp_enqueue_script`), PHP 7.4+. Lisensi GPL v2+.

## Prinsip desain
1. **Kecepatan dulu** (anggaran di `../CLAUDE.md` skill 6): CSS ≤ 30 KB gz, JS ≤ 10 KB gz, 0 jQuery, 0 library slider, 0 font eksternal.
2. **Semua konten di home dan sidebar adalah widget.** Tidak ada blok hardcode, tidak ada kategori yang dikunci di template.
3. **Semua opsi lewat Customizer/Widget.** Tanpa halaman opsi terpisah.
4. **Warna = CSS variables**, satu blok inline di `<head>`. Tidak ada CSS dinamis per selector.
5. Slider/carousel = CSS scroll-snap + `assets/js/main.js` kecil. Ikon = SVG sprite inline yang hanya memuat ikon yang dipakai halaman itu (`inc/icons.php`). Ikon merek: Simple Icons (CC0) + Bootstrap Icons (MIT, untuk LinkedIn/Email/Link), dibuat ulang lewat `tools/build-icons.js` + `tools/patch-icons.py`; catat sumber/lisensi di `readme.txt`. Ikon merek harus glyph solid `currentColor` viewBox 24 (bukan garis tipis).

## Skema warna gradient (Customizer > M-Nata > Warna)
- **Preset gradient** (swatch): pilih dari ~12 kombinasi siap pakai (mis. Ungu–Magenta seperti referensi, Biru Laut, Merah–Jingga, Hijau Tosca, Hitam Elegan, dsb.).
- **Kustom**: 2–4 titik warna (Warna 3 & 4 opsional), arah gradient (kiri→kanan, atas→bawah, diagonal 135°/45°), aktif bila preset = "Kustom".
- **Terapkan gradient ke** (toggle): bar menu, footer, judul blok, tombol, latar Blok Kategori. Elemen yang tidak diaktifkan memakai warna solid `--mn-c1`.
- Output CSS variables: `--mn-c1..--mn-c4`, `--mn-grad`, `--mn-accent` (= c1), `--mn-on-grad` (warna teks di atas gradient, dihitung server dari luminansi terburuk agar kontras ≥ 4,5:1).
- Setting warna memakai transport `refresh` (bukan `postMessage`) agar logika gradient + kontras hanya ada satu di PHP dan kontrol kustom bisa tampil/sembunyi lewat `active_callback`.
- Gradient murni CSS: **tidak ada gambar** dan tidak ada request tambahan.

## Struktur file
```
m-nata/m-nata/
  style.css  functions.php  screenshot.png  readme.txt
  header.php footer.php index.php front-page.php home.php single.php page.php
  archive.php category.php tag.php author.php search.php 404.php searchform.php comments.php sidebar.php
  inc/
    setup.php enqueue.php performance.php image-sizes.php helpers.php icons.php template-tags.php seo.php
    bmkg.php   (data BMKG: cache + fallback stale + cron; proxy admin-ajax daftar wilayah)
    meta.php related.php breadcrumbs.php sharing.php content.php   (Info Berita, terkait, share, sisip Baca Juga + banner dalam artikel)
    license/ config.php license.php gate.php updates.php admin.php   (lisensi: klien, penguncian, pembaruan tema, layar Tampilan > Lisensi)
    amp.php stats.php   (dukungan AMP; statistik pengunjung: REST beacon + counter atomik)
    demo/ data.php importer.php admin.php   (Impor Demo satu klik, hanya dimuat di admin/WP-CLI)
    ads.php admin.php   (ukuran banner + rasio, slot lazy; picker gambar di form widget)
    widget-areas.php render.php   (area registrasi + mnata_area(); renderer kartu mnata_render_posts(), mnata_run_query())
    sharing.php ads.php breadcrumbs.php related.php cache.php
    customizer/  panel.php colors.php social.php banners.php layout.php sanitize.php controls.php
    widgets/     init.php abstract-widget.php slider.php post-list.php category-block.php
                 banner-ad.php social-follow.php tag-cloud.php html-embed.php
  template-parts/
    card-list.php card-grid.php card-overlay.php card-numbered.php card-compact.php card-slide.php
    header/ footer/ single/
  assets/ css/main.css  js/main.js  js/slider.js + js/slot.js + js/visit.js (dimuat hanya jika dipakai) js/demo-import.js  js/admin.js (khusus admin)  img/
  languages/
```
`functions.php` hanya berisi `require_once` ke `inc/`.

## Widget area
| ID | Lokasi | Ukuran/rasio yang disarankan |
|---|---|---|
| `header-banner` | Di bawah header, lebar penuh | 970×90 / 728×90 (desktop), 320×100 (mobile) |
| `side-banner-left`, `side-banner-right` | Samping kiri/kanan konten, desktop lebar ≥1400 px | 160×600 (4:15) |
| `home-top` | Home, lebar penuh (umumnya slider headline) | — |
| `home-main` | Home, kolom utama; urutan widget bebas | — |
| `home-sidebar` | Home, sidebar kanan | 300×250, 300×600 untuk banner |
| `single-sidebar` | Single, sidebar kanan | idem |
| `single-in-article` | Disisipkan setelah paragraf ke-N artikel (N dari Customizer) | 300×250 / 336×280 |
| `single-after-content` | Setelah isi artikel | 728×90 / 300×250 |
| `footer-1..3` | Footer | — |

## Widget M-Nata (grup "M-Nata")
| Widget | Opsi utama |
|---|---|
| Slider Headline | Sumber (tag/kategori/sticky), jumlah, model: slider+thumbnail, slider penuh, carousel 3 kolom |
| Daftar Berita | Sumber (terbaru/populer/acak/kategori/tag), layout: list, kecil, bernomor (Trending), grid 2/3 kolom, 1 besar + list; opsi feed dengan pagination ("Berita Terkini") |
| Blok Kategori | Kategori, layout (grid/carousel/overlay), latar (gradient tema / solid / kustom), tautan "Lihat semua" |
| Banner Iklan | Preset ukuran (menampilkan ukuran + rasio), gambar+URL+alt atau kode HTML/AdSense, label "Advertisement", tampil desktop/mobile |
| Sosial Follow | Jaringan aktif dari Customizer |
| Tag / Topik | Jumlah, gaya |
| HTML / Embed | HTML bebas untuk widget pihak ketiga (skor, cuaca, dsb.) |

Semua widget daftar berita memanggil satu renderer `mnata_render_posts( $args )` dan `template-parts/card-*.php`. Layout baru = satu template part + satu entri di daftar layout. Output tiap widget di-cache (lihat `inc/cache.php`).

## Customizer — panel "M-Nata"
- **Warna**: skema gradient (di atas), warna aksen/teks.
- **Social Media Share**: aktif/nonaktif per jaringan (Facebook, X, WhatsApp, Telegram, LINE, LinkedIn, Pinterest, Email, Salin Link), urutan, posisi (atas artikel / bawah / melayang di mobile), gaya ikon, template teks share (`{title}`, `{url}`). Share memakai link `<a href>` biasa (tanpa SDK/JS pihak ketiga); JS hanya untuk "Salin Link".
- **Social Media Follow**: URL akun; dipakai header, footer, widget Sosial Follow.
- **Banner Iklan**: slot header, samping 160×600, dalam artikel (setelah paragraf ke-N), setelah konten. Setiap slot: preset ukuran dengan keterangan ukuran & rasio (`970×250 · 3.88:1`, `300×250 · 6:5`, `160×600 · 4:15`, `728×90`, `320×100`), gambar+URL+alt atau kode HTML, tampil desktop/mobile.
- **Header** (ticker breaking news, sticky), **Footer** (alamat, copyright, menu jaringan media), **Artikel** (penulis, waktu, zona waktu, jumlah pembaca, related, caption foto), **Home** (jumlah post, pagination), **Performa** (toggle pembersihan bloat WP).

## Arsitektur widget (sudah dibangun, Fase 3)
- Semua widget turunan `MNata_Widget` (`inc/widgets/abstract-widget.php`): cukup definisikan `fields()` (skema: text/slug/number/select/checkbox/category) dan `render()`. Form admin, sanitasi `update()`, cache fragmen, dan selective refresh sudah otomatis. Widget baru = 1 file + 1 baris `register_widget()` di `inc/widgets/init.php`.
- Output widget di-cache (transient, 5 menit, di-flush saat post/term berubah, dilewati di preview Customizer). Ikon yang dipakai fragmen ikut disimpan (`mnata_icon_log`) supaya sprite tetap lengkap saat cache hit. Aset widget (mis. `slider.js`) di-enqueue di `assets()` yang selalu jalan, bukan di `render()`.
- `front-page.php`: area kosong diberi default (`home-main` = Slider + Berita Terkini berpaginasi, `home-sidebar` = Trending) lewat `the_widget()` dengan `mnata_widget_args( true )`. Query utama front page sengaja diperkecil (`mnata_lean_home_query`) karena widget menjalankan query sendiri.
- Slider = CSS scroll-snap; `slider.js` (~1,1 KB gzip) mengurus prev/next, dot/thumbnail, autoplay (berhenti saat hover/tab tersembunyi/di luar layar, mati bila `prefers-reduced-motion`).
- Kartu tidak memakai `post_class()` (per-post query term + kelas panjang). Query widget wajib lewat `mnata_run_query()` (memprime thumbnail dalam 1 batch) dan `update_post_meta_cache => true`.
- Terukur di lokal (7 widget di home): 21 query dengan cache hangat, ±64 dengan cache dingin; CSS 4,1 KB gzip, JS 0,4 KB + 1,1 KB (slider).
- Banner Iklan (Fase 4): banner = widget, bukan slot di Customizer. Customizer > Banner Iklan hanya menyimpan opsi global (label banner, paragraf sisip dalam artikel) dan daftar ukuran + rasio; ukuran + rasio juga tampil di pilihan widget (`inc/ads.php`). Gambar memakai media picker (`admin.js`), kode iklan/HTML disimpan di `<template>` dan baru disuntik oleh `slot.js` saat mendekati layar dan sesuai perangkat (desktop/mobile), sehingga skrip pihak ketiga tidak menghambat render. Wadah selalu punya `aspect-ratio`/`min-height` sesuai ukuran (tanpa CLS). Kode mentah hanya tersimpan utuh untuk pengguna dengan `unfiltered_html`, selain itu di-`wp_kses_post`. `document.write` di kode iklan tidak akan jalan (disuntik setelah load).
- Tipe field baru di `MNata_Widget`: `url`, `color`, `html`, `image`. Blok Kategori: latar `theme` (mengikuti gradient + toggle "Latar Blok Kategori" di Customizer) atau `solid` (kontras teks dihitung otomatis).
- Single (Fase 5): `single.php` + `comments.php` (tanpa avatar/Gravatar demi kecepatan). Meta box "Info Berita" (sub-judul, penulis, editor, sumber) di `inc/meta.php`. Share: link biasa per jaringan, diatur di Customizer (jaringan, urutan, posisi atas/bawah/keduanya, bar melayang mobile, gaya, label, template teks `{title}`/`{url}`); hanya "Salin Link" memakai JS (di `main.js`). `inc/content.php` menyisipkan "Baca Juga" (1 berita terkait) dan area `single-in-article` setelah paragraf ke-N lewat filter `the_content` (tidak pernah setelah paragraf terakhir). Berita terkait: ID di-cache 30 menit, HTML grid di-cache lewat `mnata_cache_fragment()`. SEO (`inc/seo.php`): OG/Twitter/description + JSON-LD `NewsArticle` & `BreadcrumbList`, otomatis nonaktif bila Yoast/Rank Math/AIOSEO/SEOPress/TSF aktif. Terukur: single 35 query dengan cache hangat (77 dingin), 1 `<h1>`, gambar utama `eager` + `fetchpriority=high`.
- Template lain (Fase 6): `template-parts/archive.php` adalah satu-satunya isi untuk kategori, tag, author, tanggal, hasil pencarian, dan halaman posting; `index.php`, `archive.php`, `search.php` hanya pembungkus tipis (category/tag/author sengaja tidak punya file sendiri). Header arsip memakai label ("Kategori", "Topik", "Penulis", "Pencarian") + `get_the_archive_title_prefix` dikosongkan. `mnata_sidebar( $area )` dipakai semua template berkolom (home, single, arsip, 404) dan memberi default Trending bila area kosong. `page.php` = satu kolom baca (tanpa sidebar). `404.php` + `content-none.php` memberi kotak cari dan berita terbaru. Halaman depan memakai `<h1>` di logo/judul situs (`mnata_logo()`), footer tidak.
- Widget BMKG (setelah Fase 7): `Prakiraan Cuaca` (`weather.php`) dan `Info Gempa` (`earthquake.php`), keduanya opsional lewat widget area (tambah/hapus kapan saja). Sumber: `api.bmkg.go.id/publik/prakiraan-cuaca?adm4=` (butuh kode wilayah tingkat IV/desa, contoh `31.71.03.1001`) dan `data.bmkg.go.id/DataMKG/TEWS/{autogempa|gempaterkini|gempadirasakan}.json`. Pemilih wilayah di form widget = 4 dropdown berjenjang (tipe field `wilayah`) yang membaca daftar dari `wilayah.id` lewat proxy `admin-ajax` (`mnata_wilayah`, nonce + `edit_theme_options`, cache 30 hari); kode bisa juga ditempel manual. Aturan kecepatan: pengunjung tidak menunggu BMKG bila salinan lama ada — transient (gempa 10 menit, cuaca 1 jam) + salinan basi di option + kegagalan diingat 1 menit + WP-Cron tiap 10 menit menyegarkan data yang dipakai (`mnata_bmkg_cron`). Widget ini `$ttl = 0` (tanpa cache HTML) supaya keadaan error tidak ikut tersimpan. Peta guncangan (±230 KB) default mati. Cuaca memakai ikon SVG sendiri (`w-*`, varian malam) — tidak ada gambar/ikon dari server BMKG. Atribusi "Sumber: BMKG" wajib ditampilkan. Uji outage: salinan basi tersaji dan hanya 1 percobaan HTTP.
- Batas judul: semua kartu dan caption slider dipotong dengan CSS `line-clamp` memakai variabel `--mn-lines` yang diisi renderer (`mnata_render_posts`/`mnata_render_slider`). Bawaan: daftar 3 baris, ringkas/trending/grid/1-besar 2 baris, slider 2 baris. Widget Daftar Berita & Blok Kategori punya opsi "Batas baris judul" (0 = otomatis), Slider punya opsi 1-6. Judul penuh tetap ada di HTML (aman untuk pembaca layar dan SEO).
- Banner samping 160×600 (`side-banner-left/right`) bersifat sticky: `mnata_side_banners()` membungkus tiap sisi dalam kolom setinggi konten (`.mn-side`, absolute) berisi `.mn-side__in` (`position: sticky`). Posisi atasnya = tinggi header lengket + 16 px; tinggi header diukur `main.js` ke variabel CSS `--mn-header-h` (tanpa layout shift). Bisa dimatikan di Customizer > Banner Iklan (`mnata_side_sticky`, class body `mn-side-sticky`). Hanya tampil ≥ 1560 px; di layar pendek (< ±760 px tinggi) bagian bawah banner bisa terpotong karena header lengket.
- Jebakan Customizer: `WP_Widget::update()` **tidak boleh menulis option lain** (mis. `update_option`), kalau tidak Customizer menolak simpan widget dengan error `widget_setting_too_many_options` ("An error has occurred. Please reload the page and try again."). Karena itu `MNata_Widget::update()` menunda flush cache ke hook `shutdown`. Semua 9 widget diuji lewat jalur `call_widget_update` Customizer.
- Sidebar artikel/arsip/404: `mnata_sidebar( $area )` memakai widget area miliknya; bila kosong **jatuh ke widget `home-sidebar`** (baru bila itu kosong juga → default Trending). Jadi situs baru tampil sama di home dan artikel tanpa mengisi dua kali.
- Banner sidebar sticky: widget Banner Iklan punya opsi "Tempel saat halaman digulir". Kelas `mn-sticky-ad` hanya menempel bila widget itu **paling bawah** di sidebar (`:last-child`), supaya tidak pernah menutupi widget lain; kolom sidebar direntangkan setinggi konten (`.mn-layout__side` flex + `.mn-area` `flex:1`) agar sticky punya ruang gerak. Posisi = tinggi header lengket + 16 px. Diuji: iklan 300×600 tetap di 157 px dari atas saat digulir di home dan artikel. Banner 160×600 kiri/kanan sticky terpisah (lihat di atas).
- **Impor Demo (Tampilan > Impor Demo)**: satu klik membuat situs tampil seperti demo — 30 artikel fiktif ±500 kata (6 kategori × 5, `demo/articles/*.txt`, format `### Judul | YYYY-MM-DD | tag1, tag2`, paragraf dipisah baris kosong, `## ` = h2) dengan gambar unggulan buatan GD, 5 halaman, menu Utama/Footer/Jaringan Media, 21 widget (data di `inc/demo/data.php`), Customizer, logo (`demo/logo.png`, salinan dari `referensi/logo/logo.png`), judul/slogan, permalink `/%postname%/` + zona Asia/Jakarta, dan membersihkan "Hello world!"/"Sample Page". Tiap bagian bisa dicentang. Berjalan sebagai langkah AJAX kecil (media → struktur → 1 artikel per request → widget → pengaturan → selesai) dengan progress bar, jadi aman di shared hosting. Aman diulang (artikel dilewati lewat meta `_mnata_demo_key`); semua yang dibuat diberi meta `_mnata_demo` sehingga tombol "Hapus konten demo" bisa menghapusnya (post, halaman, media, widget, logo). Notice ajakan impor muncul di Dashboard/Tema sampai diimpor atau ditolak. CLI: `wp eval "mnata_demo_run_all();"`. Diuji end-to-end lewat layar admin asli pada situs kosong (30 artikel, 5 halaman, 20+ widget, ±40 detik), termasuk hapus lalu impor ulang. Ubah `data.php`/`articles/` untuk mengganti demo; `tools/count-words.py` dan `tools/extend.py` membantu mengatur panjang artikel.
- **Widget Statistik Pengunjung** (`visitors.php` + `inc/stats.php`): online (5 menit), hari ini, kemarin, bulan ini, total, tayangan; angka awal opsional. Penghitungan lewat beacon `POST/GET /wp-json/mnata/v1/hit` (bukan saat render, jadi aman dengan page cache): `visit.js` (0,4 KB, menghormati Do Not Track, id acak di localStorage) atau `<amp-pixel>` di AMP. Tanpa menyimpan IP: id → hash satu arah dalam transient (online 5 menit, unik-harian 24 jam); counter = `UPDATE ... option_value + 1` atomik pada option non-autoload (`mnata_c_*`), dipangkas setiap hari (>60 hari). Bot (UA), id tak valid, editor yang login, dan halaman tanpa widget aktif diabaikan (`is_active_widget`). Nomor dibaca dalam 1 query + cache 1 menit.
- **AMP** (`inc/amp.php`): `add_theme_support( 'amp' )` mode Transitional (`paired`), bisa dipilih Standard; menu mobile memakai `nav_menu_toggle` bawaan plugin (`#mn-toggle` / `#mn-nav`, kelas `is-open`). Di AMP tema berhenti mengeluarkan yang tak boleh ada: semua skrip (main/slider/slot/visit + flag `js` + speculation rules), atribut `loading` pada gambar, `aria-roledescription`, kode iklan/HTML embed, tombol Salin Link dan bar bagikan melayang; kontrol slider disembunyikan (geser tetap jalan via CSS scroll-snap); logo dibatasi lewat `amp-img`. Body class `mn-amp` mengaktifkan CSS khusus AMP. Hasil `wp amp validation run` pada Standard **dan** Transitional: **0 issue** (16 + 12 URL), menu toggle dan tanpa overflow diuji di browser (320–1440 px). Aturan: jangan menambah `!important`, `position: fixed`, `<template>`, atau JS wajib pada tampilan yang juga dipakai AMP.
- **Lisensi** (`inc/license/`): tema memverifikasi kunci ke `https://api.m-onetech.id/v1/licenses/{activate|verify}` (Laravel, sudah **hidup di produksi**; kontrak lengkap di `wp-be/permintaan-ke-backend.md`, tindak lanjut/perubahan kebijakan di `wp-be/permintaan-ke-dev-theme.md`, server tiruan untuk uji mekanika request `wp-be/mock/server.php`). Tiap jawaban ditandatangani **Ed25519**; tema memverifikasi dengan kunci publik tertanam (`MNATA_LICENSE_PUBKEY`) + `nonce` yang di-echo + `domain`/`product` + `issued_at` (±1 hari), sehingga server palsu tidak bisa membuka tema. Status disimpan di option `mnata_license` dengan segel HMAC (`wp_salt`) — diedit manual = dianggap tidak ada. Re-check **harian** lewat WP-Cron (`mnata_license_cron`) dan tombol "Periksa ulang sekarang" — dipilih harian (bukan mingguan) agar jendela waktu antara lisensi dicabut dan situs terkunci tetap pendek, tanpa membebani API (satu request kecil per situs per hari). **Penguncian**: tanpa lisensi `active` (atau `revoked/suspended/expired/domain_mismatch/invalid`, atau `unverified` = tak terverifikasi > `grace_days`+1 hari), `template_redirect` menampilkan `template-parts/license-locked.php` (HTTP 503, `Retry-After`, noindex) kepada pengunjung; admin (`manage_options`) tetap melihat situs + bilah merah; wp-admin, wp-login, REST, AJAX, cron, robots.txt tidak diblokir. Gangguan jaringan / 5xx / tanda tangan salah **tidak** mengunci seketika (toleransi `MNATA_LICENSE_GRACE_DAYS`=7). Host dev (`localhost`, `*.test`, `*.local`, ...) bebas lisensi; uji mekanika lokal dengan `define('MNATA_LICENSE_FORCE', true)` + `define('MNATA_LICENSE_API', 'http://127.0.0.1:8090/v1')` di `wp-config.php` dan jalankan `php -S 127.0.0.1:8090 wp-be/mock/server.php` (kunci uji ada di komentar server tiruan) — **catatan**: mock menandatangani dengan kunci pengembangannya sendiri, jadi sejak kunci produksi ditanam (di bawah) verifikasi tanda tangan terhadap mock akan gagal (diharapkan, bukan bug; mock hanya untuk menguji alur request, bukan tanda tangan produksi). Impor Demo butuh lisensi valid (host dev dikecualikan). **Lisensi terkunci permanen ke domain pertama** yang mengaktifkannya (keputusan produk, `wp-be/permintaan-ke-dev-theme.md` §5): tidak ada tombol "Nonaktifkan" di layar Lisensi — pindah domain sepenuhnya lewat dukungan/admin backend, bukan self-service dari tema (endpoint `deactivate` di backend tetap ada untuk tema versi lama, tapi versi ini tidak pernah memanggilnya). **Kunci di `inc/license/config.php` adalah kunci PRODUKSI** (`0aOnUW0LHdHqLW9WVlHCKHuKkMtqWn3U8leOvmAB9+U=`, dikonfirmasi 2026-09-29 dari backend yang sudah live) — jangan diganti sendiri tanpa koordinasi dengan backend (mengganti kunci tanpa kunci privat pasangannya membuat SEMUA lisensi aktif berhenti terverifikasi). Batasan jujur: tema WordPress berlisensi GPL, siapa pun yang punya kode dapat menghapus pemeriksaan ini; nilai nyata lisensi ada pada pembaruan/dukungan yang dikendalikan server. Sejarah: sempat dibongkar total (versi 1.1.0, tanpa enforcement apa pun), dikembalikan (2026-09-29), lalu diselaraskan dengan kebijakan kunci-domain-permanen dan kunci publik produksi asli (2026-09-29, `wp-be/permintaan-ke-dev-theme.md` §5 & §7).
- **Pembaruan tema** (`inc/license/updates.php`): hanya untuk lisensi `active`. Filter `pre_set_site_transient_update_themes` memanggil `POST {api}/products/m-nata/update` (jawaban bertanda tangan Ed25519 yang sama), menampilkan "pembaruan tersedia" di Tampilan > Tema, lalu `upgrader_pre_download` **memverifikasi SHA-256 zip** terhadap nilai bertanda tangan sebelum dipasang (checksum salah = batal dengan pesan jelas). Paket harus HTTPS dan berhost `api.m-onetech.id`/`*.m-onetech.id`; versi harus x.y.z dan lebih tinggi. Hasil di-cache 12 jam; tema disembunyikan dari pemeriksaan wordpress.org. Layar Lisensi menampilkan versi terpasang, ada tidaknya pembaruan, dan tombol "Cek pembaruan sekarang". Diuji pada instalasi WordPress bersih dari zip rilis terhadap server tiruan: tanpa rilis, rilis dengan checksum rusak (ditolak), rilis valid (1.0.0 -> 1.0.1 terpasang, situs dan lisensi tetap aktif), lisensi dicabut (tak ada pembaruan). Catatan uji lokal: WordPress memblokir host loopback/port aneh untuk unduhan aman, jadi uji lokal memerlukan mu-plugin yang mengizinkan 127.0.0.1:8090 (jangan dipakai di produksi).
- **Aset produksi**: `tools/build-assets.js` (csso + terser) membuat `*.min.css/js`; `mnata_asset()` (`inc/helpers.php`) otomatis memakai `.min` kecuali `SCRIPT_DEBUG`. Selalu jalankan ulang setelah mengubah CSS/JS (CSS 32,2 KB -> 25,5 KB, gzip 6,9 -> 5,7 KB).
- **Rilis** (`python tools/release.py`): gerbang + pengemas. Menolak build bila versi `style.css` != `Stable tag` readme, ada file PHP gagal `php -l`, file minify tidak ada/basi, `.pot` tidak ada, sisa debug (`var_dump`, `print_r`, `error_log`, `console.log`, `dd(`), API lisensi bukan https, atau **kunci publik lisensi masih kunci DEV** (`--allow-dev-key` hanya untuk build uji). Hasil: `dist/m-nata-<versi>.zip` + `.sha256`. Urutan rilis: naikkan versi (style.css + readme) -> `NODE_PATH=... node tools/build-assets.js` -> PHPCS + `php -l` -> `wp i18n make-pot` -> `python tools/release.py` -> unggah zip ke `wp-be` (perintah `license:release`, lihat dokumen backend) -> uji pasang dari zip di WordPress bersih.
- **Uji produksi (1.2.0)**: dipasang dari zip di WordPress bersih dengan penegakan lisensi aktif: terkunci tanpa lisensi -> aktivasi -> impor demo (30 artikel) -> pembaruan. Lighthouse mobile halaman depan: Performance 99, Accessibility 100, SEO 100, Best Practices 100 (LCP 2,0 dtk, CLS 0, TBT 0); artikel Performance 100, A11y 100, SEO 100. AMP: 0 isu (Transitional dan Standard).
- Belum ada: dedupe berita yang sudah tampil di slider (butuh keputusan karena bentrok dengan cache fragmen).

## QA (Fase 7) — hasil dan cara mengulang
- Hasil 2026-09-28: PHPCS (WordPress-Extra + WordPress-Docs + PHPCompatibilityWP 7.4+) **0 error, 0 warning**; `php -l` OK di 8.1/8.2/8.3 (7.4 dicek lewat PHPCompatibility, tidak ada PHP 7.4 lokal); Lighthouse mobile (Edge) home **100/100/100** (Performance/Accessibility/SEO), LCP 1,7 s, CLS 0, TBT 0; single Perf 98, A11y 100, SEO 100. Temuan Lighthouse yang tersisa hanya dari server lokal (tanpa HTTPS/HTTP2/kompresi/cache TTL) dan CSS tidak diminify.
- Uji data ekstrem lolos: judul sangat panjang + kata tanpa spasi, tanpa gambar, konten kosong, simbol/HTML/emoji di judul; tidak ada horizontal overflow di 320/412/768/1024/1440 px untuk home, single, kategori, pencarian, 404.
- Ulangi PHPCS: `composer require --dev squizlabs/php_codesniffer wp-coding-standards/wpcs phpcompatibility/phpcompatibility-wp dealerdirect/phpcodesniffer-composer-installer` di folder sementara, lalu `phpcs --standard=phpcs.xml.dist` dari `mnata/` (perbaikan format otomatis: `phpcbf`). `.pot`: `wp i18n make-pot <path>/m-nata <path>/m-nata/languages/m-nata.pot --slug=m-nata --exclude=assets`. Lighthouse: `npm i lighthouse`, `CHROME_PATH=<msedge.exe>`, `lighthouse http://mnata.test/ --form-factor=mobile`. Zip rilis: `python build-zip.py` → `dist/m-nata-<versi>.zip`.
- Belum: Theme Check/Plugin Check resmi (butuh plugin di WP), CSS/JS minify (build step), Author di `style.css` masih placeholder "M-Nata".

## Catatan kerja di Windows / Git Bash
- Git Bash mengubah argumen berawalan `/` menjadi path Windows (contoh: `wp rewrite structure '/%postname%/'` merusak permalink). Pakai `MSYS2_ARG_CONV_EXCL='*'` atau jalankan lewat PowerShell untuk argumen seperti itu.
- `--color` adalah flag global WP-CLI (mewarnai output) sehingga tidak bisa dipakai sebagai argumen widget; ubah nilai itu dengan `wp option patch update widget_<id_base> <n> color '#hex'`.
- `wp widget update` lewat WP-CLI memanggil `update()` hanya dengan field yang diberikan, sehingga field lain kembali ke default (mis. `count` jadi 1). Selalu kirim semua field, atau `widget delete` lalu `widget add` lengkap.

## Layout acuan (dari screenshot referensi)
- **Home desktop**: header (logo, search, ikon sosial) → menu utama bar gradient → ticker breaking news → banner header → banner 160×600 kiri/kanan → slider headline (gambar besar + thumbnail) → 2 kolom (kiri feed "Berita Terkini" disisipi banner/blok kategori, kanan sidebar Trending bernomor + widget lain) → "Index Berita" → footer gradient (logo, alamat, sosial, media network, menu bawah, copyright).
- **Single desktop**: breadcrumb → judul → sub-judul → penulis + tanggal + share → foto utama + caption → "Berita Terkait" kiri + isi + "Baca Juga" inline + slot iklan tengah artikel + penulis/editor/sumber → tag → komentar → grid "Berita Terbaru" → footer; sidebar kanan seperti home.
- **Mobile**: hamburger, slider headline, list thumbnail kecil (85×85), blok kategori jadi carousel, sidebar turun di bawah artikel.

## Fase pengerjaan (centang saat selesai)
- [x] 1. Skeleton (`style.css`, `functions.php`, `inc/setup|enqueue|performance|image-sizes`), design tokens, Customizer warna gradient
- [x] 2. Header, footer, menu, ticker, Customizer sosial follow, SVG sprite
- [x] 3. Widget area + renderer + cache + widget Daftar Berita & Slider Headline; `front-page.php`
- [x] 4. Widget Blok Kategori, Banner Iklan (+ Customizer banner), Sosial Follow, Tag, HTML
- [x] 5. Single: share (Customizer), related, "Baca Juga", slot iklan dalam artikel, komentar, JSON-LD
- [x] 6. Arsip, kategori, tag, author, search, 404
- [x] 7. QA: PHPCS, Theme Check, Lighthouse mobile, uji 1920 px & 412 px, data kosong, PHP 7.4/8.x

## Yang tidak boleh
- Menyalin file dari `referensi/` ke `m-nata/` (folder tema) tanpa menulis ulang; memakai nama, logo, atau teks "Nomina"/"Baturetno".
- Blok home hardcode, `echo get_theme_mod()` tanpa escape, jQuery, library slider, font/skrip dari CDN.
- Mengubah `../bakar/`.

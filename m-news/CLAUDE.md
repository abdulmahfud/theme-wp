# CLAUDE.md — Tema M-News

Tema portal berita WordPress **yang mengutamakan kecepatan**. Standar umum, daftar skill, anggaran performa, dan Definition of Done ada di `../CLAUDE.md` — file ini hanya memuat hal yang spesifik untuk M-News.

**Asal**: M-News dibuat dengan menyalin kerangka tema **M-Nata** (`../m-nata/`) lalu mengganti identitas (nama, slug, prefix `mnews_`/`MNews_`/`MNEWS_`, CSS `--mnw-*`/`mnw-*`) — lihat "Membuat tema baru" di `../CLAUDE.md`. Arsitektur `inc/`, semua widget, Customizer, sistem lisensi, dan Impor Demo **diwarisi apa adanya** dari M-Nata per tanggal salin (2026-09-30); dokumentasi di bawah menjelaskan kondisi warisan itu, bukan pekerjaan baru yang sudah dilakukan khusus untuk M-News. Belum ada `referensi/` sendiri untuk M-News — desain (warna, font, layout) masih identik dengan M-Nata sampai disesuaikan; kalau ada tema referensi baru untuk M-News, taruh di `referensi/` mengikuti pola tema lain.
- Kode tema: `m-news/m-news/` (folder proyek `m-news`, subfolder tema `m-news`; di-zip lewat `python tools/release.py`, lihat bagian Rilis)
- Uji lokal: WordPress di `C:\laragon\www\mnews` (DB MySQL Laragon `mnews`, root tanpa password), URL **http://mnews.test** (vhost otomatis Laragon; `home`/`siteurl` = `http://mnews.test`). Login dan detail lain ada di `note.md`. Tema di-*junction* ke folder tema sehingga edit langsung terlihat; bila folder proyek diganti nama, buat ulang: `mklink /J C:\laragon\www\mnews\wp-content\themes\m-news <path-proyek>\m-news\m-news`.
  WP-CLI: `php <path>/wp-cli.phar` dari folder WordPress. Screenshot: Edge headless; untuk mobile bungkus dalam iframe 412 px (jendela headless minimal ±500 px).
  Wajib untuk AMP: plugin resmi AMP terpasang di situs uji (mode Transitional); `wp amp validation run --limit=3` harus menghasilkan **0 issue**.

## Identitas
- Theme Name `M-News`, slug & text domain `m-news`, prefix fungsi/hook/opsi `mnews_`, class `MNews_*`, konstanta `MNEWS_*`, CSS variable `--mnw-*`, CSS class `mnw-*`.
- Requires WP 6.3+ (butuh `strategy => defer` pada `wp_enqueue_script`), PHP 7.4+. Lisensi GPL v2+.

## Prinsip desain
1. **Kecepatan dulu** (anggaran di `../CLAUDE.md` skill 6): CSS ≤ 30 KB gz, JS ≤ 10 KB gz, 0 jQuery, 0 library slider, 0 font eksternal.
2. **Semua konten di home dan sidebar adalah widget.** Tidak ada blok hardcode, tidak ada kategori yang dikunci di template.
3. **Semua opsi lewat Customizer/Widget.** Tanpa halaman opsi terpisah.
4. **Warna = CSS variables**, satu blok inline di `<head>`. Tidak ada CSS dinamis per selector.
5. Slider/carousel = CSS scroll-snap + `assets/js/main.js` kecil. Ikon = SVG sprite inline yang hanya memuat ikon yang dipakai halaman itu (`inc/icons.php`). Ikon merek: Simple Icons (CC0) + Bootstrap Icons (MIT, untuk LinkedIn/Email/Link), dibuat ulang lewat `tools/build-icons.js` + `tools/patch-icons.py`; catat sumber/lisensi di `readme.txt`. Ikon merek harus glyph solid `currentColor` viewBox 24 (bukan garis tipis).

## Skema warna gradient (Customizer > M-News > Warna)
- **Preset gradient** (swatch): pilih dari ~12 kombinasi siap pakai (mis. Ungu–Magenta seperti referensi, Biru Laut, Merah–Jingga, Hijau Tosca, Hitam Elegan, dsb.).
- **Kustom**: 2–4 titik warna (Warna 3 & 4 opsional), arah gradient (kiri→kanan, atas→bawah, diagonal 135°/45°), aktif bila preset = "Kustom".
- **Terapkan gradient ke** (toggle): bar menu, footer, judul blok, tombol, latar Blok Kategori. Elemen yang tidak diaktifkan memakai warna solid `--mnw-c1`.
- Output CSS variables: `--mnw-c1..--mnw-c4`, `--mnw-grad`, `--mnw-accent` (= c1), `--mnw-on-grad` (warna teks di atas gradient, dihitung server dari luminansi terburuk agar kontras ≥ 4,5:1).
- Setting warna memakai transport `refresh` (bukan `postMessage`) agar logika gradient + kontras hanya ada satu di PHP dan kontrol kustom bisa tampil/sembunyi lewat `active_callback`.
- Gradient murni CSS: **tidak ada gambar** dan tidak ada request tambahan.

## Struktur file
```
m-news/m-news/
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
    widget-areas.php render.php   (area registrasi + mnews_area(); renderer kartu mnews_render_posts(), mnews_run_query())
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

## Widget M-News (grup "M-News")
| Widget | Opsi utama |
|---|---|
| Slider Headline | Sumber (tag/kategori/sticky), jumlah, model: slider+thumbnail, slider penuh, carousel 3 kolom, **Hero** (1 besar + strip bubble bulat di bawahnya, sama di semua layar, bubble = penanda slide yang aktif) |
| Daftar Berita | Sumber (terbaru/populer/acak/kategori/tag), layout: list, kecil, bernomor (Trending), grid 2/3 kolom, 1 besar + list; opsi feed dengan pagination ("Berita Terkini"); opsi "hanya berita yang punya video" |
| Blok Kategori | Kategori, layout (grid/carousel/overlay), latar (gradient tema / solid / kustom), tautan "Lihat semua" |
| Banner Iklan | Preset ukuran (menampilkan ukuran + rasio), gambar+URL+alt atau kode HTML/AdSense, label "Advertisement", tampil desktop/mobile |
| Sosial Follow | Jaringan aktif dari Customizer |
| Tag / Topik | Jumlah, gaya |
| HTML / Embed | HTML bebas untuk widget pihak ketiga (skor, cuaca, dsb.) |
| Polling | Jumlah polling ditampilkan (carousel scroll-snap); pertanyaan+pilihan diisi lewat CPT Polling (Polling > Tambah Polling), bukan lewat widget |

Semua widget daftar berita memanggil satu renderer `mnews_render_posts( $args )` dan `template-parts/card-*.php`. Layout baru = satu template part + satu entri di daftar layout. Output tiap widget di-cache (lihat `inc/cache.php`).

## Customizer — panel "M-News"
- **Warna**: skema gradient (di atas), warna aksen/teks.
- **Social Media Share**: aktif/nonaktif per jaringan (Facebook, X, WhatsApp, Telegram, LINE, LinkedIn, Pinterest, Email, Salin Link), urutan, posisi (atas artikel / bawah / melayang di mobile), gaya ikon, template teks share (`{title}`, `{url}`). Share memakai link `<a href>` biasa (tanpa SDK/JS pihak ketiga); JS hanya untuk "Salin Link".
- **Social Media Follow**: URL akun; dipakai header, footer, widget Sosial Follow.
- **Banner Iklan**: slot header, samping 160×600, dalam artikel (setelah paragraf ke-N), setelah konten. Setiap slot: preset ukuran dengan keterangan ukuran & rasio (`970×250 · 3.88:1`, `300×250 · 6:5`, `160×600 · 4:15`, `728×90`, `320×100`), gambar+URL+alt atau kode HTML, tampil desktop/mobile.
- **Header** (ticker breaking news, sticky), **Footer** (alamat, copyright, menu jaringan media), **Artikel** (penulis, waktu, zona waktu, jumlah pembaca, related, caption foto), **Home** (jumlah post, pagination), **Performa** (toggle pembersihan bloat WP).

## Arsitektur widget (sudah dibangun, Fase 3)
- Semua widget turunan `MNews_Widget` (`inc/widgets/abstract-widget.php`): cukup definisikan `fields()` (skema: text/slug/number/select/checkbox/category) dan `render()`. Form admin, sanitasi `update()`, cache fragmen, dan selective refresh sudah otomatis. Widget baru = 1 file + 1 baris `register_widget()` di `inc/widgets/init.php`.
- Output widget di-cache (transient, 5 menit, di-flush saat post/term berubah, dilewati di preview Customizer). Ikon yang dipakai fragmen ikut disimpan (`mnews_icon_log`) supaya sprite tetap lengkap saat cache hit. Aset widget (mis. `slider.js`) di-enqueue di `assets()` yang selalu jalan, bukan di `render()`.
- `front-page.php`: area kosong diberi default (`home-main` = Slider + Berita Terkini berpaginasi, `home-sidebar` = Trending) lewat `the_widget()` dengan `mnews_widget_args( true )`. Query utama front page sengaja diperkecil (`mnews_lean_home_query`) karena widget menjalankan query sendiri.
- Slider = CSS scroll-snap; `slider.js` (~1,1 KB gzip) mengurus prev/next, dot/thumbnail, autoplay (berhenti saat hover/tab tersembunyi/di luar layar, mati bila `prefers-reduced-motion`).
- Kartu tidak memakai `post_class()` (per-post query term + kelas panjang). Query widget wajib lewat `mnews_run_query()` (memprime thumbnail dalam 1 batch) dan `update_post_meta_cache => true`.
- Terukur di lokal (7 widget di home): 21 query dengan cache hangat, ±64 dengan cache dingin; CSS 4,1 KB gzip, JS 0,4 KB + 1,1 KB (slider).
- Banner Iklan (Fase 4): banner = widget, bukan slot di Customizer. Customizer > Banner Iklan hanya menyimpan opsi global (label banner, paragraf sisip dalam artikel) dan daftar ukuran + rasio; ukuran + rasio juga tampil di pilihan widget (`inc/ads.php`). Gambar memakai media picker (`admin.js`), kode iklan/HTML disimpan di `<template>` dan baru disuntik oleh `slot.js` saat mendekati layar dan sesuai perangkat (desktop/mobile), sehingga skrip pihak ketiga tidak menghambat render. Wadah selalu punya `aspect-ratio`/`min-height` sesuai ukuran (tanpa CLS). Kode mentah hanya tersimpan utuh untuk pengguna dengan `unfiltered_html`, selain itu di-`wp_kses_post`. `document.write` di kode iklan tidak akan jalan (disuntik setelah load).
- Tipe field baru di `MNews_Widget`: `url`, `color`, `html`, `image`. Blok Kategori: latar `theme` (mengikuti gradient + toggle "Latar Blok Kategori" di Customizer) atau `solid` (kontras teks dihitung otomatis).
- Single (Fase 5): `single.php` + `comments.php` (tanpa avatar/Gravatar demi kecepatan). Meta box "Info Berita" (sub-judul, penulis, editor, sumber) di `inc/meta.php`. Share: link biasa per jaringan, diatur di Customizer (jaringan, urutan, posisi atas/bawah/keduanya, bar melayang mobile, gaya, label, template teks `{title}`/`{url}`); hanya "Salin Link" memakai JS (di `main.js`). `inc/content.php` menyisipkan "Baca Juga" (1 berita terkait) dan area `single-in-article` setelah paragraf ke-N lewat filter `the_content` (tidak pernah setelah paragraf terakhir). Berita terkait: ID di-cache 30 menit, HTML grid di-cache lewat `mnews_cache_fragment()`. SEO (`inc/seo.php`): OG/Twitter/description + JSON-LD `NewsArticle` & `BreadcrumbList`, otomatis nonaktif bila Yoast/Rank Math/AIOSEO/SEOPress/TSF aktif. Terukur: single 35 query dengan cache hangat (77 dingin), 1 `<h1>`, gambar utama `eager` + `fetchpriority=high`.
- Template lain (Fase 6): `template-parts/archive.php` adalah satu-satunya isi untuk kategori, tag, author, tanggal, hasil pencarian, dan halaman posting; `index.php`, `archive.php`, `search.php` hanya pembungkus tipis (category/tag/author sengaja tidak punya file sendiri). Header arsip memakai label ("Kategori", "Topik", "Penulis", "Pencarian") + `get_the_archive_title_prefix` dikosongkan. `mnews_sidebar( $area )` dipakai semua template berkolom (home, single, arsip, 404) dan memberi default Trending bila area kosong. `page.php` = satu kolom baca (tanpa sidebar). `404.php` + `content-none.php` memberi kotak cari dan berita terbaru. Halaman depan memakai `<h1>` di logo/judul situs (`mnews_logo()`), footer tidak.
- Widget BMKG (setelah Fase 7): `Prakiraan Cuaca` (`weather.php`) dan `Info Gempa` (`earthquake.php`), keduanya opsional lewat widget area (tambah/hapus kapan saja). Sumber: `api.bmkg.go.id/publik/prakiraan-cuaca?adm4=` (butuh kode wilayah tingkat IV/desa, contoh `31.71.03.1001`) dan `data.bmkg.go.id/DataMKG/TEWS/{autogempa|gempaterkini|gempadirasakan}.json`. Pemilih wilayah di form widget = 4 dropdown berjenjang (tipe field `wilayah`) yang membaca daftar dari `wilayah.id` lewat proxy `admin-ajax` (`mnews_wilayah`, nonce + `edit_theme_options`, cache 30 hari); kode bisa juga ditempel manual. Aturan kecepatan: pengunjung tidak menunggu BMKG bila salinan lama ada — transient (gempa 10 menit, cuaca 1 jam) + salinan basi di option + kegagalan diingat 1 menit + WP-Cron tiap 10 menit menyegarkan data yang dipakai (`mnews_bmkg_cron`). Widget ini `$ttl = 0` (tanpa cache HTML) supaya keadaan error tidak ikut tersimpan. Peta guncangan (±230 KB) default mati. Cuaca memakai ikon SVG sendiri (`w-*`, varian malam) — tidak ada gambar/ikon dari server BMKG. Atribusi "Sumber: BMKG" wajib ditampilkan. Uji outage: salinan basi tersaji dan hanya 1 percobaan HTTP.
- Batas judul: semua kartu dan caption slider dipotong dengan CSS `line-clamp` memakai variabel `--mnw-lines` yang diisi renderer (`mnews_render_posts`/`mnews_render_slider`). Bawaan: daftar 3 baris, ringkas/trending/grid/1-besar 2 baris, slider 2 baris. Widget Daftar Berita & Blok Kategori punya opsi "Batas baris judul" (0 = otomatis), Slider punya opsi 1-6. Judul penuh tetap ada di HTML (aman untuk pembaca layar dan SEO).
- Banner samping 160×600 (`side-banner-left/right`) bersifat sticky: `mnews_side_banners()` membungkus tiap sisi dalam kolom setinggi konten (`.mnw-side`, absolute) berisi `.mnw-side__in` (`position: sticky`). Posisi atasnya = tinggi header lengket + 16 px; tinggi header diukur `main.js` ke variabel CSS `--mnw-header-h` (tanpa layout shift). Bisa dimatikan di Customizer > Banner Iklan (`mnews_side_sticky`, class body `mnw-side-sticky`). Hanya tampil ≥ 1560 px; di layar pendek (< ±760 px tinggi) bagian bawah banner bisa terpotong karena header lengket.
- Jebakan Customizer: `WP_Widget::update()` **tidak boleh menulis option lain** (mis. `update_option`), kalau tidak Customizer menolak simpan widget dengan error `widget_setting_too_many_options` ("An error has occurred. Please reload the page and try again."). Karena itu `MNews_Widget::update()` menunda flush cache ke hook `shutdown`. Semua 9 widget diuji lewat jalur `call_widget_update` Customizer.
- Sidebar artikel/arsip/404: `mnews_sidebar( $area )` memakai widget area miliknya; bila kosong **jatuh ke widget `home-sidebar`** (baru bila itu kosong juga → default Trending). Jadi situs baru tampil sama di home dan artikel tanpa mengisi dua kali.
- Banner sidebar sticky: widget Banner Iklan punya opsi "Tempel saat halaman digulir". Kelas `mnw-sticky-ad` hanya menempel bila widget itu **paling bawah** di sidebar (`:last-child`), supaya tidak pernah menutupi widget lain; kolom sidebar direntangkan setinggi konten (`.mnw-layout__side` flex + `.mnw-area` `flex:1`) agar sticky punya ruang gerak. Posisi = tinggi header lengket + 16 px. Diuji: iklan 300×600 tetap di 157 px dari atas saat digulir di home dan artikel. Banner 160×600 kiri/kanan sticky terpisah (lihat di atas).
- **Impor Demo (Tampilan > Impor Demo)**: satu klik membuat situs tampil seperti demo — 30 artikel fiktif ±500 kata (6 kategori × 5, `demo/articles/*.txt`, format `### Judul | YYYY-MM-DD | tag1, tag2`, paragraf dipisah baris kosong, `## ` = h2) dengan gambar unggulan buatan GD, 5 halaman, menu Utama/Footer/Jaringan Media, 21 widget (data di `inc/demo/data.php`), Customizer, logo (`demo/logo.png`, salinan dari `referensi/logo/logo.png`), judul/slogan, permalink `/%postname%/` + zona Asia/Jakarta, dan membersihkan "Hello world!"/"Sample Page". Tiap bagian bisa dicentang. Berjalan sebagai langkah AJAX kecil (media → struktur → 1 artikel per request → widget → pengaturan → selesai) dengan progress bar, jadi aman di shared hosting. Aman diulang (artikel dilewati lewat meta `_mnews_demo_key`); semua yang dibuat diberi meta `_mnews_demo` sehingga tombol "Hapus konten demo" bisa menghapusnya (post, halaman, media, widget, logo). Notice ajakan impor muncul di Dashboard/Tema sampai diimpor atau ditolak. CLI: `wp eval "mnews_demo_run_all();"`. Diuji end-to-end lewat layar admin asli pada situs kosong (30 artikel, 5 halaman, 20+ widget, ±40 detik), termasuk hapus lalu impor ulang. Ubah `data.php`/`articles/` untuk mengganti demo; `tools/count-words.py` dan `tools/extend.py` membantu mengatur panjang artikel.
- **Widget Statistik Pengunjung** (`visitors.php` + `inc/stats.php`): online (5 menit), hari ini, kemarin, bulan ini, total, tayangan; angka awal opsional. Penghitungan lewat beacon `POST/GET /wp-json/mnews/v1/hit` (bukan saat render, jadi aman dengan page cache): `visit.js` (0,4 KB, menghormati Do Not Track, id acak di localStorage) atau `<amp-pixel>` di AMP. Tanpa menyimpan IP: id → hash satu arah dalam transient (online 5 menit, unik-harian 24 jam); counter = `UPDATE ... option_value + 1` atomik pada option non-autoload (`mnews_c_*`), dipangkas setiap hari (>60 hari). Bot (UA), id tak valid, editor yang login, dan halaman tanpa widget aktif diabaikan (`is_active_widget`). Nomor dibaca dalam 1 query + cache 1 menit.
- **AMP** (`inc/amp.php`): `add_theme_support( 'amp' )` mode Transitional (`paired`), bisa dipilih Standard; menu mobile memakai `nav_menu_toggle` bawaan plugin (`#mnw-toggle` / `#mnw-nav`, kelas `is-open`). Di AMP tema berhenti mengeluarkan yang tak boleh ada: semua skrip (main/slider/slot/visit + flag `js` + speculation rules), atribut `loading` pada gambar, `aria-roledescription`, kode iklan/HTML embed, tombol Salin Link dan bar bagikan melayang; kontrol slider disembunyikan (geser tetap jalan via CSS scroll-snap); logo dibatasi lewat `amp-img`. Body class `mnw-amp` mengaktifkan CSS khusus AMP. Hasil `wp amp validation run` pada Standard **dan** Transitional: **0 issue** (16 + 12 URL), menu toggle dan tanpa overflow diuji di browser (320–1440 px). Aturan: jangan menambah `!important`, `position: fixed`, `<template>`, atau JS wajib pada tampilan yang juga dipakai AMP.
- **Lisensi** (`inc/license/`, warisan arsitektur dari M-Nata — lihat `../m-nata/CLAUDE.md` untuk narasi lengkap termasuk sejarah pivotnya): tema memverifikasi kunci ke `https://api.m-onetech.id/v1/licenses/{activate|verify}` (backend Laravel yang sama dipakai semua tema M-Onetech — satu kunci tanda tangan untuk seluruh layanan, dibedakan lewat field `product`). Tiap jawaban ditandatangani **Ed25519**; tema memverifikasi dengan kunci publik tertanam (`MNEWS_LICENSE_PUBKEY`) + `nonce` yang di-echo + `domain`/`product` + `issued_at` (±1 hari). Status disimpan di option `mnews_license` dengan segel HMAC (`wp_salt`). Re-check **harian** lewat WP-Cron (`mnews_license_cron`) + tombol "Periksa ulang sekarang", toleransi gangguan `MNEWS_LICENSE_GRACE_DAYS`=7 hari. **Penguncian**: tanpa lisensi `active`, `template_redirect` menampilkan `template-parts/license-locked.php` (HTTP 503) ke pengunjung; admin tetap bisa masuk. Host dev (`localhost`, `*.test`, `*.local`, ...) bebas lisensi. Tidak ada tombol "Nonaktifkan" (kebijakan M-Nata yang diwarisi: lisensi terkunci permanen ke domain pertama, pindah domain lewat dukungan) — **evaluasi ulang apakah kebijakan ini juga diinginkan untuk M-News**, bukan otomatis ikut begitu saja.
  **Status kunci untuk M-News (2026-09-30): `MNEWS_LICENSE_PUBKEY` masih kunci DEV** (pasangan baru, khusus proyek ini — lihat `wp-be/mock/dev-keys.json`), **belum pernah ditempel kunci produksi**. Sebelum M-News dirilis: minta backend mendaftarkan **produk baru `m-news`** (bagian `## Yang perlu dari backend` di `wp-be/permintaan-ke-backend.md`), lalu tempel kunci publik (kemungkinan besar sama persis dengan kunci produksi M-Nata, karena satu backend berbagi satu kunci tanda tangan — tapi konfirmasi ke backend, jangan diasumsikan sendiri).
- **Pembaruan tema** (`inc/license/updates.php`): hanya untuk lisensi `active`, mekanismenya identik dengan M-Nata (cek `POST {api}/products/m-news/update`, verifikasi SHA-256 sebelum pasang). Belum pernah diuji ujung-ke-ujung untuk M-News secara spesifik — arsitekturnya sudah teruji di M-Nata, tapi jalankan uji pemasangan dari zip bersih sebelum rilis pertama M-News.
- **Aset produksi**: `tools/build-assets.js` (csso + terser) membuat `*.min.css/js`; `mnews_asset()` (`inc/helpers.php`) otomatis memakai `.min` kecuali `SCRIPT_DEBUG`. Selalu jalankan ulang setelah mengubah CSS/JS.
- **Rilis** (`python tools/release.py`): gerbang + pengemas, sama seperti M-Nata. Menolak build bila versi `style.css` != `Stable tag` readme, ada file PHP gagal `php -l`, file minify tidak ada/basi, `.pot` tidak ada, sisa debug, API lisensi bukan https, atau **kunci publik lisensi masih kunci DEV** (`--allow-dev-key` untuk build uji — dipakai untuk semua build sampai kunci produksi didapat dari backend). Hasil: `dist/m-news-<versi>.zip` + `.sha256`.
- **Belum diuji sama sekali untuk M-News**: instalasi dari zip bersih, aktivasi/penguncian lisensi terhadap server sungguhan, Impor Demo end-to-end, Lighthouse, AMP, PHPCS/`php -l` pasca-kustomisasi. Semua fitur ini *seharusnya* bekerja karena kodenya identik dengan M-Nata yang sudah teruji (lihat `../m-nata/CLAUDE.md` bagian "QA" dan "Uji produksi" untuk hasil aslinya) — tapi itu hasil pengujian M-Nata, bukan M-News. Jalankan ulang minimal sekali sebelum rilis pertama, terutama setelah design token/demo/branding diganti.
- Belum ada: dedupe berita yang sudah tampil di slider (warisan catatan dari M-Nata, belum diputuskan di sana ataupun di sini).

## QA — status
Arsitektur ini identik dengan M-Nata yang sudah lolos PHPCS 0 error/warning, `php -l` bersih, Lighthouse mobile 100/100/100, dan validasi AMP 0 isu (lihat `../m-nata/CLAUDE.md`). **M-News sendiri belum dijalankan lewat siklus QA itu** — jalankan urutan berikut setidaknya sekali setelah kustomisasi awal (warna, font, demo) selesai, sebelum rilis pertama:
- PHPCS: `composer require --dev squizlabs/php_codesniffer wp-coding-standards/wpcs phpcompatibility/phpcompatibility-wp dealerdirect/phpcodesniffer-composer-installer` di folder sementara, lalu `phpcs --standard=phpcs.xml.dist` dari `m-news/` (perbaikan otomatis: `phpcbf`).
- `.pot`: `wp i18n make-pot <path>/m-news <path>/m-news/languages/m-news.pot --slug=m-news --exclude=assets`.
- Lighthouse: `npm i lighthouse`, `CHROME_PATH=<msedge.exe>`, `lighthouse http://mnews.test/ --form-factor=mobile`.
- Zip rilis: `python tools/release.py` → `dist/m-news-<versi>.zip`.
- Belum: Theme Check/Plugin Check resmi, uji data ekstrem (judul panjang, tanpa gambar, konten kosong) yang sudah dilakukan di M-Nata tapi perlu diulang di sini, Author di `style.css` masih placeholder "M-Onetech" (sesuaikan bila beda).

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

## Fitur tambahan (2026-09-30, dari referensi `referensi/*.png`)
- **Slider Headline model "Hero"**: satu widget, satu mekanisme — sama seperti model "thumbs" (slide besar + strip navigasi di bawah yang saling sinkron lewat `data-slide`/`slider.js`), hanya beda gaya visual (bundar/`mnw-hero__bubble-img`, bukan persegi). Tidak ada tampilan berbeda antara desktop dan mobile (percobaan pertama sempat begitu — grid 2 kartu di desktop, bubble di mobile — lalu disederhanakan jadi satu bentuk saja sesuai arahan). Bubble **tidak** tertaut langsung ke artikel; mengetuknya memindahkan slide besar ke situ (persis seperti thumbnail pada model "thumbs"), buka artikelnya dengan mengetuk slide besar yang sudah berpindah.
- **Nav bawah mobile** (`inc/nav-bottom.php`, hanya < 992 px, `mnews_nav_bottom()` di hook `wp_footer`): pil melayang berisi Beranda, Trending, Cari, Bagikan, Polling. "Cari" membuka panel `#mnw-nav` yang sudah ada (form pencarian di dalamnya, sama seperti hamburger desktop). "Bagikan" memakai `navigator.share()`, fallback salin tautan. "Trending" menuju halaman ber-`Template Name: Trending` (`template-trending.php`) pertama yang ditemukan (`mnews_trending_url()`, cache 1 hari, ikut invalidasi seperti cache lain); tanpa halaman seperti itu, jatuh ke beranda — Impor Demo sudah membuat satu.
- **Polling** (`inc/polling.php` + `inc/widgets/polling.php`): CPT `mnews_poll` (judul = pertanyaan, meta box "Opsi Polling" = satu pilihan per baris). **Sengaja tanpa status per-pengunjung di HTML**: kartu yang dirender selalu netral (semua opsi bisa diklik, persentase = snapshot agregat yang aman di-cache), `assets/js/poll.js` yang membaca `localStorage` lalu menukar ke tampilan hasil di sisi klien — supaya tetap benar di belakang plugin page cache (pola yang sama dengan `inc/stats.php`). Suara dihitung atomik per opsi (`_mnews_poll_votes_{key}` di postmeta, pola sama seperti `mnews_stats_incr()`); REST `POST /wp-json/mnews/v1/poll-vote` idempoten lewat cookie `mnews_pv_{id}` (percobaan kedua dengan opsi lain tidak mengubah suara pertama). Arsip: `archive-mnews_poll.php` (`/polling/`), single: `single-mnews_poll.php`.
- **Post video**: field "URL Video" opsional di kotak Info Berita (`_mnews_video`, `inc/meta.php`). Bila diisi: label "Video" + `wp_oembed_get()` menggantikan gambar utama di `single.php`, dan badge putar (`mnews_video_badge()`, `inc/render.php`) muncul di atas thumbnail pada semua `template-parts/card-*.php` yang punya gambar. Widget Daftar Berita punya centang "Hanya berita yang punya video" (`meta_query`) untuk meniru blok "Video" di referensi.
- **Footer berkolom**: Customizer > M-News > Footer > "Jumlah kolom footer" (2/3/4, `mnews_footer_cols`). Widget area footer-4 ditambahkan; `mnews_footer_widgets()` membatasi jumlah kolom yang dipertimbangkan sesuai pilihan itu (kolom kosong tetap tidak tampil seperti biasa).
- Ikon baru di `inc/icons.php`: `home`, `trending`, `share`, `poll`, `play` (semua gambar asli, bukan brand).
- Belum dikerjakan (di luar cakupan permintaan referensi ini): voting Polling tidak berfungsi di AMP (JS dimatikan di sana, opsi tetap tampil sebagai teks statis); Impor Demo belum bisa mengisi footer 4 kolom secara otomatis (footer memang dibiarkan kosong di demo, isi manual di Tampilan > Widget bila mau dipakai).

## Yang tidak boleh
- Menyalin balik dari `../m-nata/` tanpa menulis ulang bila menambah fitur baru yang meniru layout Nomina persis; tetap berlaku aturan asal tema M-Nata: tidak memakai nama, logo, atau teks "Nomina"/"Baturetno" di mana pun.
- Blok home hardcode, `echo get_theme_mod()` tanpa escape, jQuery, library slider, font/skrip dari CDN.
- Mengubah `../bakar/` atau `../m-nata/` (tema lain, bukan bagian proyek ini).

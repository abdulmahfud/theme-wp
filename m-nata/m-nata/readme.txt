=== M-Nata ===
Contributors: mnata
Requires at least: 6.3
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 1.2.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Tags: news, blog, two-columns, right-sidebar, custom-logo, custom-menu, custom-colors, featured-images, threaded-comments, translation-ready

Tema portal berita WordPress yang mengutamakan kecepatan: gradient warna bisa dipilih di Customizer, dan semua bagian home & sidebar berupa widget.

== Description ==

M-Nata dibuat untuk portal berita yang butuh cepat di mobile.

* Tanpa jQuery, tanpa library slider, tanpa font atau skrip dari CDN.
* Satu file CSS (sekitar 6 KB gzip) dan JavaScript kecil yang dimuat hanya bila dipakai.
* Skema warna gradient: pilih dari 12 preset atau susun sendiri 2-4 warna (Customizer > M-Nata > Warna & Gradient).
* Home, sidebar, header, footer, dan artikel dibangun dari widget area. Widget: Slider Headline, Daftar Berita (daftar, ringkas, trending bernomor, grid, 1 besar + daftar, feed bernomor halaman), Blok Kategori, Banner Iklan, Sosial Follow, Tag / Topik, HTML / Embed, Prakiraan Cuaca (BMKG, wilayah bisa dipilih), Info Gempa (BMKG), Statistik Pengunjung.
* Banner iklan dengan ukuran standar dan rasio ditampilkan di pilihan; kode iklan dimuat lazy; ruang selalu dicadangkan sehingga tidak ada layout shift.
* Tombol bagikan (Facebook, X, WhatsApp, Telegram, LINE, LinkedIn, Pinterest, Email, Salin Link) yang bisa diatur di Customizer.
* Halaman artikel dengan sub-judul, kredit penulis/editor/sumber, "Baca Juga", berita terkait, komentar, dan data terstruktur NewsArticle + BreadcrumbList (otomatis mati bila plugin SEO aktif).
* Impor demo satu klik (Tampilan > Impor Demo): 30 artikel contoh, menu, widget, pengaturan, dan logo, lengkap dengan tombol untuk menghapusnya lagi.
* Kompatibel dengan plugin AMP resmi (Transitional/Standard) tanpa isu validasi.
* Antarmuka admin berbahasa Indonesia, siap diterjemahkan (text domain: m-nata).

== Installation ==

1. Unggah folder `m-nata` ke `wp-content/themes/` atau pasang zip lewat Tampilan > Tema > Tambah Baru.
2. Aktifkan tema.
3. Atur menu di Tampilan > Menu (lokasi: Menu Utama, Menu Footer, Media Network).
4. Atur warna, sosial, banner, dan share di Tampilan > Sesuaikan > M-Nata.
5. Atau, untuk langsung tampil seperti demo: buka Tampilan > Impor Demo lalu klik "Impor Demo Sekarang".
6. Isi widget di Tampilan > Widget. Bila area home dibiarkan kosong, tema menampilkan slider, "Berita Terkini", dan "Trending" secara bawaan.

== Frequently Asked Questions ==

= Apakah tema ini butuh lisensi? =

Ya. Aktifkan kunci lisensi di Tampilan > Lisensi. Tanpa lisensi aktif, pengunjung melihat halaman "lisensi diperlukan" (situs lokal seperti *.test dan localhost dibebaskan). Tema memeriksa lisensi ke server M-Onetech sekali sehari dan tetap berjalan selama 7 hari bila server tidak terjangkau. Satu kunci lisensi terkunci permanen ke domain pertama yang mengaktifkannya; untuk memindahkannya ke domain lain, hubungi dukungan M-Onetech.

= Kenapa widget block editor dinonaktifkan? =

Widget M-Nata memakai Widget API klasik (form, sanitasi, dan cache seragam), jadi layar Widget memakai antarmuka klasik.

= Apakah tema ini memakai jQuery atau CDN? =

Tidak. Seluruh aset ada di dalam tema.

= Kode iklan saya tidak jalan di widget? =

Kode mentah (mis. skrip AdSense) hanya tersimpan utuh untuk pengguna dengan izin `unfiltered_html`. Kode iklan disuntik setelah halaman selesai dimuat; iklan yang memakai `document.write` tidak didukung.

== Copyright ==

M-Nata WordPress Theme, (C) 2026 M-Nata.
M-Nata is distributed under the terms of the GNU General Public License v2 or later.

Sumber daya:
* CSS dan JavaScript tema ditulis sendiri. Tema tidak menyertakan font, gambar, atau library pihak ketiga.
* Ikon merek media sosial (Facebook, X, Instagram, YouTube, TikTok, Pinterest, WhatsApp, Telegram, LINE): Simple Icons, lisensi CC0 1.0 (https://simpleicons.org). Nama dan logo merek adalah milik masing-masing pemiliknya.
* Ikon LinkedIn, Email, dan Salin Link: Bootstrap Icons, lisensi MIT, Copyright (c) 2019-2024 The Bootstrap Authors (https://icons.getbootstrap.com).
* Ikon antarmuka lain (cari, menu, tutup, panah, cuaca) digambar sendiri untuk tema ini.
* demo/logo.png: logo contoh M-Nata milik pemilik tema. Artikel demo adalah teks fiktif buatan sendiri.
* Data cuaca dan gempa berasal dari BMKG (https://www.bmkg.go.id); sumber dicantumkan pada widget. Gambar di screenshot.png adalah placeholder yang dibuat dengan GD (tanpa hak cipta pihak lain).

== Changelog ==

= 1.2.1 =
* Kunci publik lisensi diganti dari kunci pengembangan ke kunci produksi resmi (backend live). Lisensi kini terkunci permanen ke domain pertama yang mengaktifkannya; tombol "Nonaktifkan di domain ini" dihapus (pindah domain lewat dukungan M-Onetech).

= 1.2.0 =
* Lisensi dikembalikan ke penegakan penuh (verifikasi bertanda tangan, penguncian tampilan tanpa lisensi aktif, pembaruan tema otomatis) setelah sempat disederhanakan di 1.1.0. Widget Trending kini urutan pertama secara default di sidebar demo.

= 1.1.0 =
* (Dilewati di rilis publik) Layar Lisensi sempat disederhanakan menjadi pencatatan aktivasi saja tanpa penegakan; dikembalikan di 1.2.0.

= 1.0.0 =
* Rilis produksi: sistem lisensi (verifikasi bertanda tangan, toleransi gangguan), pembaruan tema otomatis dari server lisensi, aset CSS/JS diminify.
* Sebelumnya (0.1.0):
* Impor demo satu klik, widget Statistik Pengunjung, dukungan AMP.
* Rilis awal: header + menu gradient + ticker, widget area, 7 widget, banner iklan, share, artikel, arsip, pencarian, 404, halaman statis, SEO markup, Customizer.

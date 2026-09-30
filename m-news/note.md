# Catatan M-News — akses situs uji & panduan instalasi

Bagian 1–2 hanya untuk komputer pengembangan ini (Laragon). Jangan dipakai di situs publik.

---

## 1. Akses situs uji lokal

### WordPress
- Situs: http://mnews.test
- Login admin: http://mnews.test/wp-login.php
- Username: `admin`
- Password: `admin123`
- Email admin: `admin@example.test`
- Zona waktu: Asia/Jakarta, permalink `/%postname%/`

### Database (MySQL Laragon)
- Host: `127.0.0.1` (port 3306)
- Nama database: `mnews`
- User: `root`
- Password: (kosong)

### Lokasi file
- Instalasi WordPress: `C:\laragon\www\mnews`
- Tema (junction ke folder proyek): `C:\laragon\www\mnews\wp-content\themes\m-news` -> `D:\project-theme-wp\m-news\m-news`
- Log debug WordPress: `C:\laragon\www\mnews\wp-content\debug.log` (WP_DEBUG aktif, tampilan error dimatikan)
- Zip rilis tema: `D:\project-theme-wp\m-news\dist\m-news-<versi>.zip` (dibuat dengan `python tools/release.py`, belum pernah dibangun untuk M-News)

### Plugin uji
- AMP (resmi) aktif, mode Transitional: buka `http://mnews.test/?amp=1` untuk melihat versi AMP. Nonaktifkan bila tidak dipakai.

### Impor demo
- Tampilan > Impor Demo: http://mnews.test/wp-admin/themes.php?page=mnews-demo

### Lisensi (uji lokal)
- Situs `.test` dibebaskan dari lisensi (status "dev"). Untuk menguji penguncian: tambahkan ke `wp-config.php` `define( 'MNEWS_LICENSE_FORCE', true ); define( 'MNEWS_LICENSE_API', 'http://127.0.0.1:8090/v1' );` lalu jalankan `php -S 127.0.0.1:8090 wp-be/mock/server.php` dari folder proyek.
- Kunci uji: `MNEWS-TEST-ACTIVE-0001` (1 domain), `MNEWS-TEST-LIFETIME-0002` (3 domain), `-EXPIRED-0003`, `-REVOKED-0004`, `-LIMIT-0005`. Kontrol uji server tiruan: `POST /_test/status`, `/_test/mode` (ok/down/badsig), `/_test/reset`.
- Layar lisensi: Tampilan > Lisensi (http://mnews.test/wp-admin/themes.php?page=mnews-license). Hapus dua konstanta di atas setelah selesai menguji.
- Permintaan ke backend (dokumen mandiri): `wp-be/permintaan-ke-backend.md`. Server tiruan untuk uji: `wp-be/mock/`.

### Kalau perlu
- Jalankan WP-CLI dari `C:\laragon\www\mnews` dengan `php wp-cli.phar` (file phar ada di folder sementara sesi, unduh ulang dari https://github.com/wp-cli/builds jika hilang).
- Junction putus bila folder proyek diganti nama; buat ulang dengan `mklink /J C:\laragon\www\mnews\wp-content\themes\m-news D:\project-theme-wp\m-news\m-news`.
- Ganti password admin sebelum situs ini dipakai di luar komputer ini.

---

## 2. Panduan: membangun lingkungan uji dari awal (Laragon, Windows)

Untuk komputer baru atau bila situs uji ingin dibuat ulang.

**Kebutuhan:** Laragon (Apache/Nginx + MySQL + PHP 7.4 atau lebih baru), Git Bash atau PowerShell, koneksi internet (unduh WordPress).

1. **Buat folder situs.** Di `C:\laragon\www\` buat folder `mnews`. Laragon otomatis membuat alamat `http://mnews.test` (Menu Laragon > Reload, atau restart Apache bila belum muncul). Pastikan Apache dan MySQL berjalan.
2. **Buat database.** `mysql -uroot -e "create database mnews character set utf8mb4;"` (root, tanpa password bawaan Laragon).
3. **Unduh WordPress.** Ambil https://wordpress.org/latest.zip, ekstrak isinya (folder `wordpress`) ke `C:\laragon\www\mnews` sehingga `wp-config-sample.php` berada langsung di dalamnya.
4. **Pasang WP-CLI** (opsional tapi sangat membantu): unduh `wp-cli.phar` dari https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar.
5. **Konfigurasi dan pasang WordPress** dari folder `C:\laragon\www\mnews`:
   ```
   php wp-cli.phar config create --dbname=mnews --dbuser=root --dbpass= --dbhost=127.0.0.1 --skip-check
   php wp-cli.phar core install --url=http://mnews.test --title="Portal Berita Demo" --admin_user=admin --admin_password=admin123 --admin_email=admin@example.test --skip-email
   ```
   Tambahkan ke `wp-config.php` untuk pengembangan: `define( 'WP_DEBUG', true ); define( 'WP_DEBUG_LOG', true ); define( 'WP_DEBUG_DISPLAY', false );`
6. **Hubungkan tema ke folder proyek** (agar setiap edit kode langsung terlihat), di Command Prompt:
   ```
   mklink /J C:\laragon\www\mnews\wp-content\themes\m-news D:\project-theme-wp\m-news\m-news
   ```
7. **Aktifkan tema dan impor demo:** `php wp-cli.phar theme activate m-news`, lalu buka **Tampilan > Impor Demo** (atau `php wp-cli.phar eval "mnews_demo_run_all();" --user=admin`). Lihat bagian 3 langkah 4.
8. **Permalink:** bila alamat cantik seperti `/kategori/nasional/` memberi 404 di Apache Laragon, buka **Pengaturan > Permalink > Simpan** sekali (atau buat `.htaccess` standar WordPress) agar aturan rewrite ditulis.
9. **Plugin pendukung uji (opsional):** `php wp-cli.phar plugin install amp --activate` untuk menguji AMP; `php wp-cli.phar amp validation run --limit=3` harus menghasilkan 0 masalah.

**Jebakan Windows / Git Bash:** argumen berawalan `/` diubah menjadi path Windows (contoh: `wp rewrite structure '/%postname%/'` merusak permalink). Jalankan perintah semacam itu dengan `MSYS2_ARG_CONV_EXCL='*'` atau lewat PowerShell. `--color` adalah flag global WP-CLI, bukan argumen widget.

**Membuat zip rilis:** dari `D:\project-theme-wp\m-news` jalankan `python tools/release.py` (tambahkan `--allow-dev-key` selama kunci lisensi masih DEV) -> `dist\m-news-<versi>.zip` (isi: folder `m-news/` siap diunggah ke WordPress).

---

## 3. Panduan: instalasi tema pada situs baru (produksi / hosting)

### Kebutuhan
- WordPress 6.3 atau lebih baru, PHP 7.4 atau lebih baru (disarankan 8.1+), HTTPS aktif.
- Ekstensi PHP **GD** (untuk gambar demo; tanpa GD impor tetap jalan tetapi tanpa gambar unggulan).
- Server dapat menghubungi internet (HTTPS keluar) bila memakai widget cuaca/gempa BMKG.

### Langkah
1. **Pasang WordPress baru** seperti biasa (cPanel/installer 1 klik atau manual), lalu login ke `wp-admin`.
2. **Unggah tema:** *Tampilan > Tema > Tambah Baru > Unggah Tema*, pilih `m-news-0.1.0.zip` (dari folder `dist`), klik **Pasang Sekarang**, lalu **Aktifkan**. (Alternatif: unggah folder `m-news` lewat FTP ke `wp-content/themes/`.)
3. **Aktifkan permalink:** *Pengaturan > Permalink* pilih **Nama Pos** lalu simpan (langkah 4 juga bisa mengaturnya).
4. **Aktifkan lisensi:** buka **Tampilan > Lisensi**, masukkan kunci lisensi (`MNEWS-XXXX-XXXX-XXXX-XXXX`), klik **Aktifkan**. Tanpa lisensi aktif pengunjung melihat halaman "Situs sedang tidak dapat ditampilkan" (situs lokal `.test` dibebaskan). **Lisensi terkunci permanen ke domain pertama yang mengaktifkannya** — tidak ada tombol pindah domain sendiri; hubungi dukungan M-Onetech untuk memindahkan lisensi ke domain lain.
5. **Impor demo (disarankan untuk situs baru):** setelah tema aktif akan muncul pemberitahuan "Impor Demo" di Dashboard; atau buka **Tampilan > Impor Demo**. Biarkan semua kotak tercentang, klik **Impor Demo Sekarang**, tunggu progress bar penuh (±1 menit). Hasilnya: 30 artikel contoh, 5 halaman, menu, widget, pengaturan tampilan, dan logo M-News. Aman diulang; bila ingin membersihkan, klik **Hapus konten demo**.
   - Situs yang **sudah berisi konten sendiri**: hilangkan centang "Hapus konten bawaan WordPress" dan "Judul dan slogan situs" bila tidak ingin diubah. Impor bersifat menambah, konten Anda tidak dihapus.
5. **Sesuaikan identitas** (bagian penting sebelum live):
   - *Tampilan > Sesuaikan > Identitas Situs*: ganti logo (ukuran ±410×88 px, PNG transparan), judul, slogan, dan ikon situs.
   - *Sesuaikan > M-News*:
     - **Warna & Gradient:** pilih preset atau susun 2–4 warna sendiri.
     - **Header:** ticker breaking news, header lengket.
     - **Social Media Follow:** isi URL akun sungguhan (bawaan demo hanya beranda tiap platform).
     - **Social Media Share:** pilih jaringan, urutan, posisi.
     - **Banner Iklan:** label, paragraf sisip, banner samping sticky.
     - **Footer:** alamat redaksi dan teks copyright.
   - *Tampilan > Menu:* ubah menu Utama/Footer/Jaringan Media sesuai situs Anda (tautan "Portal Contoh" hanya contoh).
6. **Widget:** *Tampilan > Widget* (area: Home – Atas/Utama/Sidebar, Artikel – Sidebar/Di Dalam Konten/Setelah Konten, Banner Header, Banner Samping Kiri/Kanan, Footer 1–3).
   - Ganti banner contoh dengan gambar iklan asli (pilih ukuran, isi tautan) atau kode AdSense (widget *Banner Iklan* jenis Kode).
   - **Prakiraan Cuaca:** pilih Provinsi > Kabupaten/Kota > Kecamatan > Desa/Kelurahan atau tempel kode wilayah BMKG (mis. `31.71.03.1001`).
   - **Statistik Pengunjung:** angka awal opsional; hitungan baru terkumpul setelah ada kunjungan.
   - Widget yang tidak diperlukan cukup dihapus dari area (semua widget opsional).
7. **Konten sungguhan:** tulis artikel di *Pos > Tambah Baru*; isi kotak **Info Berita (M-News)** untuk sub-judul, penulis, editor, sumber; pilih gambar unggulan (disarankan ≥ 960×540 px, rasio 16:9) dan tag `headline`/`breaking-news` bila dipakai slider/ticker.
8. **Bersihkan demo saat siap live:** *Tampilan > Impor Demo > Hapus konten demo* (menghapus artikel/halaman/gambar/widget demo saja; konten Anda tidak disentuh), lalu susun widget sendiri.

### Plugin yang disarankan
- **Cache halaman** (LiteSpeed Cache, WP Super Cache, atau W3 Total Cache) — tema sudah ringan dan kompatibel; beberapa bagian (widget, data BMKG) di-cache sendiri.
- **AMP** (plugin resmi) bila ingin halaman AMP: *AMP > Pengaturan*, mode **Transitional** (disarankan) atau **Standard**. Tema sudah lolos validasi tanpa isu.
- **Plugin SEO** (Yoast, Rank Math, dll.) bila diperlukan — tema otomatis mematikan meta OG/JSON-LD miliknya agar tidak dobel.
- Tidak perlu plugin slider, kolom, atau widget tambahan; jQuery tidak dipakai.

### Cek setelah instalasi
- Buka halaman depan, satu artikel, satu kategori, dan pencarian di ponsel dan desktop.
- Uji kecepatan: PageSpeed Insights atau Lighthouse mobile (target: Performance ≥ 90, LCP < 2,5 dtk, CLS < 0,1).
- Bila memakai AMP: buka `alamat-artikel/?amp=1` (Transitional) dan pastikan menu dan gambar tampil; validasi di *AMP > Halaman yang divalidasi*.
- Aktifkan WP-Cron yang andal (server cron tiap 10 menit bila situs berlalu lintas rendah) agar data cuaca/gempa selalu segar; atau biarkan WP-Cron bawaan (dijalankan oleh kunjungan).
- Ganti password admin dan hapus akun contoh; jangan memakai `admin/admin123` di situs publik.

### Kalau ada masalah
- **Layar putih / error:** aktifkan `WP_DEBUG_LOG` dan lihat `wp-content/debug.log`; pastikan PHP ≥ 7.4.
- **Impor demo berhenti di tengah:** buka lagi halaman Impor Demo dan klik impor sekali lagi (artikel yang sudah ada dilewati). Bila server membatasi waktu, naikkan `max_execution_time` atau `memory_limit` (256M disarankan).
- **Gambar demo tidak ada:** ekstensi GD belum aktif.
- **Simpan widget di Customizer gagal:** pastikan memakai versi tema terbaru (galat `widget_setting_too_many_options` sudah diperbaiki).
- **Cuaca/gempa kosong:** server tidak dapat mengakses `api.bmkg.go.id` / `data.bmkg.go.id`; cek firewall atau `allow_url` pembatasan host, dan pastikan kode wilayah benar.
- **Tampilan lama setelah mengubah pengaturan:** kosongkan cache plugin; cache internal tema dibersihkan otomatis saat artikel/widget/pengaturan berubah.

---

## 4. Checklist rilis produksi

M-News mewarisi seluruh mekanisme lisensi dari M-Nata, tapi **statusnya sendiri masih di titik awal** (server yang sama sudah hidup untuk M-Nata, tapi produk `m-news` belum terdaftar di sana). Urutan (lihat juga bagian "Rilis" di `CLAUDE.md`):

1. **Daftarkan produk ke backend.** Server lisensi (`https://api.m-onetech.id/v1`) sudah live untuk M-Nata, tapi **belum tahu soal produk `m-news`**. Minta backend menambahkan kode produk `m-news` (kemungkinan besar tidak perlu kunci tanda tangan baru — satu backend biasanya satu kunci untuk semua produk, tapi konfirmasikan). Kontrak API: `wp-be/permintaan-ke-backend.md` (masih salinan dari M-Nata, sesuaikan bagian yang menyebut nomor versi/tanggal sebelum benar-benar dikirim).
2. **Kunci lisensi.** ⏳ Masih kunci DEV bawaan (`wp-be/mock/dev-keys.json`). Tempel kunci publik yang benar ke `m-news/inc/license/config.php` (`MNEWS_LICENSE_PUBKEY`) setelah dikonfirmasi backend.
3. **Naikkan versi** di `style.css` (Version) dan `readme.txt` (Stable tag) + catatan di Changelog.
4. **Bangun aset:** `npm i csso terser` di folder mana pun, lalu `NODE_PATH=./node_modules node <proyek>/tools/build-assets.js`.
5. **Cek kode:** `php -l`, PHPCS (lihat CLAUDE.md), regenerasi `.pot` (`wp i18n make-pot ...`).
6. **Bangun zip:** `python tools/release.py` (tanpa `--allow-dev-key`; gagal bila kunci DEV masih dipakai). Hasil: `dist/m-news-<versi>.zip` dan `.sha256`.
7. **Uji dari zip** di WordPress bersih: pasang, aktifkan, lisensi -> aktivasi, Impor Demo, buka beranda/artikel/AMP (`?amp=1`), Lighthouse mobile.
8. **Publikasikan rilis** di server lisensi (`php artisan license:release dist/m-news-<versi>.zip --changelog=... --publish`) agar pelanggan lama menerima pembaruan otomatis.
9. **Distribusi ke pelanggan baru:** kirim zip + kunci lisensi (`php artisan license:issue email --plan=pro --max=1`). Pelanggan: Tampilan > Tema > Unggah > Aktifkan > Tampilan > Lisensi > masukkan kunci > Impor Demo (opsional).

Catatan:
- Pembaruan otomatis hanya untuk lisensi aktif; paket diverifikasi SHA-256 bertanda tangan sebelum dipasang.
- Tema tidak dapat mencegah penghapusan pemeriksaan lisensi oleh orang yang memegang kodenya (lisensi GPL); nilai lisensi ada pada pembaruan dan dukungan.
- Kalau membuat situs uji terpisah untuk mencoba alur produksi (seperti `mnataprod` dulu dibuat untuk M-Nata), ingat membersihkannya (folder, database, server tiruan yang berjalan di port) setelah selesai.

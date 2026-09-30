# Permintaan Backend — Lisensi Tema M-News (produk baru di backend yang sudah ada)

Kepada: tim backend `api.m-onetech.id` (Laravel)
Dari: pengembang tema M-News
Versi tema yang memakai kontrak ini: **1.0.0** (kerangka awal, belum dirilis)

---

## Ringkasan

M-News adalah tema kedua M-Onetech (dibangun dari kerangka M-Nata). Sistem lisensinya **memakai backend yang sama**
dengan M-Nata (`https://api.m-onetech.id/v1`, sama-sama Laravel, sama-sama endpoint
`POST /v1/licenses/{activate|verify|deactivate}` dan `POST /v1/products/{produk}/update`). Kontrak API lengkap
(format request/respons, tanda tangan Ed25519, model data, keamanan, skenario penerimaan) **tidak diulang di sini**
— itu sudah didokumentasikan penuh di `../m-nata/wp-be/permintaan-ke-backend.md`. Dokumen ini hanya berisi yang
**berbeda/baru** untuk menambahkan M-News sebagai produk kedua.

---

## Yang perlu dari backend

- [ ] **Daftarkan produk baru dengan kode `m-news`** di tabel `licenses`/validasi produk (field `product` pada
  setiap request sekarang bisa bernilai `m-nata` **atau** `m-news`; tolak kombinasi yang tidak dikenal seperti
  sekarang, dengan `code: product_mismatch`).
- [ ] **Kunci tanda tangan Ed25519**: kemungkinan besar **tidak perlu dibuat baru** — kalau satu keypair
  (`LICENSE_SIGNING_SECRET`/`LICENSE_SIGNING_PUBLIC`) memang dipakai untuk seluruh backend (bukan per-produk),
  M-News memakai kunci publik yang sama persis dengan yang sudah dikirim untuk M-Nata. **Tolong konfirmasi**: apakah
  benar satu kunci untuk semua produk, atau tiap produk butuh pasangan kunci sendiri? Kalau butuh kunci sendiri,
  jalankan `php artisan license:keys` khusus untuk `m-news` dan kirim kunci publiknya terpisah.
- [ ] **Terbitkan lisensi uji** untuk `m-news` (`php artisan license:issue ... --product=m-news`) supaya tema bisa
  diuji ujung-ke-ujung terhadap server sungguhan sebelum rilis pertama.
- [ ] Kalau pembaruan otomatis (`product_releases`, `license:release`) sudah berjalan untuk M-Nata, pastikan
  perintahnya menerima `--product=m-news` juga (atau apa pun cara membedakan produk yang sudah dipakai di sana).

## Yang TIDAK berubah dari kontrak M-Nata

- Bentuk request/respons, field `payload`/`signature`, isi `payload` yang ditandatangani, aturan `nonce`/`issued_at`,
  rate limit, normalisasi domain, keamanan/operasional — semua identik, lihat `../m-nata/wp-be/permintaan-ke-backend.md`
  bagian 2–7.
- Mekanisme pembaruan tema (bagian 6 di dokumen itu) — sama persis, hanya slug produk di URL yang beda
  (`/products/m-news/update` alih-alih `/products/m-nata/update`).

## Status di sisi tema (2026-09-30)

- `MNEWS_LICENSE_PRODUCT` sudah diset ke `'m-news'` di `inc/license/config.php`.
- `MNEWS_LICENSE_PUBKEY` **masih kunci pengembangan** (pasangan baru khusus proyek ini, lihat
  `wp-be/mock/dev-keys.json`) — tempel kunci publik yang benar begitu dikonfirmasi dari poin di atas.
- Server tiruan untuk uji lokal: `wp-be/mock/server.php` (`php -S 127.0.0.1:8090 wp-be/mock/server.php`), kontrak
  sama seperti milik M-Nata, kunci uji berprefiks `MNEWS-TEST-...` (lihat komentar berkas).
- Belum ada lisensi sungguhan yang dicoba dari domain produksi untuk `m-news` — itu langkah terakhir sebelum rilis
  pertama, setelah dua poin di atas beres.

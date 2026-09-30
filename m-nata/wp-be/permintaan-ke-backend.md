# Permintaan Backend — Server Lisensi & Pembaruan Tema M-Nata

Kepada: tim backend `api.m-onetech.id` (Laravel)
Dari: pengembang tema M-Nata
Versi tema yang memakai kontrak ini: **1.2.0**

Dokumen ini **berdiri sendiri**: seluruh yang dibutuhkan untuk mengimplementasikan backend ada di sini. Sisi tema (WordPress) sudah selesai dan teruji terhadap server tiruan (`wp-be/mock/server.php`, pakai sebagai contoh perilaku yang benar) serta di layar admin WordPress sungguhan.

Catatan riwayat: sempat ada draf yang menghapus enforcement lisensi sama sekali dari tema, lalu diputuskan kembali ke
kontrak di dokumen ini (verifikasi bertanda tangan + penguncian tampilan + pembaruan otomatis). **Dokumen ini yang
berlaku.**

---

## 1. Ringkasan

Tema WordPress **M-Nata** memverifikasi lisensinya ke server ini. Bila verifikasi gagal, situs publik menampilkan halaman "Lisensi diperlukan" (HTTP 503) dan fitur tema mati; wp-admin tetap bisa dipakai untuk mengaktifkan ulang. Selain itu, tema mengambil **pembaruan otomatis** dari server ini untuk lisensi yang aktif.

### Daftar yang diminta

**Bagian A — Lisensi (wajib, prioritas 1)**
- [ ] `POST /v1/licenses/activate`, `/verify`, `/deactivate` (bagian 4).
- [ ] Setiap jawaban lisensi **ditandatangani Ed25519** (bagian 5); tema menolak jawaban tanpa tanda tangan yang benar.
- [ ] Tabel `licenses`, `license_activations`, `license_events` (bagian 3).
- [ ] Perintah artisan: buat pasangan kunci, terbitkan lisensi (bagian 8).
- [ ] Panel admin sederhana (bagian 9).
- [ ] Rate limit, log, HTTPS (bagian 7).

**Bagian B — Pembaruan tema (prioritas 2, boleh menyusul; tema tetap berjalan bila endpoint ini belum ada)**
- [ ] `POST /v1/products/m-nata/update` (bagian 6) dan unduhan paket bertanda waktu.
- [ ] Tabel `product_releases` + perintah `license:release` (bagian 6.4).

Seluruh skenario penerimaan ada di bagian 10.

---

## 2. Konvensi umum

- Base URL: `https://api.m-onetech.id/v1` (HTTPS wajib, tolak HTTP). Bila proyek Laravel memakai prefix `/api`, atur `apiPrefix: 'v1'` di `bootstrap/app.php` agar URL persis seperti ini. (Tema memakai konstanta `MNATA_LICENSE_API`; kalau prefix harus lain, kabari agar dirilis ulang.)
- Semua endpoint `POST`, body JSON, header `Accept: application/json`. **Tidak ada token; kunci lisensi adalah kredensialnya.**
- Semua jawaban memakai header `Cache-Control: no-store`.
- Waktu selalu UTC (ISO-8601), jam server harus sinkron (NTP).
- Teks `message` pada error **ditampilkan langsung ke pemilik situs**; tulis dalam bahasa Indonesia yang sopan dan jelas.

Body request (sama untuk semua endpoint):

```json
{
  "license_key": "MNATA-ABCD-EFGH-JKLM-NPQR",
  "product": "m-nata",
  "domain": "contoh.com",
  "site_url": "https://contoh.com/",
  "version": "1.2.0",
  "wp_version": "7.1",
  "php_version": "8.3.0",
  "locale": "id_ID",
  "nonce": "a1b2c3d4e5f6a1b2c3d4e5f6"
}
```

Validasi: `license_key` ≤ 80 karakter `[A-Za-z0-9-]`; `product` wajib `m-nata`; `domain` ≤ 190; `nonce` 8–64 karakter alfanumerik; lainnya opsional. Gagal validasi -> `422` `{"success":false,"code":"invalid_request","message":"Permintaan tidak valid."}`.

---

## 3. Model data

| Tabel | Kolom penting |
|---|---|
| `licenses` | `id`, `key_hash` (char 64, unik), `key_prefix`, `key_last4`, `product` (`m-nata`), `plan` (`pro`, `lifetime`, ...), `customer_name`, `customer_email`, `status` (`active` \| `suspended` \| `revoked`), `max_activations` (default 1), `expires_at` (nullable = seumur hidup), `notes`, timestamps |
| `license_activations` | `id`, `license_id`, `domain`, `site_url`, `ip`, `theme_version`, `wp_version`, `php_version`, `locale`, `activated_at`, `last_check_at`, `deactivated_at` (nullable); **unik** (`license_id`, `domain`) |
| `license_events` | `id`, `license_id` (nullable), `type` (`activate`/`verify`/`deactivate`/`update`/`rejected`), `status_returned`, `domain`, `ip`, `meta` (json), `created_at` |
| `product_releases` (bagian B) | `id`, `product`, `version` (x.y.z, unik per produk), `package_path` (penyimpanan **privat**), `sha256`, `requires`, `requires_php`, `tested`, `changelog_url`, `released_at`, `is_published` |

Aturan:
- **Kunci lisensi tidak disimpan polos.** Simpan `HMAC-SHA256(KUNCI_HURUF_BESAR, LICENSE_PEPPER)`; tampilkan kunci penuh hanya sekali saat diterbitkan. Format: `MNATA-XXXX-XXXX-XXXX-XXXX`, alfabet `ABCDEFGHJKLMNPQRSTUVWXYZ23456789` (tanpa 0/O/1/I), dibangkitkan dengan `random_int`.
- **Status "expired" tidak disimpan**; dihitung dari `expires_at`. Prioritas status efektif: `revoked` > `suspended` > `expired` > `active`.
- **Normalisasi domain** (wajib sama untuk semua endpoint): huruf kecil, buang skema, port, path/query, dan awalan `www.` — `https://WWW.Contoh.com:8080/x` -> `contoh.com`. Subdomain berbeda = domain berbeda.

```php
function normalizeDomain(string $v): string {
    $v = strtolower(trim($v));
    $v = preg_replace('#^[a-z][a-z0-9+.-]*://#', '', $v);
    $v = preg_split('#[/?\#]#', $v)[0];
    $v = preg_replace('/:\d+$/', '', $v);
    return preg_replace('/^www\./', '', $v);
}
```

---

## 4. API lisensi (Bagian A)

### 4.1 `POST /licenses/activate`
Mendaftarkan `domain` pada lisensi. **Idempoten**: domain yang sudah terdaftar (dan belum di-deactivate) tetap sukses tanpa memakai slot baru.

Alur: validasi -> produk harus `m-nata` -> cari lisensi lewat hash kunci -> status efektif harus `active` -> bila domain belum terdaftar, jumlah aktivasi aktif harus < `max_activations` -> simpan/perbarui aktivasi (`deactivated_at = null`, `last_check_at = now`) -> catat event -> kirim payload bertanda tangan.

Sukses `200`:
```json
{ "success": true, "data": { "payload": "<base64 JSON>", "signature": "<base64 Ed25519>" } }
```
Gagal (tidak ditandatangani):

| HTTP | `code` | Kapan | Contoh `message` |
|---|---|---|---|
| 404 | `invalid_key` | kunci tidak ditemukan | Kunci lisensi tidak ditemukan. |
| 403 | `expired` / `revoked` / `suspended` | lisensi tak dapat dipakai | Lisensi ini sudah kedaluwarsa. |
| 409 | `limit_reached` | slot domain penuh | Batas domain untuk lisensi ini sudah tercapai. Nonaktifkan salah satu domain lebih dulu. |
| 422 | `product_mismatch` | produk bukan `m-nata` | Lisensi ini bukan untuk produk M-Nata. |
| 429 | `rate_limited` | terlalu sering | Terlalu banyak percobaan. Coba lagi sebentar lagi. |

```json
{ "success": false, "code": "limit_reached", "message": "Batas domain untuk lisensi ini sudah tercapai. Nonaktifkan salah satu domain lebih dulu." }
```

### 4.2 `POST /licenses/verify`
Dipanggil tema **sekali sehari** (WP-Cron) dan saat pemilik menekan "Periksa ulang". **Selalu `200` dengan payload bertanda tangan**, untuk kunci dikenal maupun tidak; hasil ada di `payload.status`:

| `status` | Arti |
|---|---|
| `active` | valid dan domain ini terdaftar |
| `expired` | lewat `expires_at` |
| `revoked` | dicabut |
| `suspended` | dibekukan sementara |
| `domain_mismatch` | lisensi sah tetapi domain ini belum/tidak lagi terdaftar |
| `invalid` | kunci tidak dikenal atau produk salah |

**Status selain `active` langsung mengunci tema** pada pemeriksaan itu, jadi jangan mengembalikan `revoked`/`suspended` secara tidak sengaja. `verify` memperbarui `last_check_at` (dan versi tema/WP/PHP) pada aktivasi.

### 4.3 `POST /licenses/deactivate`
Membebaskan slot domain (isi `deactivated_at`). `200 {"success": true}`; **idempoten** — kunci/domain tak dikenal pun `200`.

### 4.4 Isi `payload` (yang ditandatangani)

```json
{
  "license": "MNATA-ABCD-****-****-NPQR",
  "product": "m-nata",
  "domain": "contoh.com",
  "status": "active",
  "plan": "pro",
  "expires_at": "2027-09-28T00:00:00+00:00",
  "issued_at": "2026-09-29T10:00:00+00:00",
  "grace_days": 7,
  "message": "",
  "nonce": "<sama persis dengan nonce request>"
}
```
- `expires_at`: ISO-8601, `null` bila seumur hidup.
- `issued_at`: waktu server (UTC). **Tema menolak bila selisih dengan jam server WordPress lebih dari 1 hari.**
- `grace_days`: toleransi bila server ini tak terjangkau (default 7; tema membatasi 1–60).
- `nonce`: **wajib di-echo** (mencegah replay jawaban lama).
- `domain`: domain **ternormalisasi**; tema membandingkannya dengan domainnya sendiri (tanpa `www.`).
- `product`: `m-nata`.

---

## 5. Tanda tangan (Ed25519, libsodium)

```php
$json = json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
$sig  = sodium_crypto_sign_detached($json, $secretKey);   // $secretKey = base64_decode(env('LICENSE_SIGNING_SECRET'))
return ['success' => true, 'data' => [
    'payload'   => base64_encode($json),
    'signature' => base64_encode($sig),
]];
```

Tema memverifikasi **byte `$json` apa adanya** (jangan diformat ulang atau disusun ulang setelah ditandatangani) dengan kunci publik yang tertanam di `inc/license/config.php`.

- Buat pasangan kunci **produksi** dengan `php artisan license:keys` (bagian 8). Simpan `LICENSE_SIGNING_SECRET` hanya di `.env` server, **jangan di git**. Kirimkan **kunci publik** ke pengembang tema untuk ditempel di `MNATA_LICENSE_PUBKEY`; tema dirilis ulang dengan kunci itu.
- Kunci di `wp-be/mock/dev-keys.json` adalah **kunci pengembangan** — jangan pernah dipakai di produksi.
- Rotasi kunci = merilis tema baru dengan kunci publik baru (rencanakan bila perlu dukungan dua kunci publik).
- Pastikan ekstensi PHP `sodium` aktif (bawaan PHP ≥ 7.2).

---

## 6. Pembaruan tema (Bagian B)

### 6.1 `POST /products/m-nata/update`
Body request sama seperti bagian 2 (`version` = versi tema terpasang). Dipanggil tema paling sering tiap 12 jam, hanya untuk lisensi yang sedang `active` di sisi tema.

Selalu `200` dengan **payload bertanda tangan yang sama bentuknya** seperti 4.4, ditambah kunci `update`:
- lisensi bukan `active` atau domain tidak cocok -> `status` sesuai (`expired`, `domain_mismatch`, ...) dan `"update": null`;
- `active` dan sudah versi terbaru -> `"update": null`;
- `active` dan ada rilis terbit yang lebih baru (`product_releases.is_published`, versi tertinggi menurut SemVer) ->

```json
"update": {
  "version": "1.3.0",
  "package": "https://api.m-onetech.id/v1/downloads/m-nata/1.3.0.zip?expires=1790000000&signature=…",
  "sha256": "9f2c…(64 heksa huruf kecil)…",
  "requires": "6.3",
  "requires_php": "7.4",
  "tested": "7.1",
  "released_at": "2026-10-01T00:00:00+00:00",
  "changelog_url": "https://m-onetech.id/m-nata/changelog"
}
```

Aturan yang **ditegakkan tema** (jawaban yang melanggar diabaikan): `version` harus `x.y.z` dan lebih tinggi dari yang terpasang; `sha256` 64 heksa; `package` harus **HTTPS** dengan host `api.m-onetech.id` atau subdomain `*.m-onetech.id`.

### 6.2 Unduhan paket
`GET /downloads/m-nata/{version}.zip?expires=…&signature=…` — **URL bertanda tangan berumur pendek (±15 menit)**, dibuat server pada setiap panggilan `update` (di Laravel: `URL::temporarySignedRoute`). Tidak perlu header auth. Kembalikan zip dari penyimpanan privat dengan `Content-Type: application/zip`. URL kedaluwarsa/rusak -> `403`. Isi zip: satu folder `m-nata/` (struktur seperti hasil `python tools/release.py`).

Tema **menghitung SHA-256 zip yang diunduh dan membatalkan pembaruan bila tidak sama** dengan `sha256` pada payload bertanda tangan, sehingga `sha256` harus dihitung dari berkas yang benar-benar disajikan.

### 6.3 Yang dicatat
Event `update` di `license_events` (versi diminta, versi ditawarkan). Berguna untuk melihat sebaran versi pelanggan.

### 6.4 Rilis
- Perintah `php artisan license:release {zip} {--changelog=} {--requires=6.3} {--php=7.4} {--tested=7.1} {--publish}`: membaca versi dari `style.css` di dalam zip (baris `Version:`), menghitung SHA-256, menyimpan zip ke penyimpanan **privat** (bukan `public/`), membuat baris `product_releases` (tidak terbit sampai `--publish`).
- Panel admin: daftar rilis, terbitkan/tarik rilis, unggah zip baru.
- Menarik rilis (`is_published = false`) menghentikan penawaran; tema yang sudah memperbarui tidak diapa-apakan.

---

## 7. Keamanan & operasional

- HTTPS saja. `Cache-Control: no-store` pada semua jawaban.
- **Rate limit per IP**: `activate` 10/menit, `verify` 60/menit, `deactivate` 10/menit, `update` 30/menit (`429` + `code: rate_limited`). Tambahkan batas per kunci lisensi bila perlu. Pastikan `trusted proxies` benar agar IP klien tepat.
- Validasi input ketat (bagian 2). Jangan pernah mengembalikan kunci privat, hash kunci, atau data pelanggan.
- Catat setiap request ke `license_events` tanpa kunci polos (simpan `key_prefix`/hash). Simpan IP untuk deteksi penyalahgunaan; patuhi kebijakan privasi (hanya domain, versi, IP).
- **Ketersediaan sangat penting**: tema toleran `grace_days` hari, tetapi server yang mati lama akan mengunci situs pelanggan. Pantau uptime `/v1/licenses/verify`, siapkan alarm dan cadangan basis data.
- Simpan zip rilis di penyimpanan privat; jangan pernah menyajikannya lewat URL publik tetap.

---

## 8. Perintah artisan

- `php artisan license:keys` — membuat pasangan Ed25519 (base64) dan mencetak baris `.env` (`LICENSE_SIGNING_SECRET`, `LICENSE_SIGNING_PUBLIC`, `LICENSE_PEPPER`) serta baris `define( 'MNATA_LICENSE_PUBKEY', '…' );` untuk tema.
- `php artisan license:issue {email} {--name=} {--plan=pro} {--max=1} {--expires=} {--product=m-nata}` — menerbitkan lisensi dan **mencetak kunci penuh sekali saja**.
- `php artisan license:release …` — bagian 6.4.

Konfigurasi (`.env`): `LICENSE_SIGNING_SECRET`, `LICENSE_SIGNING_PUBLIC`, `LICENSE_PEPPER` (mengubahnya membuat semua hash kunci tersimpan tak cocok), `LICENSE_GRACE_DAYS=7`.

## 9. Panel admin (minimal)

Daftar lisensi (cari email/domain/4 digit akhir) dan aksi: terbitkan, ubah batas domain, perpanjang/ubah `expires_at`, bekukan/cabut/aktifkan, lihat dan **hapus aktivasi** (reset domain), lihat log event; kelola rilis (bagian 6.4). Boleh Filament/Nova atau halaman Blade sederhana dengan autentikasi admin.

---

## 10. Skenario penerimaan

Sisi tema sudah diuji terhadap server tiruan untuk skenario 1–13, 15–17 (bagian A dan B). Skenario 14 (rate limit) dan sisi Laravel belum dijalankan.

| # | Skenario | Hasil yang diharapkan |
|---|---|---|
| 1 | Activate kunci sah, domain baru | 200 + payload `active`, domain tercatat |
| 2 | Activate ulang domain yang sama | 200, tidak menambah slot |
| 3 | Activate domain ke-2 pada lisensi `max=1` | 409 `limit_reached` |
| 4 | Activate kunci tak dikenal / format salah | 404 `invalid_key` / 422 |
| 5 | Activate kunci expired / revoked / suspended | 403 dengan `code` sesuai |
| 6 | Verify kunci sah, domain terdaftar | 200 `active`, `last_check_at` terbarui |
| 7 | Verify setelah admin mencabut lisensi | 200 `revoked` (tema langsung terkunci) |
| 8 | Verify domain yang belum terdaftar | 200 `domain_mismatch` |
| 9 | Verify kunci tak dikenal | 200 `invalid` (tetap bertanda tangan) |
| 10 | Lisensi lewat `expires_at` | verify -> `expired`; `null` -> tidak pernah expired |
| 11 | Deactivate lalu activate di domain lain | slot bebas, sukses |
| 12 | `nonce` di payload = `nonce` request | selalu, semua respons |
| 13 | Tanda tangan valid dengan kunci publik | `sodium_crypto_sign_verify_detached` lolos |
| 14 | Rate limit terlampaui | 429 `rate_limited` |
| 15 | Produk selain `m-nata` | 422 `product_mismatch` |
| 16 | `update` ketika ada rilis lebih baru | payload `active` + objek `update` valid; unduhan 200 dan `sha256` cocok |
| 17 | `update` saat lisensi bukan `active`, atau sudah terbaru | `update: null` |
| 18 | URL unduhan kedaluwarsa/rusak | 403 |

Contoh uji manual:

```bash
curl -s https://api.m-onetech.id/v1/licenses/activate \
  -H 'Content-Type: application/json' \
  -d '{"license_key":"MNATA-XXXX-XXXX-XXXX-XXXX","product":"m-nata","domain":"contoh.com","site_url":"https://contoh.com/","version":"1.2.0","nonce":"a1b2c3d4e5f6a1b2c3d4e5f6"}'
```

## 11. Perilaku sisi tema (agar backend paham dampaknya)

- Tema menyimpan status di database WordPress dengan segel HMAC; mengedit manual membuat lisensi dianggap tidak ada.
- `active` yang belum diverifikasi ulang lebih dari `grace_days + 1` hari dianggap `unverified` (terkunci). Jawaban gagal / 5xx / tanda tangan salah **tidak** mengunci seketika; hanya `payload.status` bertanda tangan yang mengunci.
- Host pengembangan (`localhost`, `*.test`, `*.local`, ...) dibebaskan dari lisensi di sisi tema.
- Domain produksi tanpa lisensi aktif akan terkunci — **jangan merilis tema ke pelanggan sebelum server ini hidup dan kunci publik produksi tertanam** (`tools/release.py` menolak build produksi selama masih memakai kunci pengembangan).
- Pembaruan hanya ditawarkan/dipasang untuk lisensi `active`; paket diverifikasi SHA-256 sebelum dipasang.
- **Cek ulang harian (bukan mingguan)**: dipilih agar jendela waktu antara lisensi dicabut dan situs benar-benar terkunci tetap pendek (≤ `grace_days` + 1 hari), tanpa membebani API (satu request kecil per situs per hari lewat WP-Cron, bukan per-pengunjung). Ini murni mempersempit jendela bagi pelanggan yang tidak mengutak-atik kode tema — tema berlisensi GPL tetap tidak bisa mencegah penghapusan pemeriksaan oleh siapa pun yang memegang kodenya.

## 12. Isi folder `wp-be/`

- `permintaan-ke-backend.md` — dokumen ini.
- `permintaan-ke-dev-theme.md` — riwayat tindak lanjut (kebijakan kunci domain §5, konfirmasi pembaruan §6); masih relevan sebagai referensi tambahan, tidak bertentangan dengan dokumen ini.
- `mock/server.php` + `mock/dev-keys.json` — server tiruan berkontrak sama untuk menguji tema (`php -S 127.0.0.1:8090 wp-be/mock/server.php`); kunci uji ada di komentar berkas. Bukan untuk produksi.

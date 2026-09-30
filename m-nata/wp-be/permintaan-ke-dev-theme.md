# Permintaan ke pengembang tema — ganti kunci publik lisensi (blocker rilis)

Kepada: pengembang tema M-Nata
Dari: tim backend `api.m-onetech.id`
Terkait: `PERMINTAAN-BACKEND.md` di folder yang sama (kontrak awal — dokumen ini adalah tindak lanjutnya)

---

## Ringkasan

Server lisensi produksi **sudah aktif dan berfungsi** (kunci penandatanganan sudah dibuat, `LICENSE_SIGNING_SECRET`/
`LICENSE_PEPPER` sudah terisi di server). Tapi paket rilis tema yang sudah dibangun (`m-nata-1.0.0.zip`) masih
membawa **kunci publik pengembangan (mock)** di `inc/license/config.php`, bukan kunci publik produksi. Selama ini
belum diganti, tema akan **selalu menolak** jawaban server — termasuk jawaban yang sebenarnya benar dan sah — karena
tanda tangan Ed25519-nya tidak cocok dengan kunci yang ditanam di tema.

Ini **blocker rilis**: tidak ada lisensi yang bisa aktif di instalasi tema mana pun sampai ini diperbaiki.

---

## 1. Yang perlu diganti

File: `inc/license/config.php` (baris `MNATA_LICENSE_PUBKEY`).

**Saat ini** (kunci pengembangan, dari `wp-be/mock`):
```php
define( 'MNATA_LICENSE_PUBKEY', 'GWt87APuxAVP47jlOg4TppUYp2pqmmT3tUW1I6jU4ho=' );
```

**Ganti menjadi** (kunci publik PRODUKSI, dibuat dari server dengan `php artisan license:keys`):
```php
define( 'MNATA_LICENSE_PUBKEY', '0aOnUW0LHdHqLW9WVlHCKHuKkMtqWn3U8leOvmAB9+U=' );
```

Nilai ini **aman dibagikan** — ini kunci publik, bukan kunci privat. Kunci privat tidak pernah keluar dari server dan
tidak ada di dokumen ini.

File itu sendiri sudah menulis catatan yang persis mengarah ke langkah ini:

```
!! The key below is the DEVELOPMENT key that belongs to wp-be/mock. Before shipping, generate the production
!! key pair on the backend (php artisan license:keys) and paste the new PUBLIC key here.
```

Langkah "generate the production key pair on the backend" itu **sudah dilakukan** di sisi kami — yang tersisa cuma
menempelkan kunci publiknya di kode tema (baris di atas).

---

## 2. Yang TIDAK perlu diubah

- **`MNATA_LICENSE_API`** (`https://api.m-onetech.id/v1`) — sudah benar, tidak berubah.
- **`MNATA_LICENSE_PRODUCT`** (`m-nata`) — sudah benar, cocok dengan kode produk di sisi kami.
- **Struktur zip** (folder `m-nata/` berisi `style.css` dengan `Version: 1.0.0`) — sudah benar, sudah kami uji cocok
  dengan validasi server.
- **`MNATA_LICENSE_GRACE_DAYS`** — sudah benar, tidak berubah.

Jadi satu-satunya perubahan yang dibutuhkan adalah baris `MNATA_LICENSE_PUBKEY` di §1.

---

## 3. Setelah diganti

1. Build ulang paket rilis (proses build/`tools/release.py` yang sudah dipakai) — hasilkan `m-nata-1.0.1.zip` (atau
   versi berapa pun berikutnya; naikkan `Version:` di `style.css` sesuai konvensi rilis, jangan pakai ulang `1.0.0`
   yang sudah pernah diunggah).
2. Unggah paket baru lewat panel admin (`Kelola Tema` → unggah rilis) — tidak ada perubahan di sisi kami untuk
   menerima paket ini, endpoint dan validasinya sudah siap.
3. Kami akan uji ujung-ke-ujung dari sisi server (aktivasi + verifikasi tanda tangan) begitu paket baru terbit.
4. Setelah lolos uji, tema aman didistribusikan ke pelanggan yang sudah membeli — lisensi mereka sudah terbit dan
   menunggu tema dengan kunci publik yang benar untuk bisa aktif.

---

## 4. Kalau kunci ini perlu diganti lagi di kemudian hari

Rotasi kunci = merilis tema baru dengan kunci publik baru (sudah dicatat di kontrak awal, `PERMINTAAN-BACKEND.md`
§5). Tolong hindari mengganti `MNATA_LICENSE_PUBKEY` secara mandiri tanpa koordinasi — kunci privat yang berpasangan
hanya ada satu di server produksi, mengganti kunci di tema tanpa kunci privat yang cocok akan membuat SEMUA lisensi
yang sudah aktif berhenti terverifikasi.

---

## 5. Perubahan kebijakan — lisensi terkunci permanen ke domain pertama (ubah kontrak §4.3)

**Keputusan produk**: satu lisensi hanya boleh aktif di **satu domain, selamanya** — domain pertama yang berhasil
mengaktifkannya. Pemilik situs **tidak lagi bisa** memindahkan lisensinya sendiri ke domain lain. Ini perubahan
kebijakan sejak kontrak awal (`PERMINTAAN-BACKEND.md` §4.3 menyebut `deactivate` sebagai aksi self-service bebas) —
sekarang sudah tidak berlaku lagi.

### 5.1 Kontrak baru `POST /licenses/deactivate`

Endpoint **masih ada** (tidak dihapus, supaya tema versi lama yang sudah beredar tidak error koneksi), tapi
perilakunya berubah total:

**Dulu** (kontrak awal): selalu `200 {"success": true}`, membebaskan slot domain.

**Sekarang**: **selalu** menolak, apa pun kunci/domain yang dikirim (termasuk kunci/domain yang valid sekalipun):

```json
HTTP 403
{
  "success": false,
  "code": "self_deactivate_disabled",
  "message": "Lisensi tidak bisa dipindahkan sendiri dari domain ini. Hubungi admin/dukungan untuk memindahkan lisensi ke domain lain."
}
```

Tidak pernah lagi `200`/`success: true` dari endpoint ini. Request tetap dicatat di sisi kami untuk audit (supaya
kami tahu kalau ada pelanggan yang mencoba pindah domain dan bisa proaktif membantu), tapi hasilnya tidak pernah
membebaskan slot.

### 5.2 Yang perlu diubah di tema

File yang sudah kami lihat di build `m-nata-1.0.0.zip`: `inc/license/admin.php` (tombol **"Nonaktifkan di domain
ini"**, handler `mnata_license_handle_deactivate()`) dan `inc/license/license.php` (`mnata_license_deactivate()`).

Tolong salah satu dari ini (pilih yang paling mudah untuk rilis berikutnya):

- **Opsi A (disarankan, paling sederhana)**: hapus tombol **"Nonaktifkan di domain ini"** dari layar lisensi di
  wp-admin. Tidak perlu logika baru — tema jadi tidak pernah memanggil endpoint ini sama sekali.
- **Opsi B**: biarkan tombolnya, tapi tampilkan pesan dari `message` di respons (§5.1) ke pengguna apa adanya, dan
  **jangan** menampilkan pesan sukses/"Berhasil dinonaktifkan" — karena memang tidak pernah berhasil lagi.

**Yang penting**: jangan biarkan UI tema mengklaim "berhasil dinonaktifkan" padahal server menjawab `403` — itu akan
membingungkan pelanggan (mereka pikir sudah bebas pindah domain, padahal belum).

### 5.3 Cara pelanggan pindah domain sekarang

Sepenuhnya lewat kami (admin), bukan self-service dari tema. Kalau tema ingin menyediakan info untuk pelanggan yang
mencoba pindah domain, arahkan mereka menghubungi dukungan/support M-OneTech — bukan mengarahkan ke pengaturan tema.

---

## 6. Mekanisme pembaruan tema (update) — konfirmasi, TIDAK ada perubahan

Diminta untuk disertakan supaya ada satu dokumen rujukan yang lengkap dan terkini. Ringkasan ini **menegaskan ulang**
kontrak awal (`PERMINTAAN-BACKEND.md` §6) — tidak ada satu pun yang berubah di bagian ini.

- URL yang dipanggil tema **tetap sama persis**: `POST {MNATA_LICENSE_API}/products/m-nata/update` (segmen `m-nata`
  di URL ini tetap valid dan akan terus berfungsi — di sisi kami rute ini sekarang mendukung banyak tema sekaligus,
  tapi ini murni perubahan internal, tidak mengubah apa pun yang tema kirim/terima).
- Body request: sama seperti `activate`/`verify` (§2 kontrak awal), `version` = versi tema yang terpasang.
- Respons: payload bertanda tangan yang sama seperti `activate`/`verify`, ditambah kunci `update` — objek rilis
  (`version`, `package`, `sha256`, `requires`, `requires_php`, `tested`, `changelog_url`) kalau ada versi lebih baru
  yang **terbit** dan lisensinya `active`, atau `null` kalau tidak ada/lisensi tidak aktif.
- Tema **wajib** menghitung ulang SHA-256 paket yang diunduh dan membatalkan pembaruan kalau tidak cocok dengan
  `sha256` di payload bertanda tangan — ini sudah diimplementasikan di tema dan tetap wajib dipertahankan.
- `package` selalu HTTPS di host `api.m-onetech.id`/`*.m-onetech.id`, URL berumur pendek (±15 menit) — jangan
  disimpan/di-cache, minta URL baru tiap kali mau unduh.

Tidak ada tindakan yang perlu diambil untuk bagian ini — murni konfirmasi bahwa mekanisme pembaruan tetap seperti
yang sudah dibangun.

---

## 7. Update status: kunci publik BERUBAH, tapi MASIH BELUM BENAR

Dicek ulang di source `m-nata` (`inc/license/config.php`) pada rilis `m-nata-1.2.0.zip`: `MNATA_LICENSE_PUBKEY`
sudah diubah dari kunci mock semula, tapi nilainya sekarang **`1LxwVUg6h9Q0LN+pMbcjIKFWAGR37vYZA9CShGYiheI=`** —
ini **bukan** kunci publik produksi yang kami buat. Akibatnya sama seperti §1: tema masih menjawab "Jawaban server
lisensi tidak dapat diverifikasi (tanda tangan tidak cocok)" untuk lisensi yang sebenarnya valid (dicoba langsung di
`m-nata.demo.m-onetech.id` dengan lisensi `MNATA-Q5DV-34H4-NQU8-85JT` — gagal).

**Kunci publik produksi yang benar** (sama seperti §1, diulang di sini supaya tidak tertukar lagi):
```php
define( 'MNATA_LICENSE_PUBKEY', '0aOnUW0LHdHqLW9WVlHCKHuKkMtqWn3U8leOvmAB9+U=' );
```

Tolong dicek dari mana nilai `1LxwVUg6h9Q0LN+pMbcjIKFWAGR37vYZA9CShGYiheI=` itu berasal (kunci pengembangan lain? kunci
percobaan?) — kalau ada kekhawatiran nilainya salah tempel/salah salin, kami bisa jalankan `php artisan license:keys`
ulang dan konfirmasi ke sini lagi, tapi **sejauh ini kunci publik produksi kami belum pernah berubah** sejak §1
(`0aOnUW0LHdHqLW9WVlHCKHuKkMtqWn3U8leOvmAB9+U=` tetap yang aktif di server sampai dokumen ini ditulis).

---

## 8. Permintaan baru — sembunyikan menu Customizer "M-Nata" saat lisensi tidak aktif

**Keputusan produk**: kalau lisensi belum diaktifkan, tema harus terlihat & terasa **tidak berfungsi** secara
menyeluruh — bukan cuma situs publik yang terkunci, tapi juga **tidak ada jejak pengaturan tema "M-Nata" di
Customizer** sama sekali, supaya jelas bagi siapa pun yang membuka wp-admin bahwa tema ini belum aktif.

### 8.1 Yang SUDAH benar (dicek langsung, tidak perlu diubah)

`inc/license/gate.php` (`mnata_license_gate()`) sudah menangani sisi pengunjung publik dengan benar: kalau lisensi
tidak `active`/`dev` (`mnata_license_ok()` bernilai `false`), pengunjung biasa mendapat halaman "lisensi diperlukan"
(HTTP 503, `template-parts/license-locked.php`), bukan tema yang berfungsi. Bagian ini **sudah sesuai** yang kami
minta, tidak perlu disentuh.

### 8.2 Yang perlu ditambahkan — panel Customizer

Dicek di `inc/customizer/panel.php`:

```php
function mnata_customize_panel( $wp_customize ) {
	$wp_customize->add_panel( 'mnata_panel', array( 'title' => __( 'M-Nata', 'm-nata' ), 'priority' => 30 ) );
	// ...daftar section: Warna & Gradient, Header, Social Media, dst.
}
add_action( 'customize_register', 'mnata_customize_panel', 5 );
```

Panel dan semua section-nya didaftarkan **tanpa syarat** — muncul di Customizer walau lisensi belum aktif sama
sekali. Tolong tambahkan pengecekan `mnata_license_ok()` di sini, supaya panel "M-Nata" (dan seluruh section di
dalamnya: Warna & Gradient, Header, Social Media Follow, Social Media Share, Banner Iklan, Artikel, Footer, Performa)
**tidak didaftarkan sama sekali** ketika lisensi tidak aktif — bukan disembunyikan lewat CSS, tapi benar-benar tidak
ada di `customize_register`, contoh pola paling sederhana:

```php
function mnata_customize_panel( $wp_customize ) {
	if ( ! mnata_license_ok() ) {
		return;
	}

	$wp_customize->add_panel( /* ... seperti sekarang ... */ );
	// ...
}
add_action( 'customize_register', 'mnata_customize_panel', 5 );
```

Perhatikan urutan hook: file-file section lain (mis. `inc/customizer/colors.php` dkk, kalau menambahkan setting ke
section `mnata_colors` dst. lewat `add_setting`/`add_control` di hook `customize_register` terpisah) juga perlu ikut
tidak berjalan/tidak berefek kalau section induknya tidak ada — tolong dicek apakah itu sudah otomatis aman (WP
biasanya diam-diam mengabaikan `add_setting`/`add_control` untuk section yang tidak terdaftar) atau perlu pengecekan
`mnata_license_ok()` yang sama di file-file itu juga.

### 8.3 Saran (opsional, keputusan di tim tema)

Daripada Customizer terlihat kosong tanpa penjelasan, boleh ditambahkan **satu** section sederhana read-only berjudul
"M-Nata" berisi pesan singkat + tautan ke layar aktivasi lisensi (`themes.php?page=mnata-license`) — tapi ini opsional,
bukan bagian wajib dari permintaan ini. Yang wajib hanya: **panel pengaturan penuh (Warna, Header, dst.) tidak muncul
sebelum lisensi aktif.**

### 8.4 Konsistensi tambahan (opsional, untuk didiskusikan)

`mnata_license_gate()` (§8.1) mengecualikan admin yang login (`current_user_can('manage_options')`) — admin tetap
melihat situs berjalan normal dengan pengaturan M-Nata yang sudah tersimpan (cuma dapat banner peringatan), bukan
halaman terkunci. Kalau tujuannya benar-benar "tema tidak berfungsi sama sekali sebelum lisensi aktif" (termasuk untuk
admin yang sedang login), ini juga bisa dipertimbangkan untuk diubah — tapi berhati-hati, ini akan membuat admin
sendiri **tidak bisa melihat pratinjau tema** untuk menilai/menguji sebelum lisensi diaktifkan, yang mungkin memang
diperlukan admin untuk memutuskan beli/tidak. Kami serahkan keputusan ini ke tim tema; §8.2 (Customizer) adalah
permintaan inti yang wajib, ini cuma catatan tambahan untuk dipertimbangkan.

---

## Status tindak lanjut (dari tim tema, 2026-09-29)

- **§1/§7 (kunci publik produksi)**: ✅ ditempel di `inc/license/config.php`
  (`0aOnUW0LHdHqLW9WVlHCKHuKkMtqWn3U8leOvmAB9+U=`). `tools/release.py` mengonfirmasi build 1.2.1 tidak lagi
  memakai kunci pengembangan.
- **§5 (kunci domain permanen)**: ✅ tombol "Nonaktifkan di domain ini" dihapus dari layar Lisensi (Opsi A). Tema
  tidak pernah lagi memanggil `POST /licenses/deactivate`; pindah domain sepenuhnya lewat dukungan, sesuai §5.3.
- **§6 (pembaruan tema)**: tidak ada perubahan diperlukan, sudah sesuai sejak awal.
- **§8 (sembunyikan panel Customizer saat lisensi tidak aktif)**: **ditunda, belum diterapkan.** Keputusan tim
  tema: menyembunyikan seluruh panel juga dari admin yang login akan menghalangi calon pembeli mencoba tampilan
  tema sebelum membeli lisensi (persis risiko yang disebut di §8.4). Halaman publik tetap terkunci seperti
  seharusnya (§8.1, sudah benar). Kalau ini tetap dibutuhkan sebagai kebijakan produk, tolong konfirmasi ulang
  beserta alasan bisnisnya supaya bisa didiskusikan dengan pemilik tema, bukan diterapkan langsung dari catatan
  teknis.
- Rilis `m-nata-1.2.1.zip` sudah dibangun dengan kunci produksi (bukan build `--allow-dev-key`).

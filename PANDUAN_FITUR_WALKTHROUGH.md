# 🌊 Panduan & Dokumentasi Fitur: Apps Hub Kelautan.ID
> **Platform Wrapper Micro-App Terpadu & Katalog Digital Kelautan.ID**  
> *Sesuai Spesifikasi Product Requirements Document (PRD) & Standar Keamanan Sandboxing v2.4*

---

## 📑 Daftar Isi
1. [Gambaran Umum Sistem](#1-gambaran-umum-sistem)
2. [Akses Publik (Tanpa Login)](#2-akses-publik-tanpa-login)
3. [Engine Wrapper iFrame & Eksekusi Aplikasi](#3-engine-wrapper-iframe--eksekusi-aplikasi)
4. [Autentikasi & Manajemen Akun](#4-autentikasi--manajemen-akun)
5. [Fitur Kontributor: Pengajuan & Manajemen Aplikasi](#5-fitur-kontributor-pengajuan--manajemen-aplikasi)
6. [Fitur Administrator & Panel Tata Kelola](#6-fitur-administrator--panel-tata-kelola)
7. [Simulasi Micro-App Interaktif (Mock Engine)](#7-simulasi-micro-app-interaktif-mock-engine)
8. [Matriks Hak Akses (RBAC)](#8-matriks-hak-akses-rbac)

---

## 1. Gambaran Umum Sistem

**Apps Hub Kelautan.ID** adalah platform berbasis web berarsitektur *Single Page Application (SPA) Wrapper* yang berfungsi sebagai pintu gerbang terpadu untuk menghosting, mengelola, mengamankan, dan mengakses seluruh aplikasi mikro internal (Google Apps Script / GAS, web tools, dan aplikasi analitik) tanpa perlu berpindah-pindah domain.

### Keunggulan Arsitektur:
- **Clean URL Routing**: Pengguna mengakses aplikasi dengan URL bersih `https://apps.kelautan.id/[kategori]/[slug-aplikasi]` (contoh: `/operasional/e-logbook`).
- **Sandboxing Ketat (NFR 1)**: Modul pihak ketiga diisolasi di dalam `iframe` dengan batasan keamanan browser guna mencegah script berbahaya mengambil alih portal utama.
- **SSO Auth Forwarding (FR 5.3)**: Meneruskan token keamanan bertanda tangan HMAC-SHA256 ke dalam aplikasi mikro agar aplikasi mengenali identitas pengguna aktif secara otomatis.

---

## 2. Akses Publik (Tanpa Login)

Seluruh pengguna umum, staf lapangan, maupun nelayan dapat langsung memanfaatkan aplikasi resmi yang telah disetujui (*Approved*) tanpa hambatan autentikasi.

| Fitur | Deskripsi | Cara Penggunaan |
| :--- | :--- | :--- |
| **Katalog Terbuka** | Menampilkan kartu-kartu aplikasi mikro aktif lengkap dengan ikon, kategori, dan deskripsi singkat. | Buka beranda `/` langsung dari peramban. |
| **Pencarian Instan (*Search-as-you-type*)** | Filter pencarian instan tanpa *reload* halaman berdasarkan judul, deskripsi, atau modul aplikasi. | Ketik kata kunci pada kotak pencarian di bagian atas katalog. |
| **Filter Kategori** | Menyaring aplikasi berdasarkan rumpun modul (Google Apps Script, Operasional, POS, dsb). | Klik salah satu pill kategori di bilah tab filter. |
| **Aplikasi Terpopuler** | Widget samping yang menampilkan 3 aplikasi dengan frekuensi peluncuran (*hits*) tertinggi. | Terlihat pada sidebar kanan bagian atas. |
| **Dukungan Pusat Data** | Informasi kontak siaga petugas NOC Pusat Data Kelautan.ID jika terjadi kendala akses. | Tertera pada sidebar kanan bawah (`@kelautan_id_ops`). |

---

## 3. Engine Wrapper iFrame & Eksekusi Aplikasi

Ketika sebuah aplikasi dipilih dari katalog, sistem akan memuatnya di dalam **Core Engine Wrapper** dengan bilah kendali profesional di bagian atas:

```
+-----------------------------------------------------------------------------------+
| [← Kembali]  Portal > Operasional > E-Logbook   [SSL Clean Route: /operasional/..] |
|                                   [Token HMAC] [Muat Ulang] [Tab Baru] [Layar Penuh] |
+-----------------------------------------------------------------------------------+
|                                                                                   |
|                   AREA EKSEKUSI APLIKASI (SANDBOXED IFRAME)                       |
|                 (Google Apps Script / TypeScript SPA / Web Tool)                  |
|                                                                                   |
+-----------------------------------------------------------------------------------+
```

### Pilihan 2 Mode Tampilan iFrame Live:

Aplikasi web dapat dibuka dalam dua versi tampilan sesuai kebutuhan pengguna:

#### 1. Versi Standar (Portal Wrapper Mode) — `/{kategori}/{slug}`
Format default yang menampilkan header kendali interaktif portal:
- **Tombol Kembali ke Katalog (`←`)**: Mengembalikan pengguna ke katalog utama dalam satu klik.
- **Address Pill URL Bersih**: Menampilkan alamat URL resmi aplikasi dengan tombol **Salin Tautan (Copy Link)** cepat.
- **Inspektur Token Keamanan (HMAC Token)**: 
  - Klik badge *HMAC Token Active* untuk membuka modal diagnostik.
  - Menampilkan rincian User ID, Nama, Email, Unit Kerja, dan string payload token terenkripsi yang sedang aktif dikirimkan ke aplikasi.
- **Muat Ulang Aplikasi (`refresh`)**: Me-reload konten di dalam iFrame tanpa me-refresh seluruh halaman peramban.
- **Buka di Tab Baru (`open_in_new`)**: Membuka URL aplikasi secara mandiri di tab peramban terpisah.
- **Mode Full Frameless (`aspect_ratio`)**: Beralih langsung ke Versi 2 (Layar Penuh Murni).
- **Layar Penuh Browser (`fullscreen`)**: Memaksimalkan jendela aplikasi hingga memenuhi seluruh layar monitor (*Full Screen API*).
- **Bilah Diagnostik Keamanan**: Menampilkan atribut sandbox isolasi, status header *Anti-Clickjacking*, dan indikator latensi real-time.

#### 2. Versi Full Layar Penuh Murni (Frameless 100% Viewport Mode) — `/{kategori}/{slug}?mode=full` atau `/full/{kategori}/{slug}`
Format frameless yang menghilangkan seluruh elemen portal dan memaksimalkan viewport peramban 100% tanpa margin, padding, atau scrollbar ganda:
- **Judul Halaman Dinamis**: Tag `<title>` otomatis mengikuti judul aplikasi yang diinputkan pengguna saat pendaftaran.
- **Sumber iFrame Dinamis**: Tag `<iframe src="...">` otomatis memuat URL aplikasi (Google Apps Script / SPA) yang diisi pengaju beserta parameter autentikasi terusan portal.
- **Tata Letak Bersih Murni**: Menggunakan layout CSS:
  ```css
  html, body {
      margin: 0;
      padding: 0;
      width: 100%;
      height: 100%;
      overflow: hidden;
      background-color: #ffffff;
  }
  iframe {
      width: 100%;
      height: 100%;
      border: none;
      display: block;
  }
  ```
- **Navigasi Melayang Ringan (*Discreet Floating Quick Switcher*)**:
  - Terdapat tombol melayang transparan di pojok kanan atas yang muncul saat kursor didekatkan.
  - Memudahkan pengguna untuk beralih kembali ke **Mode Wrapper** atau ke **Katalog** kapan saja tanpa perlu tombol kembali peramban.

---

## 4. Autentikasi & Manajemen Akun

Fitur autentikasi ditujukan bagi staf atau administrator yang ingin mengajukan aplikasi baru atau mengelola sistem.

- **Masuk Sistem (`/login`)**: Autentikasi aman berbasis email institusi dan enkripsi `password_hash` BCRYPT.
- **Auto-Timeout Sesi (30 Menit)**: Sesi otomatis dinonaktifkan jika pengguna tidak melakukan aktivitas selama 30 menit guna mencegah penyalahgunaan terminal bersama.
- **Profil & Keamanan (`/profile`)**:
  - Memperbarui nama lengkap dan unit kerja/kedinasan.
  - Mengubah kata sandi mandiri dengan verifikasi kata sandi lama.
- **Aplikasi Favorit (Bookmarks)**:
  - Klik ikon bintang pada kartu aplikasi untuk menyimpannya ke daftar favorit.
  - Akses cepat kumpulan aplikasi favorit melalui tombol filter **"Favorit Saya"** di katalog utama.
  - *Catatan*: Tamu publik yang menekan tombol favorit akan diarahkan untuk login terlebih dahulu.

---

## 5. Fitur Kontributor: Pengajuan & Manajemen Aplikasi

Staf dan pengembang internal dapat mendaftarkan skrip automasi buatan sendiri agar dapat didistribusikan ke seluruh jajaran Kelautan.ID.

### 5.1 Alur Pengajuan Modul Baru (`/submissions/new`)
1. Klik menu **"Ajukan Aplikasi"** pada navigasi atas.
2. Lengkapi formulir pendaftaran:
   - **Judul Aplikasi**: Nama resmi aplikasi mikro.
   - **Kategori**: Pilih kategori yang sesuai (otomatis menentukan slug URL).
   - **URL Aplikasi**: Tautan web eksekusi (URL Web App Google Apps Script `/exec` atau URL web app).
   - **Deskripsi**: Penjelasan fungsi, sasaran pengguna, dan instruksi ringkas.
   - **Pilihan Ikon**: Gunakan preset ikon FontAwesome/Material atau unggah file gambar kustom (PNG, JPG, SVG, WebP).
3. Klik **"Kirim Pengajuan Modul"**. Aplikasi akan masuk ke antrean peninjauan administrator (*Status: Pending*).

### 5.2 Manajemen & Riwayat Pengajuan Saya (`/submissions`)
- **Pelacakan Status Real-Time**:
  - `Draft`: Masih disimpan dalam bentuk draf.
  - `Pending`: Sedang dalam antrean evaluasi Tim IT / Administrator.
  - `Approved`: Telah lulus uji keamanan sandbox dan langsung terbit di katalog publik.
  - `Revision`: Memerlukan perbaikan berdasarkan catatan verifikator.
  - `Rejected`: Ditolak karena tidak memenuhi standar kepatuhan atau keamanan.
- **Pembaruan & Pengajuan Ulang (*Resubmit*)**:
  - Jika aplikasi mendapatkan status *Revision*, klik **"Perbaiki & Ajukan Ulang"**.
  - Catatan revisi dari administrator ditampilkan secara transparan di bagian atas formulir.
  - Pengaju dapat memperbaiki URL atau deskripsi lalu mengirimkan ulang ke antrean review.

---

## 6. Fitur Administrator & Panel Tata Kelola

Pengguna dengan peran **Admin** memiliki akses ke panel e-governance lengkap:

### 6.1 Review Board & Otorisasi Rilis (`/admin/reviews`)
- **Antrean Tiket**: Setiap pengajuan memiliki nomor tiket unik (contoh: `#REV-2026-001`).
- **Live Sandboxed Preview (FR 4.1)**:
  - Administrator dapat menguji dan menjalankan aplikasi pengaju secara langsung di dalam modal iFrame terisolasi sebelum memberikan keputusan.
- **Keputusan Tata Kelola**:
  - **Setujui (Approve)**: Aplikasi langsung berstatus *Approved* dan seketika muncul di katalog publik.
  - **Minta Revisi**: Membuka modal dialog untuk menulis instruksi perbaikan teknis yang akan langsung terbaca oleh pengaju.
  - **Tolak (Reject)**: Menolak pengajuan dengan alasan kebijakan atau pelanggaran sandboxing.

### 6.2 Manajemen Kategori (`/admin/categories`)
- Tambah, edit, dan hapus rumpun kategori aplikasi.
- Dilengkapi generator slug otomatis, pemilih kode warna HEX, dan pemilihan ikon.
- Dilengkapi counter otomatis berapa banyak aplikasi aktif di bawah kategori tersebut.

### 6.3 Manajemen Pengguna (`/admin/users`)
- Menambah pengguna baru secara manual.
- Mengubah peran pengguna (*Admin* atau *User*).
- Menangguhkan (*Suspend*) atau mengaktifkan kembali akun pengguna.
- Reset kata sandi pengguna secara langsung oleh admin.

### 6.4 Rekam Jejak Audit & Log Sistem (`/admin/logs`)
- Memantau audit trail seluruh aktivitas penting:
  - Waktu login dan logout pengguna.
  - Pengajuan aplikasi baru.
  - Keputusan evaluasi (Approve, Reject, Revision).
  - Pembuatan atau perubahan kategori.
  - Dilengkapi alamat IP address dan rincian metadata setiap aksi.

---

## 7. Simulasi Micro-App Interaktif (Mock Engine)

Untuk keperluan demonstrasi dan pengujian tanpa ketergantungan koneksi Google Apps Script eksternal, sistem dilengkapi dengan **Engine Mock Micro-App** terintegrasi di rute `/mock-app/[tipe]`.

Aplikasi demo yang tersedia:
1. `/mock-app/pos`: **Sistem Kasir Pelelangan Ikan (POS TPI)** — Simulasi transaksi timbangan dan cetak struk lelang.
2. `/mock-app/logbook`: **E-Logbook Penangkapan Ikan Nahkoda** — Simulasi formulir logbook WPP-NRI dan jenis tangkapan.
3. `/mock-app/cuaca`: **Dashboard Oseanografi BMKG (GAS)** — Simulasi data radar gelombang laut dan pasang surut.
4. `/mock-app/presensi`: **Sistem Presensi Awak Kapal (ABK)** — Simulasi absensi rotasi dermaga.
5. `/mock-app/pnbp`: **Kalkulator Simulasi Tarif PNBP** — Simulasi hitung pungutan hasil perikanan.
6. `/mock-app/spb`: **Formulir SPB Digital** — Pengajuan persetujuan berlayar daring.

*Seluruh mock app di atas mampu membaca dan menampilkan parameter token HMAC yang diteruskan oleh portal utama.*

---

## 8. Matriks Hak Akses (RBAC)

| Kemampuan / Fitur | Tamu Publik | User Internal | Administrator |
| :--- | :---: | :---: | :---: |
| Akses Katalog Publik | ✅ Ya | ✅ Ya | ✅ Ya |
| Jalankan Aplikasi *Approved* di iFrame | ✅ Ya | ✅ Ya | ✅ Ya |
| Pencarian & Filter Kategori | ✅ Ya | ✅ Ya | ✅ Ya |
| Simpan Aplikasi Favorit (Bookmark) | ❌ *Perlu Login* | ✅ Ya | ✅ Ya |
| Ajukan Aplikasi Mikro Baru | ❌ *Perlu Login* | ✅ Ya | ✅ Ya |
| Riwayat & Revisi Pengajuan Saya | ❌ *Perlu Login* | ✅ Ya | ✅ Ya |
| Ubah Profil & Kata Sandi Mandiri | ❌ *Perlu Login* | ✅ Ya | ✅ Ya |
| Panel Review Board & Approve/Reject | ❌ Tidak | ❌ Tidak | ✅ Ya |
| Kelola Kategori Aplikasi | ❌ Tidak | ❌ Tidak | ✅ Ya |
| Kelola Pengguna & Hak Akses | ❌ Tidak | ❌ Tidak | ✅ Ya |
| Akses Audit Trail & Log Keamanan | ❌ Tidak | ❌ Tidak | ✅ Ya |

---

> **Apps Hub Kelautan.ID** &bull; *Direkayasa untuk keandalan operasional, integrasi modular, dan kemudahan akses maritim.*

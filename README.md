# Apps Hub Kelautan.ID (Application Hub by Kelautan.ID)

Platform terpadu berbasis web berarsitektur *Single Page Application (SPA) wrapper* yang berfungsi sebagai hub pusat untuk menghosting, mengelola, mengamankan, dan mengakses seluruh aplikasi mikro internal (Google Apps Script / GAS dan Web SPA) milik Kelautan.ID.

Aplikasi ini dibangun sesuai spesifikasi **[Product Requirements Document (PRD)](apps-kelautan_PRD.md)** dan **[Pre-Coding Architecture Plan](pre_coding_checklist_rencana_arsitektur.md)**.

---

## 🌊 Fitur Utama Berdasarkan PRD

### 1. Autentikasi & Manajemen Akun (Fitur 1)
- **FR 1.1:** Sistem login aman dengan enkripsi `password_hash` BCRYPT dan proteksi serangan CSRF.
- **FR 1.2:** Manajemen sesi dengan *auto-timeout* (30 menit inaktivitas).
- **FR 1.3:** Halaman profil mandiri untuk pembaruan data pengguna dan penggantian kata sandi.

### 2. Katalog Aplikasi (Dashboard Utama) (Fitur 2)
- **FR 2.1:** Tata letak grid kartu modern bertema estetika kelautan maritim (Deep Oceanic Slate, Glassmorphism, Neon Cyan/Emerald).
- **FR 2.2:** Pencarian instan (*Search-as-you-type*) berdasarkan judul, modul, dan deskripsi aplikasi.
- **FR 2.3:** Filter kategori interaktif dengan badge jumlah aplikasi aktif.
- **FR 2.4:** Fitur bookmark / favorit aplikasi dengan animasi bintang dan umpan balik toast instan.
- **FR 2.5:** Widget "Terakhir Digunakan" (*Recently Used*) untuk mempercepat navigasi pengguna ke aplikasi favoritnya.

### 3. Pengajuan Aplikasi oleh Pengguna (Fitur 3)
- **FR 3.1:** Formulir pengajuan aplikasi mikro baru (Judul, Kategori, URL GAS/SPA, Deskripsi, Pemilih Ikon Preset / Unggah Ikon).
- **FR 3.2:** Dashboard pelacakan status aplikasi terpadu (*Draft, Pending, Approved, Rejected, Revision*).
- **FR 3.3:** Fitur perbaikan dan pengajuan ulang (*resubmit*) aplikasi yang ditolak atau membutuhkan revisi, lengkap dengan catatan evaluasi dari administrator.

### 4. Panel Admin & Tata Kelola (Fitur 4)
- **FR 4.1:** *Review Board* untuk memeriksa antrean aplikasi baru, dilengkapi tombol **Preview** langsung di dalam modal iFrame terisolasi (*sandbox*) sebelum disetujui.
- **FR 4.2:** Formulir penolakan (*Reject*) yang mewajibkan administrator mengisi alasan penolakan dan instruksi perbaikan (*Revision Request*).
- **FR 4.3:** CRUD Kategori Aplikasi terintegrasi dengan generator slug URL bersih.
- **FR 4.4:** Manajemen Pengguna (Tambah, Edit Peran Admin/User, Suspend/Aktifkan Akun, dan Reset Kata Sandi).

### 5. Engine iFrame & Eksekusi Aplikasi (Fitur 5)
- **FR 5.1:** *Clean Dynamic URL Routing* yang menerjemahkan `/[kategori]/[slug-aplikasi]` (contoh: `/pos/pos-tpi`, `/appscript/cuaca-maritim`) menjadi halaman pembungkus iFrame secara mulus tanpa berpindah domain.
- **FR 5.2:** *Header Wrapper* di atas iFrame dengan kontrol terpadu:
  - Tombol **Kembali ke Portal**
  - Ikon & Judul Aplikasi
  - Badge Kategori
  - Tombol **Layar Penuh (Full Screen API)**
  - Tombol **Muat Ulang (Refresh iFrame)**
  - Tombol **Salin Tautan Aplikasi**
  - Tombol **Buka di Tab Baru**
- **FR 5.3:** Meneruskan token autentikasi aman (*HMAC-SHA256 signature payload*) melalui parameter URL ke GAS/SPA (`?auth_token=...&user_id=...&user_name=...&user_email=...`) agar micro-app mengenali identitas pengguna aktif.
- **Simulasi Micro-App Interaktif:** Disertakan rute `/mock-app/[tipe]` untuk mendemonstrasikan eksekusi micro-app fungsional dengan penerimaan token autentikasi di dalam iFrame.

### 6. Analitik & Audit Trail (Fitur 6)
- **FR 6.1:** *Hit Counter* otomatis bertambah setiap kali sebuah aplikasi diluncurkan pengguna, dilengkapi logging detail IP dan User-Agent di tabel `app_hits`.
- **FR 6.2:** *System Log & Audit Trail* yang mencatat seluruh aksi login, evaluasi admin (Approve/Reject/Revision), CRUD kategori, dan aktivitas pengguna.

### 7. Keamanan & Non-Fungsional (NFR)
- **NFR 1:** Atribut *Sandboxing* ketat pada iFrame: `sandbox="allow-scripts allow-same-origin allow-forms allow-popups allow-downloads"`.
- **NFR 2:** Proteksi Anti-Clickjacking: Header `X-Frame-Options: SAMEORIGIN` diterapkan pada parent portal.
- **NFR 3:** Respon waktu routing ultra-cepat (< 50ms) dengan front controller ringan.
- **NFR 4:** Konfigurasi Apache `.htaccess` (`mod_rewrite`) untuk produksi dan `router.php` untuk server pengembangan lokal.

---

## 📂 Struktur Arsitektur Folder

```text
apps-kelautan/
├── config/
│   ├── auth.php              # Autentikasi, sesi timeout, CSRF, token HMAC iframe
│   ├── database.php          # Koneksi PDO (MariaDB / MySQL & SQLite auto-fallback)
│   └── helpers.php           # Helper URLs, sanitasi XSS, slugify, badges
├── controllers/
│   ├── AdminController.php   # Review board, CRUD kategori, pengguna, audit trail
│   ├── AppController.php     # Katalog utama, engine iframe, bookmark API, mock apps
│   ├── AuthController.php    # Login, logout, profil, ganti kata sandi
│   └── SubmissionController.php # Pengajuan aplikasi baru, tracking status, revisi
├── models/
│   ├── ActivityLog.php       # Model Audit Trail & log sistem
│   ├── Application.php       # Model aplikasi, filtering, hits, bookmarks, status
│   ├── Category.php          # Model kategori & relasi jumlah aplikasi
│   └── User.php              # Model pengguna, role, dan keamanan sandi
├── views/
│   ├── admin/
│   │   ├── categories.php    # Tampilan CRUD kategori
│   │   ├── logs.php          # Tampilan Audit Trail & analitik hits
│   │   ├── review_board.php  # Review Board dengan preview modal iframe
│   │   └── users.php         # Tampilan manajemen pengguna
│   ├── partials/
│   │   ├── header.php        # Navbar & global header
│   │   └── footer.php        # Footer & container toast
│   ├── 403.php               # Halaman error 403 Forbidden
│   ├── 404.php               # Halaman error 404 Not Found
│   ├── app_view.php          # Wrapper iFrame eksekusi aplikasi
│   ├── dashboard.php         # Katalog utama aplikasi
│   ├── edit_app.php          # Formulir revisi & pengajuan ulang
│   ├── login.php             # Halaman login estetika maritim
│   ├── mock_microapp.php     # Demo micro-app interaktif di dalam iFrame
│   ├── my_apps.php           # Dashboard status pengajuan aplikasi saya
│   ├── profile.php           # Profil akun & ganti sandi
│   └── submit_app.php        # Formulir pengajuan aplikasi baru
├── database/
│   ├── schema.sql            # Skrip DDL MariaDB / MySQL untuk produksi
│   └── database.sqlite       # Database lokal siap pakai (ter-migrate & seeded)
├── assets/
│   ├── css/style.css         # Styling oceanic slate & glassmorphism
│   └── js/main.js            # Live search, bookmark AJAX, fullscreen API, toast
├── uploads/                  # Direktori penyimpanan unggahan ikon aplikasi
├── .htaccess                 # Konfigurasi Apache mod_rewrite untuk produksi
├── index.php                 # Front Controller / Entry Point aplikasi
├── router.php                # Router skrip untuk server built-in PHP
└── apps-kelautan_PRD.md      # Dokumen PRD acuan
```

---

## 🚀 Cara Menjalankan Aplikasi

### 1. Menjalankan di Lokal (Pengembangan / Mac / Linux / Windows)

Cukup jalankan PHP built-in server dari direktori proyek:

```bash
php -S localhost:8000 router.php
```

Buka peramban di: **`http://localhost:8000`**

*Catatan: Sistem secara otomatis menggunakan SQLite dan telah diinisialisasi dengan data sampel lengkap (kategori, aplikasi teruji, log, dan pengguna demo).*

---

### 2. Hak Akses & Akun Demo

- **Akses Publik (Tanpa Login)**: Seluruh katalog aplikasi publik dan semua aplikasi mikro yang berstatus **Approved** dapat langsung diakses dan dijalankan oleh siapa saja tanpa perlu masuk/login.
- **Login Diperlukan Untuk**: Pengajuan aplikasi baru (`/submissions`), penandaan aplikasi favorit, dan fitur tata kelola sistem.

| Peran (Role) | Alamat Email | Kata Sandi | Akses |
| :--- | :--- | :--- | :--- |
| **Administrator** | `admin@kelautan.id` | `admin123` | Akses penuh (Katalog, Review Board, Pengguna, Kategori, Log Audit) |
| **User Internal** | `user@kelautan.id` | `user123` | Akses terdaftar (Katalog, Favorit Pribadi, Pengajuan Aplikasi Saya) |

---

### 3. Deployment ke Ubuntu 22.04 LTS / Apache2 / MariaDB

1. **Clone/Salin direktori proyek** ke `/var/www/html/apps-kelautan`.
2. **Impor Skrip Database MariaDB**:
   ```bash
   mysql -u root -p < database/schema.sql
   ```
3. **Konfigurasi Environment Variable** (pada Apache VirtualHost atau `.htaccess`):
   ```apache
   SetEnv DB_CONNECTION mysql
   SetEnv DB_HOST 127.0.0.1
   SetEnv DB_DATABASE apps_kelautan
   SetEnv DB_USERNAME root
   SetEnv DB_PASSWORD password_rahasia_anda
   ```
4. **Aktifkan Modul Apache Rewrite & Headers**:
   ```bash
   sudo a2enmod rewrite headers
   sudo systemctl restart apache2
   ```
5. **Atur Hak Akses Folder Unggahan**:
   ```bash
   sudo chown -R www-data:www-data /var/www/html/apps-kelautan/uploads
   sudo chmod -R 775 /var/www/html/apps-kelautan/uploads
   ```

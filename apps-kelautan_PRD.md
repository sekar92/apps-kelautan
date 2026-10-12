# PRODUCT REQUIREMENTS DOCUMENT (PRD)

**Nama Produk:** Portal Hub Aplikasi (Apps Portal Kelautan)
**Versi Dokumen:** 1.0
**Platform:** Web Application
**Tech Stack:** PHP 8.5, MariaDB, Apache2, Ubuntu 22.04.5 LTS

---

## 1. Executive Summary (Ringkasan Eksekutif)
**Portal Hub Aplikasi** adalah sebuah platform berbasis web berarsitektur *Single Page Application (SPA) wrapper* yang berfungsi sebagai pusat (hub) untuk menghosting, mengelola, dan mengakses berbagai aplikasi mikro internal (terutama Google Apps Script / GAS dan SPA berbasis TypeScript/JavaScript). Sistem ini menggunakan teknologi `iframe` dengan URL routing yang bersih (contoh: `apps.kelautan.id/appscript/POS`) untuk memberikan pengalaman pengguna yang terpadu tanpa perlu berpindah-pindah domain, dilengkapi dengan sistem otorisasi dan tata kelola terpusat.

## 2. Goals & Objectives (Tujuan Bisnis & Sistem)
*   **Sentralisasi:** Menyediakan satu pintu masuk (Single Portal) untuk semua aplikasi operasional (keuangan, HR, POS, dll).
*   **Efisiensi Deployment:** Memudahkan tim untuk mempublikasikan aplikasi GAS tanpa harus mengurus hosting mandiri.
*   **Tata Kelola & Keamanan (Governance):** Memastikan setiap aplikasi pihak ketiga/GAS yang masuk ke portal telah melewati proses *review* (kode dan keamanan) oleh Admin sebelum bisa diakses publik/karyawan.
*   **Visibilitas:** Memonitor aplikasi mana yang paling sering digunakan dan melacak aktivitas pengguna.

## 3. Target User Personas (Target Pengguna)
1.  **User / Pengguna Biasa:**
    *   *Sebagai Konsumen:* Mengakses portal untuk mencari dan menggunakan aplikasi operasional sehari-hari.
    *   *Sebagai Kontributor:* Membuat aplikasi GAS mandiri dan mendaftarkannya ke portal untuk digunakan oleh tim lain.
2.  **Administrator (Tim IT / Manajemen):**
    *   Meninjau (Approve/Reject) aplikasi yang diajukan oleh user.
    *   Mengelola hak akses pengguna, kategori aplikasi, dan memantau analitik portal.

---

## 4. Kebutuhan Fungsional (Functional Requirements)

### Fitur 1: Autentikasi & Manajemen Akun
*   **FR 1.1:** Sistem login menggunakan email dan password (terenkripsi *password_hash* PHP).
*   **FR 1.2:** Manajemen sesi (session timeout) otomatis jika pengguna tidak aktif.
*   **FR 1.3:** Halaman profil bagi pengguna untuk mengubah password dan informasi dasar.

### Fitur 2: Katalog Aplikasi (Dashboard Utama)
*   **FR 2.1:** Menampilkan *grid layout* aplikasi berstatus 'Approved'.
*   **FR 2.2:** Fitur pencarian (*Search*) berdasarkan nama aplikasi.
*   **FR 2.3:** Filter berdasarkan kategori aplikasi (GAS, TypeScript, Laporan, dll).
*   **FR 2.4:** Fitur "Bookmark" agar user dapat menyimpan aplikasi favoritnya.
*   **FR 2.5:** Menampilkan *widget* "Terakhir Digunakan" (Recently Used).

### Fitur 3: Pengajuan Aplikasi (User Portal)
*   **FR 3.1:** Form input aplikasi (Judul, Kategori, URL GAS/SPA, Deskripsi, Upload Ikon).
*   **FR 3.2:** Dashboard pelacakan status aplikasi (*Draft, Pending, Approved, Rejected, Revision*).
*   **FR 3.3:** Fitur perbaikan/resubmit jika aplikasi ditolak beserta catatan dari admin.

### Fitur 4: Panel Admin & Tata Kelola
*   **FR 4.1:** *Review Board* untuk meninjau aplikasi baru. Terdapat tombol "Preview" untuk melihat aplikasi dalam iframe sebelum di-approve.
*   **FR 4.2:** Form penolakan (*Reject*) yang mewajibkan admin mengisi alasan penolakan.
*   **FR 4.3:** CRUD (Create, Read, Update, Delete) Kategori Aplikasi.
*   **FR 4.4:** Manajemen Pengguna (Tambah, Edit Role, Reset Password, Suspend Akun).

### Fitur 5: Engine iFrame & Eksekusi Aplikasi
*   **FR 5.1:** *Dynamic Routing* yang menerjemahkan URL `subdomain.com/[kategori]/[slug-aplikasi]` menjadi halaman pembungkus iFrame.
*   **FR 5.2:** *Header Wrapper* di atas iFrame yang menampilkan: Nama Aplikasi, Tombol "Full Screen", dan Tombol "Kembali".
*   **FR 5.3:** Meneruskan token autentikasi (JWT atau *Secure Hash*) melalui URL parameter ke GAS agar GAS mengenali user yang sedang login.

### Fitur 6: Analitik & Log (Audit Trail)
*   **FR 6.1:** *Hit Counter* yang bertambah setiap kali sebuah aplikasi diklik/dibuka.
*   **FR 6.2:** *System Log* yang mencatat aktivitas CRUD admin dan histori login pengguna.

---

## 5. Kebutuhan Non-Fungsional (Non-Functional Requirements)
*   **NFR 1 (Security - iFrame Sandboxing):** Iframe harus diimplementasikan dengan atribut `sandbox="allow-scripts allow-same-origin allow-forms allow-popups"` untuk mencegah aplikasi eksternal (GAS) memanipulasi *parent DOM* (Portal).
*   **NFR 2 (Security - X-Frame-Options):** Aplikasi tidak mengizinkan dirinya sendiri di-embed oleh web lain (anti-clickjacking).
*   **NFR 3 (Performance):** Respon waktu *routing* PHP tidak boleh lebih dari 500ms sebelum iFrame mulai dimuat.
*   **NFR 4 (Routing):** Wajib menggunakan konfigurasi Apache `.htaccess` (`mod_rewrite`) untuk memastikan *Clean URL* berjalan sempurna.

---

## 6. Alur Pengguna (User Flow) Utama
**Skenario: User Mendaftarkan Aplikasi GAS hingga Digunakan**
1. User login ke Portal.
2. Navigasi ke menu "Ajukan Aplikasi Baru".
3. Mengisi detail (Nama: POS, URL: `https://script.google.com/...`, Kategori: GAS).
4. Status aplikasi = *Pending*.
5. Admin menerima notifikasi/melihat di dasbor, lalu menekan tombol "Preview".
6. Admin menguji keamanan/fungsi di dalam iframe. Admin menekan "Approve".
7. Aplikasi muncul di Halaman Utama Portal.
8. User lain mengklik ikon POS.
9. URL berubah menjadi `https://apps.kelautan.id/appscript/POS`. Portal merender iFrame berisi GAS tersebut.

---

## 7. Fase Pengembangan (Milestones)

| Fase | Deskripsi Tugas | Estimasi Waktu |
| :--- | :--- | :--- |
| **Fase 1: Fondasi & Backend** | Setup DB MariaDB, konfigurasi Apache `.htaccess`, arsitektur MVC/Front Controller PHP, Sistem Login & Sesi. | Minggu 1 |
| **Fase 2: UI/UX & Katalog** | Desain halaman login, Dashboard User, layout Grid Aplikasi, Search & Filter. | Minggu 2 |
| **Fase 3: Core Engine iFrame** | Halaman eksekusi iFrame, integrasi URL routing, Header kontrol iFrame (Fullscreen), Passing Token Auth. | Minggu 3 |
| **Fase 4: Admin & Workflow** | Panel Admin, sistem Review (Approve/Reject), manajemen user & kategori. | Minggu 4 |
| **Fase 5: Testing & Rilis** | Penetration testing (khusus isolasi iFrame), perbaikan bug, peluncuran ke *production* Ubuntu Server. | Minggu 5 |
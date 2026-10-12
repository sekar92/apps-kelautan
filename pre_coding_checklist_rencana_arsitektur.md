# PRE-CODING CHECKLIST (Portal Hub Aplikasi)

Sebelum mulai menulis baris kode pertama di IDE, pastikan 5 hal ini sudah Anda putuskan dan siapkan:

## 1. Finalisasi 'Senjata' Front-End (UI/UX)
Karena kita menggunakan PHP murni tanpa framework besar seperti Laravel, Anda perlu memutuskan bagaimana UI akan dibangun agar prosesnya cepat:
*   **CSS Framework:** Apakah akan menggunakan **Tailwind CSS** (via CDN untuk awalan/rapid prototyping) atau **Bootstrap 5**? (Rekomendasi: Tailwind CSS karena sangat cocok untuk desain modern bergaya SPA).
*   **JavaScript:** Apakah butuh framework reaktif ringan seperti **Alpine.js** atau cukup Vanilla JS untuk interaksi modal dan dropdown?

## 2. Rancang Struktur Folder (Architecture Plan)
Agar kode tidak berantakan (spaghetti code), kita akan menggunakan pola **MVC Sederhana (Model-View-Controller)** dengan **Front Controller** (`index.php`). Siapkan bayangan struktur folder ini:

```text
/public_html            --> (Root Document Apache)
  ├── /assets           --> (CSS, JS, Images, Logo)
  ├── /config           --> (database.php, init.php)
  ├── /controllers      --> (AuthController.php, AppController.php, AdminController.php)
  ├── /models           --> (User.php, Application.php, Category.php)
  ├── /views            --> (login.php, dashboard.php, admin_board.php, iframe_layout.php)
  ├── .htaccess         --> (Routing rewrite rules)
  └── index.php         --> (Front Controller / Entry Point)
```

## 3. Siapkan Skrip Database (SQL Schema)
Sebelum membuat koneksi PHP, database dan tabel-tabelnya harus sudah ada. Anda butuh skrip SQL utuh (DDL) untuk di- *copy-paste* ke phpMyAdmin atau *command line* MariaDB Anda. Ini mencakup relasi tabel (`users`, `categories`, `applications`, `logs`).

## 4. Pastikan Environment Server/Lokal Siap
Jika Anda *coding* langsung di Ubuntu Server atau menggunakan *localhost* (seperti Laragon/XAMPP):
*   Pastikan PHP 8.5 dan ekstensi PDO MySQL aktif.
*   Pastikan `mod_rewrite` di Apache sudah diaktifkan (`sudo a2enmod rewrite`).
*   Pastikan direktori project memiliki *permission* yang benar agar bisa membaca dan menulis file jika ada fitur upload ikon aplikasi.

## 5. Inisialisasi Git Repository
Jangan lupakan *version control*. Sebelum mulai, selalu inisialisasi Git:
```bash
git init
git add .
git commit -m "Initial commit: Setup folder structure and PRD"
```
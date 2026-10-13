-- ==========================================================
-- Portal Hub Aplikasi (Apps Portal Kelautan)
-- MariaDB / MySQL Schema Definition (Ubuntu 22.04 LTS / Apache2)
-- ==========================================================

CREATE DATABASE IF NOT EXISTS `apps_kelautan` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `apps_kelautan`;

-- 1. Users Table
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(150) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `role` ENUM('admin', 'user') NOT NULL DEFAULT 'user',
    `department` VARCHAR(100) DEFAULT 'Kelautan & Perikanan',
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Categories Table
CREATE TABLE IF NOT EXISTS `categories` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `slug` VARCHAR(100) NOT NULL UNIQUE,
    `description` TEXT NULL,
    `icon` VARCHAR(50) DEFAULT 'folder',
    `color` VARCHAR(20) DEFAULT '#0ea5e9',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Applications Table
CREATE TABLE IF NOT EXISTS `applications` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(150) NOT NULL,
    `slug` VARCHAR(150) NOT NULL,
    `category_id` INT NOT NULL,
    `user_id` INT NOT NULL,
    `app_url` TEXT NOT NULL,
    `description` TEXT NULL,
    `icon_type` ENUM('preset', 'upload') DEFAULT 'preset',
    `icon_value` VARCHAR(255) DEFAULT 'fa-cube',
    `status` ENUM('draft', 'pending', 'approved', 'rejected', 'revision') NOT NULL DEFAULT 'pending',
    `review_notes` TEXT NULL,
    `reviewed_by` INT NULL,
    `reviewed_at` DATETIME NULL,
    `hit_count` INT NOT NULL DEFAULT 0,
    `is_featured` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_app_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_app_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Bookmarks Table
CREATE TABLE IF NOT EXISTS `bookmarks` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `application_id` INT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `unique_user_app_bookmark` (`user_id`, `application_id`),
    CONSTRAINT `fk_bookmark_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_bookmark_app` FOREIGN KEY (`application_id`) REFERENCES `applications` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Application Access Hits Table (Analytics)
CREATE TABLE IF NOT EXISTS `app_hits` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `application_id` INT NOT NULL,
    `user_id` INT NULL,
    `ip_address` VARCHAR(45) NULL,
    `user_agent` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_hits_app` FOREIGN KEY (`application_id`) REFERENCES `applications` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Activity & Audit Logs Table
CREATE TABLE IF NOT EXISTS `activity_logs` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NULL,
    `action` VARCHAR(100) NOT NULL,
    `details` TEXT NULL,
    `ip_address` VARCHAR(45) NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Seed Data (Admin: admin@kelautan.id / admin123, User: user@kelautan.id / user123)
INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `department`) VALUES
(1, 'Administrator Kelautan', 'admin@kelautan.id', '$2y$12$oCzisiXlFDNa5stQGlM0u.HP72MG0cA4YiQJmtleVxUvyKJ/pd7/u', 'admin', 'Pusat Data dan Informasi Kelautan.ID'),
(2, 'Budi Samudra (User)', 'user@kelautan.id', '$2y$12$T.Zbp9HQzdeynGVwI/ome..mhXgZdv86l5G/MdPfhIrtFOiEbB5RG', 'user', 'Pelabuhan Perikanan Samudera'),
(3, 'Siti Bahari (Analis)', 'siti@kelautan.id', '$2y$12$T.Zbp9HQzdeynGVwI/ome..mhXgZdv86l5G/MdPfhIrtFOiEbB5RG', 'user', 'Direktorat Pengawasan Maritim')
ON DUPLICATE KEY UPDATE `password`=VALUES(`password`), `role`=VALUES(`role`), `name`=VALUES(`name`);

-- 7. Categories Seed
INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `icon`, `color`) VALUES
(1, 'Google Apps Script (GAS)', 'appscript', 'Aplikasi automasi spreadsheet, formulir, dan ekosistem Google Workspace', 'fa-brands fa-google', '#3b82f6'),
(2, 'TypeScript & SPA', 'typescript', 'Aplikasi Web Single Page Application dengan antarmuka dinamis dan responsif', 'fa-solid fa-code', '#06b6d4'),
(3, 'Logistik & POS Nelayan', 'pos', 'Sistem kasir lelang, inventori pasokan bahan bakar, dan logistik pelabuhan', 'fa-solid fa-cash-register', '#10b981'),
(4, 'Operasional & ABK', 'operasional', 'Aplikasi pencatatan operasional kapal, presensi ABK, dan izin berlayar', 'fa-solid fa-ship', '#8b5cf6'),
(5, 'Keuangan & PNBP', 'keuangan', 'Kalkulator penerimaan negara bukan pajak dan laporan keuangan lelang', 'fa-solid fa-coins', '#f59e0b'),
(6, 'Laporan & Oseanografi', 'laporan', 'Dashboard pemantauan cuaca laut, gelombang, dan data citra satelit', 'fa-solid fa-chart-line', '#ec4899')
ON DUPLICATE KEY UPDATE `slug`=`slug`;

-- 8. Applications Seed
INSERT INTO `applications` (`id`, `title`, `slug`, `category_id`, `user_id`, `app_url`, `description`, `icon_type`, `icon_value`, `status`, `review_notes`, `reviewed_by`, `reviewed_at`, `hit_count`, `is_featured`) VALUES
(1, 'POS Kasir Lelang TPI', 'pos-tpi', 3, 1, 'https://example.com/demo-app-pos', 'Aplikasi kasir penjualan hasil tangkapan ikan di Tempat Pelelangan Ikan (TPI) terintegrasi dengan timbangan digital dan struk otomatis.', 'preset', 'fa-solid fa-cash-register', 'approved', 'Aplikasi telah memenuhi standar keamanan dan siap digunakan.', 1, NOW() - INTERVAL 10 DAY, 542, 1),
(2, 'E-Logbook Penangkapan Ikan', 'e-logbook', 4, 2, 'https://example.com/demo-app-logbook', 'Pencatatan digital hasil tangkapan harian nahkoda: jenis ikan, perkiraan bobot, dan koordinat WPP-NRI sesuai regulasi Kelautan.ID.', 'preset', 'fa-solid fa-book-bookmark', 'approved', 'Sesuai dengan format pelaporan terpadu.', 1, NOW() - INTERVAL 5 DAY, 839, 1),
(3, 'Dashboard Oseanografi BMKG (GAS)', 'cuaca-maritim', 1, 2, 'https://example.com/demo-app-cuaca', 'Google Apps Script yang mengagregasi data radar tinggi gelombang, kecepatan angin maritim, dan pasang surut air laut untuk keselamatan pelayaran.', 'preset', 'fa-solid fa-water', 'approved', 'Script aman, isolasi iframe berjalan dengan baik.', 1, NOW() - INTERVAL 2 DAY, 1240, 1),
(4, 'Sistem Presensi Awak Kapal (ABK)', 'presensi-abk', 4, 1, 'https://example.com/demo-app-presensi', 'Sistem monitoring kehadiran dan rotasi kru kapal perikanan dengan validasi lokasi dermaga pelabuhan singgah.', 'preset', 'fa-solid fa-id-card-clip', 'approved', 'Disetujui untuk pilot project pelabuhan perikanan.', 1, NOW() - INTERVAL 1 DAY, 312, 0),
(5, 'Kalkulator Simulasi PNBP Pascaproduksi', 'kalkulator-pnbp', 5, 2, 'https://example.com/demo-app-pnbp', 'Simulasi mandiri untuk pelaku usaha perikanan menghitung tarif PNBP berdasarkan tonase kapal dan harga patokan ikan terbaru.', 'preset', 'fa-solid fa-calculator', 'approved', 'Formula perhitungan telah diverifikasi oleh tim keuangan.', 1, NOW() - INTERVAL 4 DAY, 428, 0),
(6, 'Formulir Izin Berlayar Cepat (SPB)', 'spb-cepat', 2, 3, 'https://example.com/demo-app-spb', 'Aplikasi SPA TypeScript untuk pengajuan Surat Persetujuan Berlayar secara digital dengan verifikasi berkas daring.', 'preset', 'fa-solid fa-compass', 'pending', NULL, NULL, NULL, 14, 0),
(7, 'Inventarisasi Alat Tangkap Ramah Lingkungan', 'inventaris-alat-tangkap', 6, 2, 'https://example.com/demo-app-alat-tangkap', 'Katalog dan inventaris alat penangkapan ikan (API) yang tersertifikasi ramah lingkungan di wilayah pesisir.', 'preset', 'fa-solid fa-network-wired', 'revision', 'Mohon perbarui URL formulir dan sertakan petunjuk hak akses.', 1, NOW() - INTERVAL 3 HOUR, 6, 0),
(8, 'Sistem Tracking Suhu Cold Storage (Draft)', 'tracking-cold-storage', 2, 2, 'https://example.com/demo-app-cold-storage', 'Monitoring sensor suhu gudang beku ikan secara real-time melalui websocket IoT.', 'preset', 'fa-solid fa-temperature-arrow-down', 'draft', NULL, NULL, NULL, 0, 0)
ON DUPLICATE KEY UPDATE `slug`=`slug`;

-- 9. Sample Bookmarks
INSERT INTO `bookmarks` (`user_id`, `application_id`) VALUES
(2, 1), (2, 3), (1, 2)
ON DUPLICATE KEY UPDATE `application_id`=`application_id`;

-- 10. Sample Activity Logs
INSERT INTO `activity_logs` (`user_id`, `action`, `details`, `ip_address`) VALUES 
(1, 'SYSTEM_INIT', 'Sistem Apps Hub Kelautan.ID diinisialisasi.', '127.0.0.1'),
(1, 'APP_APPROVE', 'Menyetujui aplikasi POS Kasir Lelang TPI', '127.0.0.1'),
(2, 'APP_SUBMIT', 'Mengajukan aplikasi baru: Dashboard Oseanografi BMKG', '127.0.0.1'),
(1, 'APP_REVISION', 'Meminta revisi aplikasi: Inventarisasi Alat Tangkap Ramah Lingkungan', '127.0.0.1');

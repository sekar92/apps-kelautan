<?php
/**
 * Database Connection & Migration Helper
 * Supports MariaDB / MySQL with zero-config SQLite auto-fallback.
 */

class Database {
    private static ?PDO $instance = null;
    private static string $driver = 'sqlite';

    public static function getConnection(): PDO {
        if (self::$instance !== null) {
            return self::$instance;
        }

        // Determine driver based on environment or configuration
        $envDriver = getenv('DB_CONNECTION') ?: 'sqlite';
        $dbHost = getenv('DB_HOST') ?: '127.0.0.1';
        $dbPort = getenv('DB_PORT') ?: '3306';
        $dbName = getenv('DB_DATABASE') ?: 'apps_kelautan';
        $dbUser = getenv('DB_USERNAME') ?: 'root';
        $dbPass = getenv('DB_PASSWORD') ?: '';

        if ($envDriver === 'mysql') {
            try {
                $dsn = "mysql:host={$dbHost};port={$dbPort};dbname={$dbName};charset=utf8mb4";
                $pdo = new PDO($dsn, $dbUser, $dbPass, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]);
                self::$instance = $pdo;
                self::$driver = 'mysql';
                return self::$instance;
            } catch (PDOException $e) {
                // If MySQL connection fails, fall back gracefully to SQLite
                error_log("MySQL connection failed: " . $e->getMessage() . ". Falling back to SQLite.");
            }
        }

        // SQLite Initialization
        $sqlitePath = dirname(__DIR__) . '/database/database.sqlite';
        $isNewDb = !file_exists($sqlitePath);
        
        $pdo = new PDO("sqlite:" . $sqlitePath, null, null, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        
        // Enable foreign keys in SQLite
        $pdo->exec("PRAGMA foreign_keys = ON;");
        self::$instance = $pdo;
        self::$driver = 'sqlite';

        // Auto-run schema & seeds if database is newly created or empty
        self::initSqliteTables($pdo);

        return self::$instance;
    }

    public static function getDriver(): string {
        return self::$driver;
    }

    private static function initSqliteTables(PDO $pdo): void {
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                email TEXT NOT NULL UNIQUE,
                password TEXT NOT NULL,
                role TEXT NOT NULL DEFAULT 'user',
                department TEXT DEFAULT 'Kelautan & Perikanan',
                is_active INTEGER NOT NULL DEFAULT 1,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );

            CREATE TABLE IF NOT EXISTS categories (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                slug TEXT NOT NULL UNIQUE,
                description TEXT,
                icon TEXT DEFAULT 'fa-folder',
                color TEXT DEFAULT '#0ea5e9',
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );

            CREATE TABLE IF NOT EXISTS applications (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                title TEXT NOT NULL,
                slug TEXT NOT NULL,
                category_id INTEGER NOT NULL,
                user_id INTEGER NOT NULL,
                app_url TEXT NOT NULL,
                description TEXT,
                icon_type TEXT DEFAULT 'preset',
                icon_value TEXT DEFAULT 'fa-cube',
                status TEXT NOT NULL DEFAULT 'pending',
                review_notes TEXT,
                reviewed_by INTEGER,
                reviewed_at DATETIME,
                hit_count INTEGER NOT NULL DEFAULT 0,
                is_featured INTEGER NOT NULL DEFAULT 0,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            );

            CREATE TABLE IF NOT EXISTS bookmarks (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER NOT NULL,
                application_id INTEGER NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                UNIQUE(user_id, application_id),
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                FOREIGN KEY (application_id) REFERENCES applications(id) ON DELETE CASCADE
            );

            CREATE TABLE IF NOT EXISTS app_hits (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                application_id INTEGER NOT NULL,
                user_id INTEGER,
                ip_address TEXT,
                user_agent TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (application_id) REFERENCES applications(id) ON DELETE CASCADE
            );

            CREATE TABLE IF NOT EXISTS activity_logs (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER,
                action TEXT NOT NULL,
                details TEXT,
                ip_address TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );
        ");

        // Check if users table is empty, if so, seed demo data
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
        $userCount = $stmt->fetch()['count'] ?? 0;

        if ($userCount == 0) {
            self::seedData($pdo);
        }
    }

    public static function seedData(PDO $pdo): void {
        // Default passwords:
        // admin@kelautan.id -> admin123
        // user@kelautan.id  -> user123
        $adminPass = password_hash('admin123', PASSWORD_BCRYPT);
        $userPass = password_hash('user123', PASSWORD_BCRYPT);

        // 1. Insert Users
        $insertUser = $pdo->prepare("INSERT INTO users (name, email, password, role, department) VALUES (?, ?, ?, ?, ?)");
        $insertUser->execute(['Administrator Kelautan', 'admin@kelautan.id', $adminPass, 'admin', 'Pusat Data dan Informasi Kelautan']);
        $insertUser->execute(['Budi Samudra (User)', 'user@kelautan.id', $userPass, 'user', 'Pelabuhan Perikanan Samudera']);
        $insertUser->execute(['Siti Bahari (Analis)', 'siti@kelautan.id', $userPass, 'user', 'Ditjen Pengawasan SDKP']);

        // 2. Insert Categories
        $categories = [
            ['Google Apps Script (GAS)', 'appscript', 'Aplikasi automasi spreadsheet, formulir, dan ekosistem Google Workspace', 'fa-brands fa-google', '#3b82f6'],
            ['TypeScript & SPA', 'typescript', 'Aplikasi Web Single Page Application dengan antarmuka dinamis dan responsif', 'fa-solid fa-code', '#06b6d4'],
            ['Logistik & POS Nelayan', 'pos', 'Sistem kasir lelang, inventori pasokan bahan bakar, dan logistik pelabuhan', 'fa-solid fa-cash-register', '#10b981'],
            ['Operasional & ABK', 'operasional', 'Aplikasi pencatatan operasional kapal, presensi ABK, dan izin berlayar', 'fa-solid fa-ship', '#8b5cf6'],
            ['Keuangan & PNBP', 'keuangan', 'Kalkulator penerimaan negara bukan pajak dan laporan keuangan lelang', 'fa-solid fa-coins', '#f59e0b'],
            ['Laporan & Oseanografi', 'laporan', 'Dashboard pemantauan cuaca laut, gelombang, dan data citra satelit', 'fa-solid fa-chart-line', '#ec4899']
        ];

        $insertCat = $pdo->prepare("INSERT INTO categories (name, slug, description, icon, color) VALUES (?, ?, ?, ?, ?)");
        foreach ($categories as $cat) {
            $insertCat->execute($cat);
        }

        // 3. Insert Sample Applications
        $apps = [
            [
                'POS Kasir Lelang TPI',
                'pos-tpi',
                3, // pos
                1,
                'https://example.com/demo-app-pos',
                'Aplikasi kasir penjualan hasil tangkapan ikan di Tempat Pelelangan Ikan (TPI) terintegrasi dengan timbangan digital dan struk otomatis.',
                'preset',
                'fa-solid fa-cash-register',
                'approved',
                'Aplikasi telah memenuhi standar keamanan perbankan daerah dan siap digunakan.',
                1,
                date('Y-m-d H:i:s', strtotime('-10 days')),
                542,
                1
            ],
            [
                'E-Logbook Penangkapan Ikan',
                'e-logbook',
                4, // operasional
                2,
                'https://example.com/demo-app-logbook',
                'Pencatatan digital hasil tangkapan harian nahkoda: jenis ikan, perkiraan bobot, dan koordinat WPP-NRI sesuai regulasi KKP.',
                'preset',
                'fa-solid fa-book-bookmark',
                'approved',
                'Sesuai dengan format pelaporan Direktorat Pengelolaan Sumber Daya Ikan.',
                1,
                date('Y-m-d H:i:s', strtotime('-5 days')),
                839,
                1
            ],
            [
                'Dashboard Oseanografi BMKG (GAS)',
                'cuaca-maritim',
                1, // appscript
                2,
                'https://example.com/demo-app-cuaca',
                'Google Apps Script yang mengagregasi data radar tinggi gelombang, kecepatan angin maritim, dan pasang surut air laut untuk keselamatan pelayaran.',
                'preset',
                'fa-solid fa-water',
                'approved',
                'Script aman, isolasi iframe berjalan dengan baik.',
                1,
                date('Y-m-d H:i:s', strtotime('-2 days')),
                1240,
                1
            ],
            [
                'Sistem Presensi Awak Kapal (ABK)',
                'presensi-abk',
                4, // operasional
                1,
                'https://example.com/demo-app-presensi',
                'Sistem monitoring kehadiran dan rotasi kru kapal perikanan dengan validasi lokasi dermaga pelabuhan singgah.',
                'preset',
                'fa-solid fa-id-card-clip',
                'approved',
                'Disetujui untuk pilot project di PPS Nizam Zachman Jakarta.',
                1,
                date('Y-m-d H:i:s', strtotime('-1 day')),
                312,
                0
            ],
            [
                'Kalkulator Simulasi PNBP Pascaproduksi',
                'kalkulator-pnbp',
                5, // keuangan
                2,
                'https://example.com/demo-app-pnbp',
                'Simulasi mandiri untuk pelaku usaha perikanan menghitung tarif PNBP berdasarkan tonase kapal dan harga patokan ikan terbaru.',
                'preset',
                'fa-solid fa-calculator',
                'approved',
                'Formula perhitungan telah diverifikasi oleh tim keuangan pelabuhan.',
                1,
                date('Y-m-d H:i:s', strtotime('-4 days')),
                428,
                0
            ],
            [
                'Formulir Izin Berlayar Cepat (SPB)',
                'spb-cepat',
                2, // typescript
                3,
                'https://example.com/demo-app-spb',
                'Aplikasi SPA TypeScript untuk pengajuan Surat Persetujuan Berlayar secara digital dengan verifikasi berkas daring.',
                'preset',
                'fa-solid fa-compass',
                'pending',
                null,
                null,
                null,
                14,
                0
            ],
            [
                'Inventarisasi Alat Tangkap Ramah Lingkungan',
                'inventaris-alat-tangkap',
                6, // laporan
                2,
                'https://example.com/demo-app-alat-tangkap',
                'Katalog dan inventaris alat penangkapan ikan (API) yang tersertifikasi ramah lingkungan di wilayah pesisir.',
                'preset',
                'fa-solid fa-network-wired',
                'revision',
                'Mohon perbarui URL formulir dan sertakan petunjuk hak akses spreadsheet untuk verifikator pelabuhan.',
                1,
                date('Y-m-d H:i:s', strtotime('-3 hours')),
                6,
                0
            ],
            [
                'Sistem Tracking Suhu Cold Storage (Draft)',
                'tracking-cold-storage',
                2, // typescript
                2,
                'https://example.com/demo-app-cold-storage',
                'Monitoring sensor suhu gudang beku ikan secara real-time melalui websocket IoT.',
                'preset',
                'fa-solid fa-temperature-arrow-down',
                'draft',
                null,
                null,
                null,
                0,
                0
            ]
        ];

        $insertApp = $pdo->prepare("
            INSERT INTO applications (
                title, slug, category_id, user_id, app_url, description,
                icon_type, icon_value, status, review_notes, reviewed_by,
                reviewed_at, hit_count, is_featured
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        foreach ($apps as $app) {
            $insertApp->execute($app);
        }

        // 4. Sample Bookmarks for user 2 (Budi Nelayan)
        $pdo->exec("INSERT OR IGNORE INTO bookmarks (user_id, application_id) VALUES (2, 1), (2, 3), (1, 2)");

        // 5. Sample Activity Logs
        $pdo->exec("
            INSERT INTO activity_logs (user_id, action, details, ip_address) VALUES 
            (1, 'SYSTEM_INIT', 'Sistem Portal Hub Aplikasi diinisialisasi.', '127.0.0.1'),
            (1, 'APP_APPROVE', 'Menyetujui aplikasi POS Kasir Lelang TPI', '127.0.0.1'),
            (2, 'APP_SUBMIT', 'Mengajukan aplikasi baru: Dashboard Oseanografi BMKG', '127.0.0.1'),
            (1, 'APP_REVISION', 'Meminta revisi aplikasi: Inventarisasi Alat Tangkap Ramah Lingkungan', '127.0.0.1')
        ");
    }
}

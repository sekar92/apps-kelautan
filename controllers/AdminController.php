<?php
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../models/Application.php';
require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/ActivityLog.php';

class AdminController {
    public function __construct() {
        Auth::requireAdmin();
    }

    /**
     * FR 4.1: Review Board
     */
    public function reviewBoard(): void {
        $filter = $_GET['filter'] ?? 'pending';
        $apps = Application::getAllAdmin($filter === 'all' ? null : $filter);
        $stats = Application::getStats();
        $pageTitle = "Review Board & Tata Kelola - Admin Portal Kelautan";
        require __DIR__ . '/../views/admin/review_board.php';
    }

    public function approve(int $id): void {
        if (!Auth::verifyCsrf($_POST['csrf_token'] ?? '')) {
            redirect('/admin/reviews', null, 'Token sesi tidak valid.');
        }

        $app = Application::findById($id);
        if (!$app) {
            redirect('/admin/reviews', null, 'Aplikasi tidak ditemukan.');
        }

        $notes = trim($_POST['notes'] ?? 'Aplikasi memenuhi kriteria dan disetujui untuk rilis.');
        Application::updateStatus($id, 'approved', $notes, Auth::id());

        Auth::logActivity(
            Auth::id(),
            'APP_APPROVE',
            "Admin menyetujui aplikasi '{$app['title']}' (ID: {$id})"
        );

        redirect('/admin/reviews', "Aplikasi '{$app['title']}' berhasil disetujui dan kini aktif di katalog!");
    }

    /**
     * FR 4.2: Rejection with required reason
     */
    public function reject(int $id): void {
        if (!Auth::verifyCsrf($_POST['csrf_token'] ?? '')) {
            redirect('/admin/reviews', null, 'Token sesi tidak valid.');
        }

        $notes = trim($_POST['notes'] ?? '');
        if (empty($notes)) {
            redirect('/admin/reviews', null, 'Alasan penolakan wajib diisi untuk memberi umpan balik kepada pengembang.');
        }

        $app = Application::findById($id);
        if (!$app) {
            redirect('/admin/reviews', null, 'Aplikasi tidak ditemukan.');
        }

        Application::updateStatus($id, 'rejected', $notes, Auth::id());

        Auth::logActivity(
            Auth::id(),
            'APP_REJECT',
            "Admin menolak aplikasi '{$app['title']}' dengan catatan: {$notes}"
        );

        redirect('/admin/reviews', "Aplikasi '{$app['title']}' telah ditolak dengan catatan evaluasi.");
    }

    public function requestRevision(int $id): void {
        if (!Auth::verifyCsrf($_POST['csrf_token'] ?? '')) {
            redirect('/admin/reviews', null, 'Token sesi tidak valid.');
        }

        $notes = trim($_POST['notes'] ?? '');
        if (empty($notes)) {
            redirect('/admin/reviews', null, 'Instruksi perbaikan wajib diisi agar pengaju dapat merevisi aplikasi.');
        }

        $app = Application::findById($id);
        if (!$app) {
            redirect('/admin/reviews', null, 'Aplikasi tidak ditemukan.');
        }

        Application::updateStatus($id, 'revision', $notes, Auth::id());

        Auth::logActivity(
            Auth::id(),
            'APP_REVISION',
            "Admin meminta revisi aplikasi '{$app['title']}' dengan catatan: {$notes}"
        );

        redirect('/admin/reviews', "Permintaan revisi untuk '{$app['title']}' berhasil dikirim ke pengaju.");
    }

    /**
     * FR 4.3: Categories CRUD
     */
    public function categories(): void {
        $categories = Category::getAll();
        $pageTitle = "Kelola Kategori Aplikasi - Admin Portal";
        require __DIR__ . '/../views/admin/categories.php';
    }

    public function storeCategory(): void {
        if (!Auth::verifyCsrf($_POST['csrf_token'] ?? '')) {
            redirect('/admin/categories', null, 'Token sesi tidak valid.');
        }

        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $icon = trim($_POST['icon'] ?? 'fa-folder');
        $color = trim($_POST['color'] ?? '#0ea5e9');

        if (empty($name)) {
            redirect('/admin/categories', null, 'Nama kategori wajib diisi.');
        }

        $slug = slugify($name);

        Category::create([
            'name' => $name,
            'slug' => $slug,
            'description' => $description,
            'icon' => $icon,
            'color' => $color
        ]);

        Auth::logActivity(Auth::id(), 'CATEGORY_CREATE', "Admin membuat kategori baru: {$name}");
        redirect('/admin/categories', "Kategori '{$name}' berhasil ditambahkan.");
    }

    public function updateCategory(int $id): void {
        if (!Auth::verifyCsrf($_POST['csrf_token'] ?? '')) {
            redirect('/admin/categories', null, 'Token sesi tidak valid.');
        }

        $name = trim($_POST['name'] ?? '');
        $slug = trim($_POST['slug'] ?? slugify($name));
        $description = trim($_POST['description'] ?? '');
        $icon = trim($_POST['icon'] ?? 'fa-folder');
        $color = trim($_POST['color'] ?? '#0ea5e9');

        Category::update($id, [
            'name' => $name,
            'slug' => $slug,
            'description' => $description,
            'icon' => $icon,
            'color' => $color
        ]);

        Auth::logActivity(Auth::id(), 'CATEGORY_UPDATE', "Admin memperbarui kategori ID {$id}");
        redirect('/admin/categories', "Kategori berhasil diperbarui.");
    }

    public function deleteCategory(int $id): void {
        if (!Auth::verifyCsrf($_POST['csrf_token'] ?? '')) {
            redirect('/admin/categories', null, 'Token sesi tidak valid.');
        }

        Category::delete($id);
        Auth::logActivity(Auth::id(), 'CATEGORY_DELETE', "Admin menghapus kategori ID {$id}");
        redirect('/admin/categories', "Kategori berhasil dihapus.");
    }

    /**
     * FR 4.4: User Management
     */
    public function users(): void {
        $users = User::getAll();
        $pageTitle = "Manajemen Pengguna - Admin Portal";
        require __DIR__ . '/../views/admin/users.php';
    }

    public function storeUser(): void {
        if (!Auth::verifyCsrf($_POST['csrf_token'] ?? '')) {
            redirect('/admin/users', null, 'Token sesi tidak valid.');
        }

        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? 'user123';
        $role = $_POST['role'] ?? 'user';
        $department = trim($_POST['department'] ?? 'Kelautan & Perikanan');

        if (empty($name) || empty($email)) {
            redirect('/admin/users', null, 'Nama dan email wajib diisi.');
        }

        if (User::findByEmail($email)) {
            redirect('/admin/users', null, 'Email sudah terdaftar di sistem.');
        }

        User::create([
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'role' => $role,
            'department' => $department,
            'is_active' => 1
        ]);

        Auth::logActivity(Auth::id(), 'USER_CREATE', "Admin menambahkan pengguna baru: {$email}");
        redirect('/admin/users', "Pengguna {$name} ({$email}) berhasil dibuat.");
    }

    public function updateUser(int $id): void {
        if (!Auth::verifyCsrf($_POST['csrf_token'] ?? '')) {
            redirect('/admin/users', null, 'Token sesi tidak valid.');
        }

        $role = $_POST['role'] ?? 'user';
        $isActive = isset($_POST['is_active']) ? 1 : 0;

        // Prevent self suspension or self-demotion
        if ($id === Auth::id() && ($isActive === 0 || $role !== 'admin')) {
            redirect('/admin/users', null, 'Anda tidak dapat menonaktifkan atau mengubah hak akses akun admin Anda sendiri.');
        }

        User::updateRoleAndStatus($id, $role, $isActive);

        if (!empty($_POST['new_password'])) {
            User::updatePassword($id, $_POST['new_password']);
        }

        Auth::logActivity(Auth::id(), 'USER_UPDATE', "Admin memperbarui data pengguna ID {$id}");
        redirect('/admin/users', "Data pengguna berhasil diperbarui.");
    }

    /**
     * FR 6.2: Audit Trail & Activity Logs
     */
    public function logs(): void {
        $logs = ActivityLog::getRecent(100);
        $stats = Application::getStats();
        $pageTitle = "Audit Trail & Log Aktivitas - Admin Portal";
        require __DIR__ . '/../views/admin/logs.php';
    }
}

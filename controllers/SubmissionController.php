<?php
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../models/Application.php';
require_once __DIR__ . '/../models/Category.php';

class SubmissionController {
    public function index(): void {
        Auth::requireLogin();
        $userId = Auth::id();
        $myApps = Application::getByUser($userId);
        $pageTitle = "Pengajuan Aplikasi Saya - Apps Hub Kelautan.ID";
        require __DIR__ . '/../views/my_apps.php';
    }

    public function showCreate(): void {
        Auth::requireLogin();
        $categories = Category::getAll();
        $pageTitle = "Ajukan Aplikasi Baru - Apps Hub Kelautan.ID";
        require __DIR__ . '/../views/submit_app.php';
    }

    public function store(): void {
        Auth::requireLogin();
        if (!Auth::verifyCsrf($_POST['csrf_token'] ?? '')) {
            redirect('/submissions/new', null, 'Token sesi tidak valid.');
        }

        $title = trim($_POST['title'] ?? '');
        $categoryId = (int)($_POST['category_id'] ?? 0);
        $appUrl = trim($_POST['app_url'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $iconType = $_POST['icon_type'] ?? 'preset';
        $iconValue = trim($_POST['icon_value'] ?? 'fa-cube');
        $isDraft = isset($_POST['save_as_draft']);

        if (empty($title) || empty($categoryId) || empty($appUrl)) {
            redirect('/submissions/new', null, 'Mohon lengkapi judul, kategori, dan URL aplikasi.');
        }

        // Validate URL format
        if (!filter_var($appUrl, FILTER_VALIDATE_URL)) {
            redirect('/submissions/new', null, 'Format URL aplikasi tidak valid (harus diawali http:// atau https://).');
        }

        // Handle file upload if icon_type is upload
        if ($iconType === 'upload' && isset($_FILES['icon_file']) && $_FILES['icon_file']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['icon_file'];
            $allowedTypes = ['image/png', 'image/jpeg', 'image/svg+xml', 'image/webp'];
            if (in_array($file['type'], $allowedTypes)) {
                $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                $filename = 'icon_' . time() . '_' . uniqid() . '.' . $ext;
                $targetDir = dirname(__DIR__) . '/uploads/';
                if (!is_dir($targetDir)) {
                    mkdir($targetDir, 0755, true);
                }
                if (move_uploaded_file($file['tmp_name'], $targetDir . $filename)) {
                    $iconValue = 'uploads/' . $filename;
                }
            }
        }

        $baseSlug = slugify($title);
        $slug = $baseSlug;
        $counter = 1;
        while (Application::findBySlugAndCategory('', $slug) !== null) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        $status = $isDraft ? 'draft' : 'pending';

        $appId = Application::create([
            'title' => $title,
            'slug' => $slug,
            'category_id' => $categoryId,
            'user_id' => Auth::id(),
            'app_url' => $appUrl,
            'description' => $description,
            'icon_type' => $iconType,
            'icon_value' => $iconValue,
            'status' => $status
        ]);

        Auth::logActivity(
            Auth::id(),
            $isDraft ? 'APP_DRAFT' : 'APP_SUBMIT',
            "Pengguna membuat aplikasi '{$title}' (Status: {$status})"
        );

        redirect('/submissions', 'Aplikasi berhasil ' . ($isDraft ? 'disimpan sebagai draf.' : 'diajukan untuk ditinjau oleh Admin.'));
    }

    public function showEdit(int $id): void {
        Auth::requireLogin();
        $app = Application::findById($id);

        if (!$app) {
            redirect('/submissions', null, 'Aplikasi tidak ditemukan.');
        }

        if ($app['user_id'] !== Auth::id() && !Auth::isAdmin()) {
            redirect('/submissions', null, 'Anda tidak memiliki hak akses mengubah aplikasi ini.');
        }

        $categories = Category::getAll();
        $pageTitle = "Perbarui Aplikasi: " . $app['title'] . " - Apps Hub Kelautan.ID";
        require __DIR__ . '/../views/edit_app.php';
    }

    public function update(int $id): void {
        Auth::requireLogin();
        if (!Auth::verifyCsrf($_POST['csrf_token'] ?? '')) {
            redirect("/submissions/edit/{$id}", null, 'Token sesi tidak valid.');
        }

        $app = Application::findById($id);
        if (!$app || ($app['user_id'] !== Auth::id() && !Auth::isAdmin())) {
            redirect('/submissions', null, 'Akses ditolak.');
        }

        $title = trim($_POST['title'] ?? '');
        $categoryId = (int)($_POST['category_id'] ?? 0);
        $appUrl = trim($_POST['app_url'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $iconType = $_POST['icon_type'] ?? 'preset';
        $iconValue = trim($_POST['icon_value'] ?? $app['icon_value']);
        $isDraft = isset($_POST['save_as_draft']);

        if (empty($title) || empty($categoryId) || empty($appUrl)) {
            redirect("/submissions/edit/{$id}", null, 'Mohon lengkapi judul, kategori, dan URL aplikasi.');
        }

        // Upload icon if provided
        if ($iconType === 'upload' && isset($_FILES['icon_file']) && $_FILES['icon_file']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['icon_file'];
            $allowedTypes = ['image/png', 'image/jpeg', 'image/svg+xml', 'image/webp'];
            if (in_array($file['type'], $allowedTypes)) {
                $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                $filename = 'icon_' . time() . '_' . uniqid() . '.' . $ext;
                $targetDir = dirname(__DIR__) . '/uploads/';
                if (move_uploaded_file($file['tmp_name'], $targetDir . $filename)) {
                    $iconValue = 'uploads/' . $filename;
                }
            }
        }

        // Status transition: if previously rejected or revision, resubmitting sets it to 'pending'
        $newStatus = $isDraft ? 'draft' : 'pending';

        Application::update($id, [
            'title' => $title,
            'slug' => $app['slug'],
            'category_id' => $categoryId,
            'app_url' => $appUrl,
            'description' => $description,
            'icon_type' => $iconType,
            'icon_value' => $iconValue,
            'status' => $newStatus
        ]);

        Auth::logActivity(
            Auth::id(),
            'APP_RESUBMIT',
            "Pengguna memperbarui & mengajukan ulang aplikasi '{$title}'"
        );

        redirect('/submissions', 'Aplikasi berhasil diperbarui dan diajukan ulang untuk verifikasi.');
    }

    public function delete(int $id): void {
        Auth::requireLogin();
        if (!Auth::verifyCsrf($_POST['csrf_token'] ?? '')) {
            redirect('/submissions', null, 'Token sesi tidak valid.');
        }

        $app = Application::findById($id);
        if (!$app || ($app['user_id'] !== Auth::id() && !Auth::isAdmin())) {
            redirect('/submissions', null, 'Akses ditolak.');
        }

        Application::delete($id);
        Auth::logActivity(Auth::id(), 'APP_DELETE', "Aplikasi '{$app['title']}' telah dihapus.");
        redirect('/submissions', 'Aplikasi berhasil dihapus.');
    }
}

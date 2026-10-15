<?php
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../models/Application.php';
require_once __DIR__ . '/../models/Category.php';

class AppController {
    public function index(): void {
        $currentUser = Auth::user();
        $userId = $currentUser ? $currentUser['id'] : null;

        // Get filter params
        $catFilter = isset($_GET['cat']) ? (int)$_GET['cat'] : null;
        $search = isset($_GET['q']) ? trim($_GET['q']) : null;
        $viewType = isset($_GET['view']) ? $_GET['view'] : 'all'; // 'all' or 'favorites'

        $categories = Category::getAll();

        if ($viewType === 'favorites') {
            if (!$currentUser) {
                redirect('/login', null, 'Silakan masuk terlebih dahulu untuk melihat daftar aplikasi favorit Anda.');
            }
            $apps = Application::getUserBookmarks($userId);
            // Apply in-memory search if search query exists
            if ($search) {
                $apps = array_filter($apps, function($a) use ($search) {
                    return stripos($a['title'], $search) !== false || stripos($a['description'], $search) !== false;
                });
            }
        } else {
            $apps = Application::getApproved($catFilter, $search, $userId);
        }

        // Recently used apps widget (FR 2.5)
        $recentlyUsed = $userId ? Application::getRecentlyUsed($userId, 4) : [];

        $pageTitle = "Katalog Aplikasi - Apps Hub Kelautan.ID";
        require __DIR__ . '/../views/dashboard.php';
    }

    /**
     * FR 5.1 & 5.2: Core Engine iFrame Wrapper
     */
    public function viewApp(string $categorySlug, string $appSlug): void {
        $currentUser = Auth::user();

        $app = Application::findBySlugAndCategory($categorySlug, $appSlug);

        if (!$app) {
            http_response_code(404);
            require __DIR__ . '/../views/404.php';
            exit;
        }

        // Only approved apps can be opened publicly (unless logged in as admin or author previewing)
        if ($app['status'] !== 'approved') {
            Auth::requireLogin();
            if (!Auth::isAdmin() && $app['user_id'] !== $currentUser['id']) {
                http_response_code(403);
                require __DIR__ . '/../views/403.php';
                exit;
            }
        }

        // FR 6.1: Increment hit count and record analytics hit
        Application::incrementHit($app['id'], $currentUser['id'] ?? null);

        // FR 5.3: Generate secure token for the micro-app
        if ($currentUser) {
            $appToken = Auth::generateAppToken($currentUser, $app['id']);
            $userIdParam = $currentUser['id'];
            $userNameParam = $currentUser['name'];
            $userEmailParam = $currentUser['email'];
        } else {
            $guestUser = [
                'id' => 0,
                'name' => 'Pengguna Publik',
                'email' => 'publik@kelautan.id',
                'role' => 'public',
                'department' => 'Publik'
            ];
            $appToken = Auth::generateAppToken($guestUser, $app['id']);
            $userIdParam = 0;
            $userNameParam = 'Pengguna Publik';
            $userEmailParam = 'publik@kelautan.id';
        }

        // Build target URL with auth parameters
        $targetUrl = $app['app_url'];
        
        // If it's a demo placeholder URL, redirect internally to our simulated micro-app endpoint
        if (strpos($targetUrl, 'example.com/demo-app-') !== false) {
            $demoType = str_replace('https://example.com/demo-app-', '', $targetUrl);
            $targetUrl = url('/mock-app/' . $demoType);
        }

        $separator = (strpos($targetUrl, '?') === false) ? '?' : '&';
        $fullFrameUrl = $targetUrl . $separator . http_build_query([
            'auth_token' => $appToken,
            'user_id' => $userIdParam,
            'user_name' => $userNameParam,
            'user_email' => $userEmailParam,
            'portal_source' => 'apps.kelautan.id'
        ]);

        $pageTitle = $app['title'] . " - Apps Hub Kelautan.ID";

        // Cek mode tampilan: Versi Standar (Wrapper) atau Versi Full Screen Murni (Frameless)
        $mode = $_GET['mode'] ?? $_GET['view'] ?? $_GET['layout'] ?? 'standard';
        if ($mode === 'full') {
            require __DIR__ . '/../views/app_view_full.php';
            return;
        }

        require __DIR__ . '/../views/app_view.php';
    }

    /**
     * FR 2.4: AJAX toggle bookmark
     */
    public function toggleBookmark(): void {
        header('Content-Type: application/json');
        if (!Auth::check()) {
            echo json_encode([
                'success' => false, 
                'require_login' => true, 
                'message' => 'Silakan masuk untuk menandai aplikasi favorit.'
            ]);
            exit;
        }

        $appId = isset($_POST['app_id']) ? (int)$_POST['app_id'] : 0;
        if (!$appId) {
            echo json_encode(['success' => false, 'error' => 'Invalid app ID']);
            exit;
        }

        $isBookmarked = Application::toggleBookmark(Auth::id(), $appId);

        echo json_encode([
            'success' => true,
            'is_bookmarked' => $isBookmarked,
            'message' => $isBookmarked ? 'Aplikasi ditambahkan ke favorit' : 'Aplikasi dihapus dari favorit'
        ]);
        exit;
    }

    /**
     * Interactive Mock Micro-Apps (GAS / TypeScript SPA demo inside iFrame)
     */
    public function mockApp(string $type): void {
        $user = [
            'id' => $_GET['user_id'] ?? 'N/A',
            'name' => $_GET['user_name'] ?? 'Tamu',
            'email' => $_GET['user_email'] ?? 'tamu@kelautan.id',
            'token' => $_GET['auth_token'] ?? 'NO_TOKEN'
        ];
        
        require __DIR__ . '/../views/mock_microapp.php';
        exit;
    }
}

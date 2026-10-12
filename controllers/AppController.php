<?php
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../models/Application.php';
require_once __DIR__ . '/../models/Category.php';

class AppController {
    public function index(): void {
        Auth::requireLogin();
        $currentUser = Auth::user();

        // Get filter params
        $catFilter = isset($_GET['cat']) ? (int)$_GET['cat'] : null;
        $search = isset($_GET['q']) ? trim($_GET['q']) : null;
        $viewType = isset($_GET['view']) ? $_GET['view'] : 'all'; // 'all' or 'favorites'

        $categories = Category::getAll();

        if ($viewType === 'favorites') {
            $apps = Application::getUserBookmarks($currentUser['id']);
            // Apply in-memory search if search query exists
            if ($search) {
                $apps = array_filter($apps, function($a) use ($search) {
                    return stripos($a['title'], $search) !== false || stripos($a['description'], $search) !== false;
                });
            }
        } else {
            $apps = Application::getApproved($catFilter, $search, $currentUser['id']);
        }

        // Recently used apps widget (FR 2.5)
        $recentlyUsed = Application::getRecentlyUsed($currentUser['id'], 4);

        $pageTitle = "Katalog Aplikasi - Portal Hub Kelautan";
        require __DIR__ . '/../views/dashboard.php';
    }

    /**
     * FR 5.1 & 5.2: Core Engine iFrame Wrapper
     */
    public function viewApp(string $categorySlug, string $appSlug): void {
        Auth::requireLogin();
        $currentUser = Auth::user();

        $app = Application::findBySlugAndCategory($categorySlug, $appSlug);

        if (!$app) {
            http_response_code(404);
            require __DIR__ . '/../views/404.php';
            exit;
        }

        // Only approved apps can be opened by regular users (unless admin or author previewing)
        if ($app['status'] !== 'approved' && !Auth::isAdmin() && $app['user_id'] !== $currentUser['id']) {
            http_response_code(403);
            require __DIR__ . '/../views/403.php';
            exit;
        }

        // FR 6.1: Increment hit count and record analytics hit
        Application::incrementHit($app['id'], $currentUser['id']);

        // FR 5.3: Generate secure token for the micro-app
        $appToken = Auth::generateAppToken($currentUser, $app['id']);

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
            'user_id' => $currentUser['id'],
            'user_name' => $currentUser['name'],
            'user_email' => $currentUser['email'],
            'portal_source' => 'apps.kelautan.id'
        ]);

        $pageTitle = $app['title'] . " - Portal Hub Kelautan";
        require __DIR__ . '/../views/app_view.php';
    }

    /**
     * FR 2.4: AJAX toggle bookmark
     */
    public function toggleBookmark(): void {
        header('Content-Type: application/json');
        if (!Auth::check()) {
            echo json_encode(['success' => false, 'error' => 'Unauthorized']);
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

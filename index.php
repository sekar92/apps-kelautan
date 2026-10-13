<?php
/**
 * Front Controller & Routing Engine
 * Portal Hub Aplikasi (Apps Portal Kelautan)
 */

// Early .env loader
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    $parsed = @parse_ini_file($envFile);
    if (is_array($parsed)) {
        foreach ($parsed as $key => $val) {
            putenv("{$key}={$val}");
            $_ENV[$key] = $val;
            $_SERVER[$key] = $val;
        }
    } else {
        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line) || strpos($line, '#') === 0) continue;
            if (strpos($line, '=') !== false) {
                list($key, $val) = explode('=', $line, 2);
                $key = trim($key);
                $val = trim($val, " \t\n\r\0\x0B\"'");
                putenv("{$key}={$val}");
                $_ENV[$key] = $val;
                $_SERVER[$key] = $val;
            }
        }
    }
}

$isDebug = ($_ENV['APP_DEBUG'] ?? getenv('APP_DEBUG')) === 'true' || ($_ENV['APP_DEBUG'] ?? getenv('APP_DEBUG')) === '1';
if ($isDebug) {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
}

try {
    require_once __DIR__ . '/config/database.php';
    require_once __DIR__ . '/config/auth.php';
    require_once __DIR__ . '/config/helpers.php';
    require_once __DIR__ . '/controllers/AuthController.php';
    require_once __DIR__ . '/controllers/AppController.php';
    require_once __DIR__ . '/controllers/SubmissionController.php';
    require_once __DIR__ . '/controllers/AdminController.php';

    // Initialize session and security headers
    Auth::init();

    // Parse request URI
    $requestUri = $_SERVER['REQUEST_URI'] ?? '/';
    $parsedUrl = parse_url($requestUri);
    $path = $parsedUrl['path'] ?? '/';

    // Strip application base directory if running in subdirectory
    $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
    if ($scriptDir !== '/' && $scriptDir !== '.' && strpos($path, $scriptDir) === 0) {
        $path = substr($path, strlen($scriptDir));
    }

    // Strip leading /index.php if directly accessed
    if (strpos($path, '/index.php') === 0) {
        $path = substr($path, strlen('/index.php'));
    }
    $path = '/' . trim($path, '/');

    $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

// -------------------------------------------------------------
// Core Application Routing
// -------------------------------------------------------------

// Home / Dashboard
if ($path === '/' || $path === '') {
    (new AppController())->index();
    exit;
}

// Authentication
if ($path === '/login') {
    $auth = new AuthController();
    if ($method === 'POST') {
        $auth->processLogin();
    } else {
        $auth->showLogin();
    }
    exit;
}

if ($path === '/logout') {
    (new AuthController())->logout();
    exit;
}

if ($path === '/profile') {
    $auth = new AuthController();
    if ($method === 'POST') {
        $auth->updateProfile();
    } else {
        $auth->showProfile();
    }
    exit;
}

if ($path === '/profile/password' && $method === 'POST') {
    (new AuthController())->updatePassword();
    exit;
}

// Bookmark API (AJAX)
if ($path === '/api/bookmark' && $method === 'POST') {
    (new AppController())->toggleBookmark();
    exit;
}

// User Submissions
if ($path === '/submissions') {
    (new SubmissionController())->index();
    exit;
}

if ($path === '/submissions/new') {
    $sub = new SubmissionController();
    if ($method === 'POST') {
        $sub->store();
    } else {
        $sub->showCreate();
    }
    exit;
}

if (preg_match('#^/submissions/edit/(\d+)$#', $path, $matches)) {
    $id = (int)$matches[1];
    $sub = new SubmissionController();
    if ($method === 'POST') {
        $sub->update($id);
    } else {
        $sub->showEdit($id);
    }
    exit;
}

if (preg_match('#^/submissions/delete/(\d+)$#', $path, $matches) && $method === 'POST') {
    (new SubmissionController())->delete((int)$matches[1]);
    exit;
}

// Admin Panel Routes
if ($path === '/admin' || $path === '/admin/reviews') {
    (new AdminController())->reviewBoard();
    exit;
}

if (preg_match('#^/admin/reviews/(\d+)/approve$#', $path, $matches) && $method === 'POST') {
    (new AdminController())->approve((int)$matches[1]);
    exit;
}

if (preg_match('#^/admin/reviews/(\d+)/reject$#', $path, $matches) && $method === 'POST') {
    (new AdminController())->reject((int)$matches[1]);
    exit;
}

if (preg_match('#^/admin/reviews/(\d+)/revision$#', $path, $matches) && $method === 'POST') {
    (new AdminController())->requestRevision((int)$matches[1]);
    exit;
}

if ($path === '/admin/categories') {
    $admin = new AdminController();
    if ($method === 'POST') {
        $admin->storeCategory();
    } else {
        $admin->categories();
    }
    exit;
}

if (preg_match('#^/admin/categories/(\d+)/update$#', $path, $matches) && $method === 'POST') {
    (new AdminController())->updateCategory((int)$matches[1]);
    exit;
}

if (preg_match('#^/admin/categories/(\d+)/delete$#', $path, $matches) && $method === 'POST') {
    (new AdminController())->deleteCategory((int)$matches[1]);
    exit;
}

if ($path === '/admin/users') {
    $admin = new AdminController();
    if ($method === 'POST') {
        $admin->storeUser();
    } else {
        $admin->users();
    }
    exit;
}

if (preg_match('#^/admin/users/(\d+)/update$#', $path, $matches) && $method === 'POST') {
    (new AdminController())->updateUser((int)$matches[1]);
    exit;
}

if ($path === '/admin/logs') {
    (new AdminController())->logs();
    exit;
}

// Interactive Mock Micro-App Route (for testing sandboxed iFrames)
if (preg_match('#^/mock-app/([a-zA-Z0-9_\-]+)$#', $path, $matches)) {
    (new AppController())->mockApp($matches[1]);
    exit;
}

// Dynamic iFrame Route (FR 5.1): /[category_slug]/[app_slug]
if (preg_match('#^/([a-zA-Z0-9_\-]+)/([a-zA-Z0-9_\-]+)$#', $path, $matches)) {
    $categorySlug = $matches[1];
    $appSlug = $matches[2];
    (new AppController())->viewApp($categorySlug, $appSlug);
    exit;
}

// 404 Fallback
http_response_code(404);
require __DIR__ . '/views/404.php';

} catch (Throwable $e) {
    error_log("Portal Server Error: " . $e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine());
    http_response_code(500);
    $errorMessage = $e->getMessage();
    require __DIR__ . '/views/500.php';
    exit;
}

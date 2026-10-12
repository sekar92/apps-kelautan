<?php
/**
 * Authentication & Security Manager
 * Handles sessions, session timeouts (FR 1.2), CSRF tokens, access control, and iframe tokens (FR 5.3).
 */

require_once __DIR__ . '/database.php';

class Auth {
    private const SESSION_TIMEOUT = 1800; // 30 minutes in seconds (FR 1.2)
    private const SECRET_KEY = 'kelautan_apps_secret_portal_key_2026';

    public static function init(): void {
        if (session_status() === PHP_SESSION_NONE) {
            // Set secure session cookie parameters
            session_set_cookie_params([
                'lifetime' => 0,
                'path' => '/',
                'domain' => '',
                'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
                'httponly' => true,
                'samesite' => 'Lax'
            ]);
            session_start();
        }

        // Apply Anti-Clickjacking headers to the Portal (NFR 2)
        if (!headers_sent()) {
            header('X-Frame-Options: SAMEORIGIN');
            header('X-Content-Type-Options: nosniff');
            header('Referrer-Policy: strict-origin-when-cross-origin');
        }

        // Check Session Inactivity Timeout (FR 1.2)
        if (self::check()) {
            $lastActivity = $_SESSION['last_activity'] ?? time();
            if ((time() - $lastActivity) > self::SESSION_TIMEOUT) {
                self::logout('Sesi Anda telah berakhir karena tidak ada aktivitas selama 30 menit.');
                header('Location: ' . url('/login?expired=1'));
                exit;
            }
            $_SESSION['last_activity'] = time();
        }

        // Generate CSRF token if not set
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
    }

    public static function check(): bool {
        return !empty($_SESSION['user_id']);
    }

    public static function user(): ?array {
        if (!self::check()) {
            return null;
        }

        return [
            'id' => $_SESSION['user_id'] ?? null,
            'name' => $_SESSION['user_name'] ?? '',
            'email' => $_SESSION['user_email'] ?? '',
            'role' => $_SESSION['user_role'] ?? 'user',
            'department' => $_SESSION['user_department'] ?? 'Kelautan & Perikanan',
        ];
    }

    public static function id(): ?int {
        return $_SESSION['user_id'] ?? null;
    }

    public static function role(): string {
        return $_SESSION['user_role'] ?? 'user';
    }

    public static function isAdmin(): bool {
        return self::role() === 'admin';
    }

    public static function login(array $user): void {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['user_department'] = $user['department'] ?? 'Kelautan & Perikanan';
        $_SESSION['last_activity'] = time();

        // Log login activity
        self::logActivity($user['id'], 'USER_LOGIN', 'Pengguna berhasil masuk ke sistem.');
    }

    public static function logout(?string $message = null): void {
        if (self::check()) {
            self::logActivity(self::id(), 'USER_LOGOUT', 'Pengguna keluar dari sistem.');
        }

        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
        
        if ($message) {
            session_start();
            $_SESSION['flash_info'] = $message;
        }
    }

    public static function requireLogin(): void {
        if (!self::check()) {
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'] ?? '/';
            header('Location: ' . url('/login'));
            exit;
        }
    }

    public static function requireAdmin(): void {
        self::requireLogin();
        if (!self::isAdmin()) {
            http_response_code(403);
            require_once dirname(__DIR__) . '/views/403.php';
            exit;
        }
    }

    public static function csrfToken(): string {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public static function verifyCsrf(?string $token): bool {
        if (empty($token) || empty($_SESSION['csrf_token'])) {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * Generate secure authentication token to pass to micro-apps (FR 5.3)
     */
    public static function generateAppToken(array $user, int $appId): string {
        $payload = [
            'uid' => $user['id'],
            'email' => $user['email'],
            'name' => $user['name'],
            'role' => $user['role'],
            'app_id' => $appId,
            'iat' => time(),
            'exp' => time() + 3600 // Valid for 1 hour
        ];

        $json = json_encode($payload);
        $encodedPayload = base64_encode($json);
        $signature = hash_hmac('sha256', $encodedPayload, self::SECRET_KEY);

        return $encodedPayload . '.' . $signature;
    }

    public static function logActivity(?int $userId, string $action, string $details): void {
        try {
            $db = Database::getConnection();
            $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
            $stmt = $db->prepare("INSERT INTO activity_logs (user_id, action, details, ip_address) VALUES (?, ?, ?, ?)");
            $stmt->execute([$userId, $action, $details, $ip]);
        } catch (Exception $e) {
            error_log("Failed to write activity log: " . $e->getMessage());
        }
    }
}

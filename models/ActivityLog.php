<?php
require_once __DIR__ . '/../config/database.php';

class ActivityLog {
    public static function getRecent(int $limit = 100): array {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT l.*, u.name as user_name, u.email as user_email, u.role as user_role
            FROM activity_logs l
            LEFT JOIN users u ON l.user_id = u.id
            ORDER BY l.created_at DESC
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }

    public static function log(?int $userId, string $action, string $details): void {
        Auth::logActivity($userId, $action, $details);
    }
}

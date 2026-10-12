<?php
require_once __DIR__ . '/../config/database.php';

class Application {
    public static function getApproved(?int $categoryId = null, ?string $search = null, ?int $userId = null): array {
        $db = Database::getConnection();
        $params = [];
        $sql = "
            SELECT a.*, c.name as category_name, c.slug as category_slug, c.color as category_color,
                   u.name as author_name,
                   CASE WHEN b.id IS NOT NULL THEN 1 ELSE 0 END as is_bookmarked
            FROM applications a
            JOIN categories c ON a.category_id = c.id
            JOIN users u ON a.user_id = u.id
            LEFT JOIN bookmarks b ON b.application_id = a.id AND b.user_id = ?
            WHERE a.status = 'approved'
        ";
        $params[] = $userId ?? 0;

        if ($categoryId) {
            $sql .= " AND a.category_id = ?";
            $params[] = $categoryId;
        }

        if (!empty($search)) {
            $sql .= " AND (a.title LIKE ? OR a.description LIKE ?)";
            $params[] = '%' . $search . '%';
            $params[] = '%' . $search . '%';
        }

        $sql .= " ORDER BY a.is_featured DESC, a.hit_count DESC, a.title ASC";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function getRecentlyUsed(int $userId, int $limit = 4): array {
        $db = Database::getConnection();
        $sql = "
            SELECT a.*, c.name as category_name, c.slug as category_slug, c.color as category_color,
                   MAX(h.created_at) as last_opened_at,
                   CASE WHEN b.id IS NOT NULL THEN 1 ELSE 0 END as is_bookmarked
            FROM app_hits h
            JOIN applications a ON h.application_id = a.id
            JOIN categories c ON a.category_id = c.id
            LEFT JOIN bookmarks b ON b.application_id = a.id AND b.user_id = ?
            WHERE h.user_id = ? AND a.status = 'approved'
            GROUP BY a.id
            ORDER BY last_opened_at DESC
            LIMIT ?
        ";
        $stmt = $db->prepare($sql);
        $stmt->execute([$userId, $userId, $limit]);
        return $stmt->fetchAll();
    }

    public static function getUserBookmarks(int $userId): array {
        $db = Database::getConnection();
        $sql = "
            SELECT a.*, c.name as category_name, c.slug as category_slug, c.color as category_color,
                   u.name as author_name, 1 as is_bookmarked
            FROM bookmarks b
            JOIN applications a ON b.application_id = a.id
            JOIN categories c ON a.category_id = c.id
            JOIN users u ON a.user_id = u.id
            WHERE b.user_id = ? AND a.status = 'approved'
            ORDER BY b.created_at DESC
        ";
        $stmt = $db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public static function findById(int $id): ?array {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT a.*, c.name as category_name, c.slug as category_slug, c.color as category_color,
                   u.name as author_name, u.email as author_email
            FROM applications a
            JOIN categories c ON a.category_id = c.id
            JOIN users u ON a.user_id = u.id
            WHERE a.id = ?
        ");
        $stmt->execute([$id]);
        $app = $stmt->fetch();
        return $app ?: null;
    }

    public static function findBySlugAndCategory(string $categorySlug, string $appSlug): ?array {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT a.*, c.name as category_name, c.slug as category_slug, c.color as category_color,
                   u.name as author_name, u.email as author_email
            FROM applications a
            JOIN categories c ON a.category_id = c.id
            JOIN users u ON a.user_id = u.id
            WHERE (c.slug = ? OR ? = '') AND a.slug = ?
        ");
        $stmt->execute([$categorySlug, $categorySlug, $appSlug]);
        $app = $stmt->fetch();
        return $app ?: null;
    }

    public static function getByUser(int $userId): array {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT a.*, c.name as category_name, c.slug as category_slug, c.color as category_color,
                   rev.name as reviewer_name
            FROM applications a
            JOIN categories c ON a.category_id = c.id
            LEFT JOIN users rev ON a.reviewed_by = rev.id
            WHERE a.user_id = ?
            ORDER BY a.created_at DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public static function getPending(): array {
        $db = Database::getConnection();
        $stmt = $db->query("
            SELECT a.*, c.name as category_name, c.slug as category_slug, c.color as category_color,
                   u.name as author_name, u.email as author_email, u.department as author_department
            FROM applications a
            JOIN categories c ON a.category_id = c.id
            JOIN users u ON a.user_id = u.id
            WHERE a.status = 'pending'
            ORDER BY a.created_at ASC
        ");
        return $stmt->fetchAll();
    }

    public static function getAllAdmin(?string $status = null): array {
        $db = Database::getConnection();
        $sql = "
            SELECT a.*, c.name as category_name, c.slug as category_slug,
                   u.name as author_name, u.email as author_email,
                   rev.name as reviewer_name
            FROM applications a
            JOIN categories c ON a.category_id = c.id
            JOIN users u ON a.user_id = u.id
            LEFT JOIN users rev ON a.reviewed_by = rev.id
        ";
        $params = [];
        if ($status) {
            $sql .= " WHERE a.status = ?";
            $params[] = $status;
        }
        $sql .= " ORDER BY a.created_at DESC";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function create(array $data): int {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            INSERT INTO applications (
                title, slug, category_id, user_id, app_url, description,
                icon_type, icon_value, status
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $data['title'],
            $data['slug'],
            $data['category_id'],
            $data['user_id'],
            $data['app_url'],
            $data['description'] ?? '',
            $data['icon_type'] ?? 'preset',
            $data['icon_value'] ?? 'fa-cube',
            $data['status'] ?? 'pending'
        ]);
        return (int)$db->lastInsertId();
    }

    public static function update(int $id, array $data): bool {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            UPDATE applications SET 
                title = ?, slug = ?, category_id = ?, app_url = ?, description = ?,
                icon_type = ?, icon_value = ?, status = ?, updated_at = CURRENT_TIMESTAMP
            WHERE id = ?
        ");
        return $stmt->execute([
            $data['title'],
            $data['slug'],
            $data['category_id'],
            $data['app_url'],
            $data['description'],
            $data['icon_type'],
            $data['icon_value'],
            $data['status'],
            $id
        ]);
    }

    public static function updateStatus(int $id, string $status, ?string $notes, int $adminId): bool {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            UPDATE applications SET
                status = ?,
                review_notes = ?,
                reviewed_by = ?,
                reviewed_at = CURRENT_TIMESTAMP,
                updated_at = CURRENT_TIMESTAMP
            WHERE id = ?
        ");
        return $stmt->execute([$status, $notes, $adminId, $id]);
    }

    public static function incrementHit(int $id, ?int $userId = null): void {
        $db = Database::getConnection();
        // Increment hit_count
        $stmt = $db->prepare("UPDATE applications SET hit_count = hit_count + 1 WHERE id = ?");
        $stmt->execute([$id]);

        // Record hit details in analytics table
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
        $logStmt = $db->prepare("INSERT INTO app_hits (application_id, user_id, ip_address, user_agent) VALUES (?, ?, ?, ?)");
        $logStmt->execute([$id, $userId, $ip, $ua]);
    }

    public static function toggleBookmark(int $userId, int $appId): bool {
        $db = Database::getConnection();
        $check = $db->prepare("SELECT id FROM bookmarks WHERE user_id = ? AND application_id = ?");
        $check->execute([$userId, $appId]);
        $exists = $check->fetch();

        if ($exists) {
            $del = $db->prepare("DELETE FROM bookmarks WHERE user_id = ? AND application_id = ?");
            $del->execute([$userId, $appId]);
            return false; // Removed
        } else {
            $ins = $db->prepare("INSERT INTO bookmarks (user_id, application_id) VALUES (?, ?)");
            $ins->execute([$userId, $appId]);
            return true; // Added
        }
    }

    public static function delete(int $id): bool {
        $db = Database::getConnection();
        $stmt = $db->prepare("DELETE FROM applications WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public static function getStats(): array {
        $db = Database::getConnection();
        return [
            'total_apps' => (int)$db->query("SELECT COUNT(*) FROM applications")->fetchColumn(),
            'approved_apps' => (int)$db->query("SELECT COUNT(*) FROM applications WHERE status = 'approved'")->fetchColumn(),
            'pending_apps' => (int)$db->query("SELECT COUNT(*) FROM applications WHERE status = 'pending'")->fetchColumn(),
            'revision_apps' => (int)$db->query("SELECT COUNT(*) FROM applications WHERE status = 'revision'")->fetchColumn(),
            'total_hits' => (int)$db->query("SELECT COALESCE(SUM(hit_count), 0) FROM applications")->fetchColumn(),
            'total_users' => (int)$db->query("SELECT COUNT(*) FROM users")->fetchColumn(),
            'total_categories' => (int)$db->query("SELECT COUNT(*) FROM categories")->fetchColumn(),
        ];
    }
}

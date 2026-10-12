<?php
require_once __DIR__ . '/../config/database.php';

class User {
    public static function findById(int $id): ?array {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public static function findByEmail(string $email): ?array {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public static function create(array $data): int {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            INSERT INTO users (name, email, password, role, department, is_active)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $data['name'],
            $data['email'],
            password_hash($data['password'], PASSWORD_BCRYPT),
            $data['role'] ?? 'user',
            $data['department'] ?? 'Kelautan & Perikanan',
            $data['is_active'] ?? 1
        ]);
        return (int)$db->lastInsertId();
    }

    public static function update(int $id, array $data): bool {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            UPDATE users SET name = ?, department = ?, updated_at = CURRENT_TIMESTAMP
            WHERE id = ?
        ");
        return $stmt->execute([$data['name'], $data['department'], $id]);
    }

    public static function updatePassword(int $id, string $newPassword): bool {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            UPDATE users SET password = ?, updated_at = CURRENT_TIMESTAMP
            WHERE id = ?
        ");
        return $stmt->execute([password_hash($newPassword, PASSWORD_BCRYPT), $id]);
    }

    public static function getAll(): array {
        $db = Database::getConnection();
        $stmt = $db->query("
            SELECT u.*, 
            (SELECT COUNT(*) FROM applications WHERE user_id = u.id) as app_count
            FROM users u 
            ORDER BY u.created_at DESC
        ");
        return $stmt->fetchAll();
    }

    public static function updateRoleAndStatus(int $id, string $role, int $isActive): bool {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            UPDATE users SET role = ?, is_active = ?, updated_at = CURRENT_TIMESTAMP
            WHERE id = ?
        ");
        return $stmt->execute([$role, $isActive, $id]);
    }
}

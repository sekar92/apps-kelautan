<?php
require_once __DIR__ . '/../config/database.php';

class Category {
    public static function getAll(): array {
        $db = Database::getConnection();
        $stmt = $db->query("
            SELECT c.*, 
            (SELECT COUNT(*) FROM applications WHERE category_id = c.id AND status = 'approved') as approved_count,
            (SELECT COUNT(*) FROM applications WHERE category_id = c.id) as total_count
            FROM categories c 
            ORDER BY c.name ASC
        ");
        return $stmt->fetchAll();
    }

    public static function findById(int $id): ?array {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM categories WHERE id = ?");
        $stmt->execute([$id]);
        $cat = $stmt->fetch();
        return $cat ?: null;
    }

    public static function findBySlug(string $slug): ?array {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM categories WHERE slug = ?");
        $stmt->execute([$slug]);
        $cat = $stmt->fetch();
        return $cat ?: null;
    }

    public static function create(array $data): int {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            INSERT INTO categories (name, slug, description, icon, color)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $data['name'],
            $data['slug'],
            $data['description'] ?? '',
            $data['icon'] ?? 'fa-folder',
            $data['color'] ?? '#0ea5e9'
        ]);
        return (int)$db->lastInsertId();
    }

    public static function update(int $id, array $data): bool {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            UPDATE categories 
            SET name = ?, slug = ?, description = ?, icon = ?, color = ?
            WHERE id = ?
        ");
        return $stmt->execute([
            $data['name'],
            $data['slug'],
            $data['description'] ?? '',
            $data['icon'] ?? 'fa-folder',
            $data['color'] ?? '#0ea5e9',
            $id
        ]);
    }

    public static function delete(int $id): bool {
        $db = Database::getConnection();
        $stmt = $db->prepare("DELETE FROM categories WHERE id = ?");
        return $stmt->execute([$id]);
    }
}

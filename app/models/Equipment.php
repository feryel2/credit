<?php

class Equipment
{
    public static function all(PDO $pdo): array
    {
        $stmt = $pdo->query(
            'SELECT e.*, c.name as category_name FROM equipments e LEFT JOIN categories c ON c.id = e.category_id ORDER BY e.id DESC'
        );
        return $stmt->fetchAll();
    }

    public static function search(PDO $pdo, array $filters = []): array
    {
        $sql = 'SELECT e.*, c.name as category_name FROM equipments e LEFT JOIN categories c ON c.id = e.category_id WHERE 1=1';
        $params = [];

        if (!empty($filters['category_id'])) {
            $sql .= ' AND e.category_id = :category_id';
            $params[':category_id'] = $filters['category_id'];
        }

        if (!empty($filters['status'])) {
            $sql .= ' AND e.status = :status';
            $params[':status'] = $filters['status'];
        }

        if (!empty($filters['query'])) {
            $sql .= ' AND (e.name LIKE :query OR e.code LIKE :query OR e.serial_number LIKE :query)';
            $params[':query'] = '%' . $filters['query'] . '%';
        }

        $sql .= ' ORDER BY e.id DESC';
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function findById(PDO $pdo, int $id): ?array
    {
        $stmt = $pdo->prepare('SELECT e.*, c.name as category_name FROM equipments e LEFT JOIN categories c ON c.id = e.category_id WHERE e.id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public static function create(PDO $pdo, array $data): int
    {
        $stmt = $pdo->prepare('INSERT INTO equipments (category_id, name, code, serial_number, purchase_price, daily_rate, status, stock_level, alert_threshold, condition_note, created_at) VALUES (:category_id, :name, :code, :serial_number, :purchase_price, :daily_rate, :status, :stock_level, :alert_threshold, :condition_note, :created_at)');
        $stmt->execute([
            ':category_id' => $data['category_id'],
            ':name' => $data['name'],
            ':code' => $data['code'],
            ':serial_number' => $data['serial_number'] ?? null,
            ':purchase_price' => (float)($data['purchase_price'] ?? 0),
            ':daily_rate' => (float)($data['daily_rate'] ?? 0),
            ':status' => $data['status'],
            ':stock_level' => (int)($data['stock_level'] ?? 0),
            ':alert_threshold' => (int)($data['alert_threshold'] ?? 0),
            ':condition_note' => $data['condition_note'] ?? null,
            ':created_at' => date('Y-m-d H:i:s'),
        ]);
        return (int)$pdo->lastInsertId();
    }

    public static function update(PDO $pdo, int $id, array $data): bool
    {
        $stmt = $pdo->prepare('UPDATE equipments SET category_id = :category_id, name = :name, code = :code, serial_number = :serial_number, purchase_price = :purchase_price, daily_rate = :daily_rate, status = :status, stock_level = :stock_level, alert_threshold = :alert_threshold, condition_note = :condition_note WHERE id = :id');
        return $stmt->execute([
            ':category_id' => $data['category_id'],
            ':name' => $data['name'],
            ':code' => $data['code'],
            ':serial_number' => $data['serial_number'] ?? null,
            ':purchase_price' => (float)($data['purchase_price'] ?? 0),
            ':daily_rate' => (float)($data['daily_rate'] ?? 0),
            ':status' => $data['status'],
            ':stock_level' => (int)($data['stock_level'] ?? 0),
            ':alert_threshold' => (int)($data['alert_threshold'] ?? 0),
            ':condition_note' => $data['condition_note'] ?? null,
            ':id' => $id,
        ]);
    }

    public static function delete(PDO $pdo, int $id): bool
    {
        $stmt = $pdo->prepare('DELETE FROM equipments WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }

    public static function getAvailable(PDO $pdo): array
    {
        $stmt = $pdo->query('SELECT * FROM equipments WHERE status = "available" ORDER BY name ASC');
        return $stmt->fetchAll();
    }
}

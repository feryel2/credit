<?php

class Category
{
    public static function all(PDO $pdo): array
    {
        $stmt = $pdo->query('SELECT * FROM categories ORDER BY id DESC');
        return $stmt->fetchAll();
    }

    public static function findById(PDO $pdo, int $id): ?array
    {
        $stmt = $pdo->prepare('SELECT * FROM categories WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public static function create(PDO $pdo, array $data): int
    {
        $stmt = $pdo->prepare('INSERT INTO categories (name, description, created_at) VALUES (:name, :description, :created_at)');
        $stmt->execute([
            ':name' => $data['name'],
            ':description' => $data['description'] ?? null,
            ':created_at' => date('Y-m-d H:i:s'),
        ]);
        return (int)$pdo->lastInsertId();
    }

    public static function update(PDO $pdo, int $id, array $data): bool
    {
        $stmt = $pdo->prepare('UPDATE categories SET name = :name, description = :description WHERE id = :id');
        return $stmt->execute([
            ':name' => $data['name'],
            ':description' => $data['description'] ?? null,
            ':id' => $id,
        ]);
    }

    public static function delete(PDO $pdo, int $id): bool
    {
        $stmt = $pdo->prepare('DELETE FROM categories WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }
}

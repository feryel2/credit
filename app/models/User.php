<?php

class User
{
    public static function all(PDO $pdo): array
    {
        $stmt = $pdo->query('SELECT * FROM users ORDER BY id DESC');
        return $stmt->fetchAll();
    }

    public static function allClients(PDO $pdo): array
    {
        $stmt = $pdo->query("SELECT id, name, email FROM users WHERE role = 'client' ORDER BY name ASC");
        return $stmt->fetchAll();
    }

    public static function findById(PDO $pdo, int $id): ?array
    {
        $stmt = $pdo->prepare('SELECT * FROM users WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public static function findClientById(PDO $pdo, int $id): ?array
    {
        $stmt = $pdo->prepare("SELECT id, name, email FROM users WHERE id = :id AND role = 'client' LIMIT 1");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public static function findByEmail(PDO $pdo, string $email): ?array
    {
        $stmt = $pdo->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public static function create(PDO $pdo, array $data): int
    {
        $stmt = $pdo->prepare('INSERT INTO users (name, email, password_hash, role, phone, created_at) VALUES (:name, :email, :password_hash, :role, :phone, :created_at)');
        $stmt->execute([
            ':name' => $data['name'],
            ':email' => $data['email'],
            ':password_hash' => password_hash($data['password'], PASSWORD_DEFAULT),
            ':role' => $data['role'],
            ':phone' => $data['phone'] ?? null,
            ':created_at' => date('Y-m-d H:i:s'),
        ]);
        return (int)$pdo->lastInsertId();
    }

    public static function update(PDO $pdo, int $id, array $data): bool
    {
        $stmt = $pdo->prepare('UPDATE users SET name = :name, email = :email, role = :role, phone = :phone WHERE id = :id');
        return $stmt->execute([
            ':name' => $data['name'],
            ':email' => $data['email'],
            ':role' => $data['role'],
            ':phone' => $data['phone'] ?? null,
            ':id' => $id,
        ]);
    }

    public static function delete(PDO $pdo, int $id): bool
    {
        $stmt = $pdo->prepare('DELETE FROM users WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }
}

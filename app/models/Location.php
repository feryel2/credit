<?php

class Location
{
    public static function all(PDO $pdo): array
    {
        $stmt = $pdo->query('SELECT l.*, e.name as equipment_name, u.name as client_name FROM locations l LEFT JOIN equipments e ON e.id = l.equipment_id LEFT JOIN users u ON u.id = l.client_id ORDER BY l.id DESC');
        return $stmt->fetchAll();
    }

    public static function findById(PDO $pdo, int $id): ?array
    {
        $stmt = $pdo->prepare('SELECT l.*, e.name as equipment_name, u.name as client_name FROM locations l LEFT JOIN equipments e ON e.id = l.equipment_id LEFT JOIN users u ON u.id = l.client_id WHERE l.id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public static function create(PDO $pdo, array $data): int
    {
        $stmt = $pdo->prepare('INSERT INTO locations (equipment_id, client_id, start_date, end_date, duration_days, quantity, unit_price, total_amount, status, invoice_number, notes, return_date, return_condition, additional_fees, return_notes, created_at) VALUES (:equipment_id, :client_id, :start_date, :end_date, :duration_days, :quantity, :unit_price, :total_amount, :status, :invoice_number, :notes, :return_date, :return_condition, :additional_fees, :return_notes, :created_at)');
        $stmt->execute([
            ':equipment_id' => (int)$data['equipment_id'],
            ':client_id' => (int)$data['client_id'],
            ':start_date' => $data['start_date'],
            ':end_date' => $data['end_date'],
            ':duration_days' => (int)$data['duration_days'],
            ':quantity' => (int)($data['quantity'] ?? 1),
            ':unit_price' => (float)($data['unit_price'] ?? 0),
            ':total_amount' => (float)($data['total_amount'] ?? 0),
            ':status' => $data['status'] ?? 'pending',
            ':invoice_number' => $data['invoice_number'] ?? null,
            ':notes' => $data['notes'] ?? null,
            ':return_date' => $data['return_date'] ?? null,
            ':return_condition' => $data['return_condition'] ?? 'good',
            ':additional_fees' => (float)($data['additional_fees'] ?? 0),
            ':return_notes' => $data['return_notes'] ?? null,
            ':created_at' => date('Y-m-d H:i:s'),
        ]);
        return (int)$pdo->lastInsertId();
    }

    public static function update(PDO $pdo, int $id, array $data): bool
    {
        $stmt = $pdo->prepare('UPDATE locations SET equipment_id = :equipment_id, client_id = :client_id, start_date = :start_date, end_date = :end_date, duration_days = :duration_days, quantity = :quantity, unit_price = :unit_price, total_amount = :total_amount, status = :status, invoice_number = :invoice_number, notes = :notes, return_date = :return_date, return_condition = :return_condition, additional_fees = :additional_fees, return_notes = :return_notes WHERE id = :id');
        return $stmt->execute([
            ':equipment_id' => (int)$data['equipment_id'],
            ':client_id' => (int)$data['client_id'],
            ':start_date' => $data['start_date'],
            ':end_date' => $data['end_date'],
            ':duration_days' => (int)$data['duration_days'],
            ':quantity' => (int)($data['quantity'] ?? 1),
            ':unit_price' => (float)($data['unit_price'] ?? 0),
            ':total_amount' => (float)($data['total_amount'] ?? 0),
            ':status' => $data['status'] ?? 'pending',
            ':invoice_number' => $data['invoice_number'] ?? null,
            ':notes' => $data['notes'] ?? null,
            ':return_date' => $data['return_date'] ?? null,
            ':return_condition' => $data['return_condition'] ?? 'good',
            ':additional_fees' => (float)($data['additional_fees'] ?? 0),
            ':return_notes' => $data['return_notes'] ?? null,
            ':id' => $id,
        ]);
    }

    public static function delete(PDO $pdo, int $id): bool
    {
        $pdo->beginTransaction();
        $deletePayments = $pdo->prepare('DELETE FROM payments WHERE location_id = :id');
        $deletePayments->execute([':id' => $id]);
        $stmt = $pdo->prepare('DELETE FROM locations WHERE id = :id');
        $deleted = $stmt->execute([':id' => $id]);
        $pdo->commit();
        return $deleted;
    }
}

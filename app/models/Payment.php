<?php

class Payment
{
    public static function forLocation(PDO $pdo, int $locationId): array
    {
        $stmt = $pdo->prepare(
            'SELECT id, location_id, recorded_by, amount, payment_method, balance_after, paid_at
            FROM payments WHERE location_id = :location_id ORDER BY id DESC'
        );
        $stmt->execute([':location_id' => $locationId]);
        return $stmt->fetchAll();
    }

    public static function findForLocation(PDO $pdo, int $paymentId, int $locationId): ?array
    {
        $stmt = $pdo->prepare(
            'SELECT id, location_id, recorded_by, amount, payment_method, balance_after, paid_at
            FROM payments WHERE id = :id AND location_id = :location_id LIMIT 1'
        );
        $stmt->execute([':id' => $paymentId, ':location_id' => $locationId]);
        return $stmt->fetch() ?: null;
    }

    public static function totalForLocation(PDO $pdo, int $locationId): float
    {
        $stmt = $pdo->prepare('SELECT COALESCE(SUM(amount), 0) FROM payments WHERE location_id = :location_id');
        $stmt->execute([':location_id' => $locationId]);
        return round((float)$stmt->fetchColumn(), 2);
    }

    public static function create(PDO $pdo, int $locationId, int $recordedBy, float $amount, string $method, float $balanceAfter): int
    {
        $stmt = $pdo->prepare(
            'INSERT INTO payments (location_id, recorded_by, amount, payment_method, balance_after, paid_at)
            VALUES (:location_id, :recorded_by, :amount, :payment_method, :balance_after, :paid_at)'
        );
        $stmt->execute([
            ':location_id' => $locationId,
            ':recorded_by' => $recordedBy,
            ':amount' => round($amount, 2),
            ':payment_method' => $method,
            ':balance_after' => round($balanceAfter, 2),
            ':paid_at' => date('Y-m-d H:i:s'),
        ]);
        return (int)$pdo->lastInsertId();
    }
}

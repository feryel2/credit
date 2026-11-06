<?php

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/Equipment.php';
require_once __DIR__ . '/../models/Location.php';

class DashboardController extends Controller
{
    public function index(): void
    {
        $this->requireRole(['responsable_inventaire', 'agent_location']);

        $pdo = Database::getConnection();
        $equipments = Equipment::all($pdo);
        $locations = Location::all($pdo);
        $lowStock = Equipment::lowStock($pdo);
        $inventoryStatus = ['available' => 0, 'rented' => 0, 'maintenance' => 0, 'damaged' => 0];
        $availableUnits = 0;
        $inventoryByCategory = [];

        foreach ($equipments as $equipment) {
            $status = $equipment['status'] ?? '';
            if (isset($inventoryStatus[$status])) {
                $inventoryStatus[$status]++;
            }
            if ($status === 'available') {
                $availableUnits += (int)$equipment['stock_level'];
            }

            $categoryName = $equipment['category_name'] ?? 'Sans catégorie';
            $inventoryByCategory[$categoryName] = ($inventoryByCategory[$categoryName] ?? 0) + 1;
        }

        arsort($inventoryByCategory);
        $inventoryByCategory = array_slice($inventoryByCategory, 0, 5, true);
        $maxCategoryEquipment = max($inventoryByCategory ?: [1]);
        $categoryChart = [];
        foreach ($inventoryByCategory as $categoryName => $equipmentCount) {
            $categoryChart[] = [
                'name' => $categoryName,
                'count' => $equipmentCount,
                'width' => (int)round($equipmentCount / $maxCategoryEquipment * 100),
            ];
        }

        $locationStatus = ['pending' => 0, 'accepted' => 0, 'returned' => 0, 'rejected' => 0];
        foreach ($locations as $location) {
            if (isset($locationStatus[$location['status']])) {
                $locationStatus[$location['status']]++;
            }
        }

        $statusLabels = [
            'pending' => 'En attente',
            'accepted' => 'Validées',
            'returned' => 'Retournées',
            'rejected' => 'Refusées',
        ];
        $locationChart = [];
        foreach ($locationStatus as $status => $count) {
            $locationChart[] = [
                'status' => $status,
                'label' => $statusLabels[$status],
                'count' => $count,
                'url' => '/locations',
            ];
        }

        $monthStart = new DateTimeImmutable('first day of this month');
        $nextMonthStart = $monthStart->modify('+1 month');
        $sixMonthsAgo = $monthStart->modify('-5 months');
        $paymentSummary = $pdo->prepare(
            'SELECT COALESCE(SUM(amount), 0) AS total_collected,
                COALESCE(SUM(CASE WHEN paid_at >= :month_start AND paid_at < :next_month_start THEN amount ELSE 0 END), 0) AS collected_this_month
            FROM payments'
        );
        $paymentSummary->execute([
            ':month_start' => $monthStart->format('Y-m-d H:i:s'),
            ':next_month_start' => $nextMonthStart->format('Y-m-d H:i:s'),
        ]);
        $paymentSummary = $paymentSummary->fetch();

        $paymentsByMonth = $pdo->prepare(
            "SELECT strftime('%Y-%m', paid_at) AS month_key, SUM(amount) AS total
            FROM payments
            WHERE paid_at >= :start_date AND paid_at < :end_date
            GROUP BY month_key"
        );
        $paymentsByMonth->execute([
            ':start_date' => $sixMonthsAgo->format('Y-m-d H:i:s'),
            ':end_date' => $nextMonthStart->format('Y-m-d H:i:s'),
        ]);
        $collectedByMonth = [];
        foreach ($paymentsByMonth->fetchAll() as $row) {
            $collectedByMonth[$row['month_key']] = (float)$row['total'];
        }

        $monthNames = ['janv.', 'févr.', 'mars', 'avr.', 'mai', 'juin', 'juil.', 'août', 'sept.', 'oct.', 'nov.', 'déc.'];
        $paymentChart = [];
        for ($offset = 0; $offset < 6; $offset++) {
            $month = $sixMonthsAgo->modify('+' . $offset . ' months');
            $monthKey = $month->format('Y-m');
            $paymentChart[] = [
                'label' => $monthNames[(int)$month->format('n') - 1],
                'total' => $collectedByMonth[$monthKey] ?? 0.0,
            ];
        }
        $maxMonthlyCollection = max(array_column($paymentChart, 'total') ?: [0]);
        foreach ($paymentChart as &$month) {
            $month['height'] = $maxMonthlyCollection > 0
                ? max(5, (int)round($month['total'] / $maxMonthlyCollection * 100))
                : 5;
        }
        unset($month);

        $this->render('dashboard/index', [
            'equipments' => $equipments,
            'locations' => $locations,
            'lowStock' => $lowStock,
            'dashboardStats' => [
                'equipment_count' => count($equipments),
                'available_units' => $availableUnits,
                'pending_locations' => $locationStatus['pending'],
                'accepted_locations' => $locationStatus['accepted'],
                'returned_locations' => $locationStatus['returned'],
                'low_stock_count' => count($lowStock),
                'collected_total' => (float)$paymentSummary['total_collected'],
                'collected_this_month' => (float)$paymentSummary['collected_this_month'],
                'inventory_status' => $inventoryStatus,
                'category_chart' => $categoryChart,
                'location_chart' => $locationChart,
                'payment_chart' => $paymentChart,
            ],
        ]);
    }
}

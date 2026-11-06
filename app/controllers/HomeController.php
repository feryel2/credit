<?php

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/Equipment.php';
require_once __DIR__ . '/../models/Category.php';

class HomeController extends Controller
{
    public function index(): void
    {
        $pdo = Database::getConnection();
        $equipments = Equipment::search($pdo, ['status' => 'available']);
        $categories = Category::all($pdo);
        $categoryStats = [];
        $startingRate = null;

        foreach ($equipments as $equipment) {
            $categoryId = (int)($equipment['category_id'] ?? 0);
            $key = (string)$categoryId;
            if (!isset($categoryStats[$key])) {
                $categoryStats[$key] = [
                    'id' => $categoryId,
                    'name' => $categoryId > 0 ? ($equipment['category_name'] ?? 'Autres équipements') : 'Autres équipements',
                    'references' => 0,
                    'rate_total' => 0.0,
                ];
            }

            $dailyRate = (float)$equipment['daily_rate'];
            $categoryStats[$key]['references']++;
            $categoryStats[$key]['rate_total'] += $dailyRate;
            $startingRate = $startingRate === null ? $dailyRate : min($startingRate, $dailyRate);
        }

        foreach ($categoryStats as &$stats) {
            $stats['average_rate'] = $stats['rate_total'] / $stats['references'];
        }
        unset($stats);

        $popularCategories = array_values($categoryStats);
        usort($popularCategories, static function (array $left, array $right): int {
            return $right['references'] <=> $left['references'] ?: strcmp($left['name'], $right['name']);
        });
        $popularCategories = array_slice($popularCategories, 0, 4);
        $maxReferences = max(array_column($popularCategories, 'references') ?: [1]);
        foreach ($popularCategories as &$stats) {
            $stats['bar_width'] = (int)round($stats['references'] / $maxReferences * 100);
        }
        unset($stats);

        $priceCategories = array_values($categoryStats);
        usort($priceCategories, static function (array $left, array $right): int {
            return $right['average_rate'] <=> $left['average_rate'] ?: strcmp($left['name'], $right['name']);
        });
        $priceCategories = array_slice($priceCategories, 0, 4);
        $maxAverageRate = max(array_column($priceCategories, 'average_rate') ?: [1]);
        foreach ($priceCategories as &$stats) {
            $stats['bar_width'] = $maxAverageRate > 0 ? (int)round($stats['average_rate'] / $maxAverageRate * 100) : 0;
        }
        unset($stats);

        $this->render('home/index', [
            'equipments' => $equipments,
            'categories' => $categories,
            'homeStats' => [
                'reference_count' => count($equipments),
                'category_count' => count($categoryStats),
                'starting_rate' => $startingRate,
                'popular_categories' => $popularCategories,
                'price_categories' => $priceCategories,
            ],
        ]);
    }

    public function catalog(): void
    {
        $pdo = Database::getConnection();
        $categories = Category::all($pdo);
        $filters = [
            'query' => trim($_GET['query'] ?? ''),
            'category_id' => $_GET['category_id'] ?? '',
            'status' => 'available',
        ];
        $equipments = Equipment::search($pdo, $filters);
        $this->render('home/catalog', ['equipments' => $equipments, 'categories' => $categories, 'filters' => $filters]);
    }
}

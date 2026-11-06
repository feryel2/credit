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
        $this->render('home/index', ['equipments' => $equipments, 'categories' => $categories]);
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

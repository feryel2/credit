<?php

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/Equipment.php';
require_once __DIR__ . '/../models/Location.php';
require_once __DIR__ . '/../models/Category.php';

class DashboardController extends Controller
{
    public function index(): void
    {
        $this->requireRole(['responsable_inventaire', 'agent_location']);

        $pdo = Database::getConnection();
        $equipments = Equipment::all($pdo);
        $locations = Location::all($pdo);
        $categories = Category::all($pdo);
        $lowStock = Equipment::lowStock($pdo);

        $this->render('dashboard/index', [
            'equipments' => $equipments,
            'locations' => $locations,
            'categories' => $categories,
            'lowStock' => $lowStock,
        ]);
    }
}

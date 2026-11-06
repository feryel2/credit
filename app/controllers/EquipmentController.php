<?php

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/Equipment.php';
require_once __DIR__ . '/../models/Category.php';

class EquipmentController extends Controller
{
    private function validateEquipment(PDO $pdo, array $data): array
    {
        $errors = $this->validate([
            'name' => ['required'],
            'code' => ['required'],
            'daily_rate' => ['numeric'],
            'purchase_price' => ['numeric'],
            'stock_level' => ['int'],
            'alert_threshold' => ['int'],
        ], $data);

        if (!Category::findById($pdo, (int)$data['category_id'])) {
            $errors['category_id'][] = 'Sélectionnez une catégorie valide.';
        }
        foreach (['daily_rate' => 'Tarif journalier', 'purchase_price' => "Prix d'achat"] as $field => $label) {
            if (!is_numeric($data[$field]) || (float)$data[$field] < 0) {
                $errors[$field][] = $label . ' doit être un nombre positif ou nul.';
            }
        }
        foreach (['stock_level' => 'Stock', 'alert_threshold' => "Seuil d'alerte"] as $field => $label) {
            if (filter_var($data[$field], FILTER_VALIDATE_INT) === false || (int)$data[$field] < 0) {
                $errors[$field][] = $label . ' doit être un entier positif ou nul.';
            }
        }
        if (!in_array($data['status'], ['available', 'rented', 'maintenance', 'damaged'], true)) {
            $errors['status'][] = 'État de matériel invalide.';
        }

        return $errors;
    }

    public function index(): void
    {
        $this->requireRole(['responsable_inventaire', 'agent_location']);

        $pdo = Database::getConnection();
        $categories = Category::all($pdo);
        $filters = [
            'query' => trim($_GET['query'] ?? ''),
            'category_id' => $_GET['category_id'] ?? '',
            'status' => $_GET['status'] ?? '',
        ];
        $equipments = Equipment::search($pdo, $filters);
        $lowStock = Equipment::lowStock($pdo);
        $this->render('equipment/index', ['equipments' => $equipments, 'categories' => $categories, 'filters' => $filters, 'lowStock' => $lowStock]);
    }

    public function create(): void
    {
        $this->requireRole('responsable_inventaire');

        $pdo = Database::getConnection();
        $categories = Category::all($pdo);

        if ($this->isPost()) {
            $data = [
                'category_id' => (int)($_POST['category_id'] ?? 0),
                'name' => trim($_POST['name'] ?? ''),
                'code' => trim($_POST['code'] ?? ''),
                'serial_number' => trim($_POST['serial_number'] ?? ''),
                'purchase_price' => trim($_POST['purchase_price'] ?? 0),
                'daily_rate' => trim($_POST['daily_rate'] ?? 0),
                'status' => $_POST['status'] ?? 'available',
                'stock_level' => trim($_POST['stock_level'] ?? '0'),
                'alert_threshold' => trim($_POST['alert_threshold'] ?? '0'),
                'condition_note' => trim($_POST['condition_note'] ?? ''),
            ];

            $errors = $this->validateEquipment($pdo, $data);

            if ($errors) {
                $this->render('equipment/form', ['errors' => $errors, 'equipment' => $data, 'categories' => $categories, 'mode' => 'create']);
                return;
            }

            Equipment::create($pdo, $data);
            $this->redirect('/equipments');
        }

        $this->render('equipment/form', ['equipment' => [], 'categories' => $categories, 'mode' => 'create']);
    }

    public function edit(): void
    {
        $this->requireRole('responsable_inventaire');

        $id = (int)($_GET['id'] ?? 0);
        $pdo = Database::getConnection();
        $equipment = Equipment::findById($pdo, $id);
        $categories = Category::all($pdo);

        if (!$equipment) {
            $this->redirect('/equipments');
        }

        if ($this->isPost()) {
            $data = [
                'category_id' => (int)($_POST['category_id'] ?? 0),
                'name' => trim($_POST['name'] ?? ''),
                'code' => trim($_POST['code'] ?? ''),
                'serial_number' => trim($_POST['serial_number'] ?? ''),
                'purchase_price' => trim($_POST['purchase_price'] ?? 0),
                'daily_rate' => trim($_POST['daily_rate'] ?? 0),
                'status' => $_POST['status'] ?? 'available',
                'stock_level' => trim($_POST['stock_level'] ?? '0'),
                'alert_threshold' => trim($_POST['alert_threshold'] ?? '0'),
                'condition_note' => trim($_POST['condition_note'] ?? ''),
            ];

            $errors = $this->validateEquipment($pdo, $data);

            if ($errors) {
                $this->render('equipment/form', ['errors' => $errors, 'equipment' => $data, 'categories' => $categories, 'mode' => 'edit']);
                return;
            }

            Equipment::update($pdo, $id, $data);
            $this->redirect('/equipments');
        }

        $this->render('equipment/form', ['equipment' => $equipment, 'categories' => $categories, 'mode' => 'edit']);
    }

    public function delete(): void
    {
        $this->requireRole('responsable_inventaire');

        $id = (int)($_GET['id'] ?? 0);
        if ($id > 0) {
            $pdo = Database::getConnection();
            Equipment::delete($pdo, $id);
        }
        $this->redirect('/equipments');
    }
}

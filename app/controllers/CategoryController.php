<?php

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/Category.php';

class CategoryController extends Controller
{
    public function index(): void
    {
        $this->requireRole('responsable_inventaire');

        $pdo = Database::getConnection();
        $categories = $pdo->query(
            'SELECT c.*, COUNT(e.id) AS equipment_count,
                COALESCE(SUM(CASE WHEN e.status = "available" AND e.stock_level > 0 THEN 1 ELSE 0 END), 0) AS available_count
            FROM categories c
            LEFT JOIN equipments e ON e.category_id = c.id
            GROUP BY c.id
            ORDER BY c.id DESC'
        )->fetchAll();
        $this->render('categories/index', ['categories' => $categories]);
    }

    public function create(): void
    {
        $this->requireRole('responsable_inventaire');

        if ($this->isPost()) {
            $data = [
                'name' => trim($_POST['name'] ?? ''),
                'description' => trim($_POST['description'] ?? ''),
            ];

            $errors = $this->validate([
                'name' => ['required'],
            ], $data);

            if ($errors) {
                $this->render('categories/form', ['errors' => $errors, 'category' => $data, 'mode' => 'create']);
                return;
            }

            $pdo = Database::getConnection();
            Category::create($pdo, $data);
            $this->redirect('/categories');
        }

        $this->render('categories/form', ['mode' => 'create', 'category' => []]);
    }

    public function edit(): void
    {
        $this->requireRole('responsable_inventaire');

        $id = (int)($_GET['id'] ?? 0);
        $pdo = Database::getConnection();
        $category = Category::findById($pdo, $id);

        if (!$category) {
            $this->redirect('/categories');
        }

        if ($this->isPost()) {
            $data = [
                'name' => trim($_POST['name'] ?? ''),
                'description' => trim($_POST['description'] ?? ''),
            ];

            $errors = $this->validate([
                'name' => ['required'],
            ], $data);

            if ($errors) {
                $this->render('categories/form', ['errors' => $errors, 'category' => $data, 'mode' => 'edit']);
                return;
            }

            Category::update($pdo, $id, $data);
            $this->redirect('/categories');
        }

        $this->render('categories/form', ['mode' => 'edit', 'category' => $category]);
    }

    public function delete(): void
    {
        $this->requireRole('responsable_inventaire');

        $id = (int)($_GET['id'] ?? 0);
        if ($id > 0) {
            $pdo = Database::getConnection();
            Category::delete($pdo, $id);
        }
        $this->redirect('/categories');
    }
}

<?php

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/Category.php';

class CategoryController extends Controller
{
    public function index(): void
    {
        $pdo = Database::getConnection();
        $categories = Category::all($pdo);
        $this->render('categories/index', ['categories' => $categories]);
    }

    public function create(): void
    {
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
        $id = (int)($_GET['id'] ?? 0);
        if ($id > 0) {
            $pdo = Database::getConnection();
            Category::delete($pdo, $id);
        }
        $this->redirect('/categories');
    }
}

<?php

abstract class Controller
{
    protected function render(string $view, array $data = [], string $layout = 'main'): void
    {
        extract($data);
        $viewPath = __DIR__ . '/../../views/' . $view . '.php';
        $layoutPath = __DIR__ . '/../../views/layouts/' . $layout . '.php';

        if (!file_exists($layoutPath)) {
            require $viewPath;
            return;
        }

        require $layoutPath;
    }

    protected function currentUser(): ?array
    {
        return Session::get('user');
    }

    protected function requireAuth(): array
    {
        $user = $this->currentUser();

        if (!$user) {
            $this->redirect('/login');
        }

        return $user;
    }

    protected function requireRole(array|string $roles): array
    {
        $user = $this->requireAuth();
        $allowedRoles = is_array($roles) ? $roles : [$roles];

        if (!in_array($user['role'] ?? '', $allowedRoles, true)) {
            $this->redirect('/');
        }

        return $user;
    }

    protected function redirect(string $path): void
    {
        header('Location: ' . $path);
        exit;
    }

    protected function isPost(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    protected function validate(array $rules, array $data): array
    {
        $errors = [];
        foreach ($rules as $field => $ruleSet) {
            $value = $data[$field] ?? '';
            foreach ($ruleSet as $rule) {
                if ($rule === 'required' && trim((string)$value) === '') {
                    $errors[$field][] = 'Ce champ est obligatoire.';
                }
                if ($rule === 'email' && !empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $errors[$field][] = 'Adresse email invalide.';
                }
                if ($rule === 'numeric' && !empty($value) && !is_numeric($value)) {
                    $errors[$field][] = 'Valeur numérique attendue.';
                }
                if ($rule === 'int' && !empty($value) && !ctype_digit((string)$value)) {
                    $errors[$field][] = 'Valeur entière attendue.';
                }
            }
        }
        return $errors;
    }
}

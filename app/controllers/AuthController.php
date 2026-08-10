<?php

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../core/Session.php';
require_once __DIR__ . '/../models/User.php';

class AuthController extends Controller
{
    public function login(): void
    {
        if ($this->isPost()) {
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            $pdo = Database::getConnection();
            $user = User::findByEmail($pdo, $email);

            if ($user && password_verify($password, $user['password_hash'])) {
                Session::set('user', $user);
                $this->redirect('/');
            }

            $this->render('auth/login', ['error' => 'Identifiants invalides.']);
            return;
        }

        $this->render('auth/login');
    }

    public function logout(): void
    {
        Session::flush();
        $this->redirect('/login');
    }
}

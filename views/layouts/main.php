<?php
$authUser = Session::get('user');
$role = $authUser['role'] ?? '';
$requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$isAdminUser = in_array($role, ['responsable_inventaire', 'agent_location'], true);
$isAdminPage = $isAdminUser && !in_array($requestPath, ['/', '/home', '/catalog'], true);
$showAdminShell = $isAdminPage && $requestPath !== '/dashboard';
$isClientUser = ($role === 'client');
$showClientShell = $isClientUser && $requestPath !== '/login';
$hideTopbar = $isAdminPage || $showClientShell;
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Location d'équipements</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <?php if (!$hideTopbar): ?>
        <header class="topbar">
            <div class="container nav">
                <div class="brand">EquipRent</div>
                <nav>
                    <a href="/">Accueil</a>
                    <a href="/catalog">Catalogue</a>
                    <?php if ($authUser && $isAdminUser): ?>
                        <a href="/dashboard">Dashboard</a>
                        <a href="/equipments">Équipements</a>
                        <a href="/locations">Locations</a>
                        <?php if ($role === 'responsable_inventaire'): ?><a href="/categories">Catégories</a><?php endif; ?>
                    <?php elseif ($authUser): ?>
                        <a href="/locations">Mes locations</a>
                    <?php endif; ?>
                </nav>
                <?php if ($authUser): ?>
                    <div class="userbox"><span><?= htmlspecialchars($authUser['name']) ?> (<?= htmlspecialchars($role) ?>)</span><a href="/logout">Déconnexion</a></div>
                <?php else: ?>
                    <a href="/login">Connexion</a>
                <?php endif; ?>
            </div>
        </header>
    <?php endif; ?>

    <main class="container page<?= $showAdminShell ? ' page-admin' : '' ?><?= $showClientShell ? ' page-client' : '' ?>">
        <?php if ($showAdminShell): ?>
            <div class="dashboard-shell">
                <?php require __DIR__ . '/../partials/admin_sidebar.php'; ?>
                <div class="dashboard-main">
        <?php elseif ($showClientShell): ?>
            <div class="client-shell">
                <?php require __DIR__ . '/../partials/client_sidebar.php'; ?>
                <div class="client-main">
        <?php endif; ?>
        <?php if ($isClientUser && ($requestPath === '/' || $requestPath === '/home')): ?>
            <?php require __DIR__ . '/../home/client_dashboard.php'; ?>
        <?php else: ?>
            <?php require $viewPath; ?>
        <?php endif; ?>
        <?php if ($showAdminShell): ?>
                </div>
            </div>
        <?php elseif ($showClientShell): ?>
                </div>
            </div>
        <?php endif; ?>
    </main>

    <?php if (!$hideTopbar): ?>
        <footer class="footer"><div class="container">Plateforme de gestion de location d'équipements</div></footer>
    <?php endif; ?>
</body>
</html>

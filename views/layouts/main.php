<?php
$authUser = Session::get('user');
$role = $authUser['role'] ?? '';
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Location d'équipements</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <header class="topbar">
        <div class="container nav">
            <div class="brand">EquipRent</div>
            <nav>
                <a href="/">Accueil</a>
                <a href="/catalog">Catalogue</a>

                <?php if ($authUser): ?>
                    <?php if (in_array($role, ['responsable_inventaire', 'agent_location'], true)): ?>
                        <a href="/dashboard">Dashboard</a>
                        <a href="/equipments">Équipements</a>
                        <a href="/locations">Locations</a>
                        <?php if ($role === 'responsable_inventaire'): ?>
                            <a href="/categories">Catégories</a>
                        <?php endif; ?>
                    <?php else: ?>
                        <a href="/locations">Mes locations</a>
                    <?php endif; ?>
                <?php endif; ?>
            </nav>
            <?php if ($authUser): ?>
                <div class="userbox">
                    <span><?= htmlspecialchars($authUser['name']) ?> (<?= htmlspecialchars($role) ?>)</span>
                    <a href="/logout">Déconnexion</a>
                </div>
            <?php else: ?>
                <a href="/login">Connexion</a>
            <?php endif; ?>
        </div>
    </header>

    <main class="container page">
        <?php require $viewPath; ?>
    </main>

    <footer class="footer">
        <div class="container">Plateforme de gestion de location d'équipements</div>
    </footer>
</body>
</html>

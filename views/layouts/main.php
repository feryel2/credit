<?php
$authUser = Session::get('user');
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
                <a href="/equipments">Équipements</a>
                <a href="/locations">Locations</a>
                <a href="/categories">Catégories</a>
            </nav>
            <?php if ($authUser): ?>
                <div class="userbox">
                    <span><?= htmlspecialchars($authUser['name']) ?></span>
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

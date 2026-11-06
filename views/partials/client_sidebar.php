<?php
$currentUser = Session::get('user');
$requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$clientSection = match (true) {
    $requestPath === '/' || $requestPath === '/home' => 'home',
    $requestPath === '/catalog' => 'catalog',
    str_starts_with($requestPath, '/locations') => 'locations',
    default => '',
};
$clientLocationsCount = isset($clientStats['total']) ? (int)$clientStats['total'] : null;
?>
<aside class="dashboard-sidebar client-sidebar" aria-label="Navigation de l’espace client">
    <div class="dashboard-sidebar-brand">
        <span class="dashboard-brand-mark" aria-hidden="true">E</span>
        <span><strong>EquipRent</strong><small>ESPACE CLIENT</small></span>
    </div>
    <p class="dashboard-sidebar-label">MON ESPACE</p>
    <nav class="dashboard-side-nav" aria-label="Espace client">
        <a class="dashboard-side-link <?= $clientSection === 'home' ? 'is-active' : '' ?>" href="/" <?= $clientSection === 'home' ? 'aria-current="page"' : '' ?>><span aria-hidden="true">▦</span>Vue d’ensemble</a>
        <a class="dashboard-side-link <?= $clientSection === 'catalog' ? 'is-active' : '' ?>" href="/catalog" <?= $clientSection === 'catalog' ? 'aria-current="page"' : '' ?>><span aria-hidden="true">⌕</span>Catalogue</a>
        <a class="dashboard-side-link <?= $clientSection === 'locations' ? 'is-active' : '' ?>" href="/locations" <?= $clientSection === 'locations' ? 'aria-current="page"' : '' ?>><span aria-hidden="true">⇄</span>Mes locations<?php if ($clientLocationsCount !== null): ?> <small><?= $clientLocationsCount ?></small><?php endif; ?></a>
    </nav>
    <div class="dashboard-sidebar-bottom">
        <div class="dashboard-sidebar-user">
            <span class="dashboard-avatar" aria-hidden="true"><?= htmlspecialchars(mb_strtoupper(mb_substr($currentUser['name'] ?? 'C', 0, 1))) ?></span>
            <span><strong><?= htmlspecialchars($currentUser['name'] ?? 'Client') ?></strong><small>Espace personnel</small></span>
        </div>
        <a class="dashboard-logout" href="/logout">Se déconnecter <span aria-hidden="true">↗</span></a>
    </div>
</aside>

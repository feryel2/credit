<?php
$currentUser = Session::get('user');
$role = $currentUser['role'] ?? '';
$requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$adminSection = match (true) {
    $requestPath === '/dashboard' => 'dashboard',
    str_starts_with($requestPath, '/equipments') => 'equipments',
    str_starts_with($requestPath, '/locations') => 'locations',
    str_starts_with($requestPath, '/categories') => 'categories',
    default => '',
};
$isInventoryManager = $role === 'responsable_inventaire';
?>
<aside class="dashboard-sidebar" aria-label="Navigation de l’administration">
    <div class="dashboard-sidebar-brand">
        <span class="dashboard-brand-mark" aria-hidden="true">E</span>
        <span><strong>EquipRent</strong><small>ESPACE ÉQUIPE</small></span>
    </div>
    <p class="dashboard-sidebar-label">MENU PRINCIPAL</p>
    <nav class="dashboard-side-nav" aria-label="Administration">
        <a class="dashboard-side-link <?= $adminSection === 'dashboard' ? 'is-active' : '' ?>" href="/dashboard" <?= $adminSection === 'dashboard' ? 'aria-current="page"' : '' ?>><span aria-hidden="true">▦</span>Vue d’ensemble</a>
        <a class="dashboard-side-link <?= $adminSection === 'equipments' ? 'is-active' : '' ?>" href="/equipments" <?= $adminSection === 'equipments' ? 'aria-current="page"' : '' ?>><span aria-hidden="true">▣</span>Équipements<?php if (isset($dashboardStats['equipment_count'])): ?> <small><?= (int)$dashboardStats['equipment_count'] ?></small><?php endif; ?></a>
        <a class="dashboard-side-link <?= $adminSection === 'locations' ? 'is-active' : '' ?>" href="/locations" <?= $adminSection === 'locations' ? 'aria-current="page"' : '' ?>><span aria-hidden="true">⇄</span>Locations<?php if (isset($dashboardStats['pending_locations'])): ?> <small><?= (int)$dashboardStats['pending_locations'] ?></small><?php endif; ?></a>
        <?php if ($isInventoryManager): ?>
            <a class="dashboard-side-link <?= $adminSection === 'categories' ? 'is-active' : '' ?>" href="/categories" <?= $adminSection === 'categories' ? 'aria-current="page"' : '' ?>><span aria-hidden="true">◈</span>Catégories</a>
        <?php endif; ?>
        <a class="dashboard-side-link" href="/catalog"><span aria-hidden="true">⌕</span>Voir le catalogue</a>
    </nav>
    <div class="dashboard-sidebar-bottom">
        <div class="dashboard-sidebar-user">
            <span class="dashboard-avatar" aria-hidden="true"><?= htmlspecialchars(mb_strtoupper(mb_substr($currentUser['name'] ?? 'E', 0, 1))) ?></span>
            <span><strong><?= htmlspecialchars($currentUser['name'] ?? 'Équipe') ?></strong><small><?= $isInventoryManager ? 'Responsable inventaire' : 'Agent de location' ?></small></span>
        </div>
        <a class="dashboard-logout" href="/logout">Se déconnecter <span aria-hidden="true">↗</span></a>
    </div>
</aside>

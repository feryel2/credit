<?php
$canManageEquipment = (Session::get('user')['role'] ?? '') === 'responsable_inventaire';
$availableCount = count(array_filter($equipments, static fn (array $item): bool => $item['status'] === 'available' && (int)$item['stock_level'] > 0));
$maintenanceCount = count(array_filter($equipments, static fn (array $item): bool => in_array($item['status'], ['maintenance', 'damaged'], true)));
$statusLabels = ['available' => 'Disponible', 'rented' => 'En location', 'maintenance' => 'En maintenance', 'damaged' => 'Endommagé'];
?>
<header class="admin-page-heading">
    <div>
        <p class="dashboard-breadcrumb">INVENTAIRE <span>/</span> ÉQUIPEMENTS</p>
        <h1>Gestion des équipements</h1>
        <p>Consultez le stock, les tarifs et les alertes de votre parc.</p>
    </div>
    <?php if ($canManageEquipment): ?>
        <a class="button-link dashboard-primary-action" href="/equipments/create"><span aria-hidden="true">＋</span> Nouveau matériel</a>
    <?php endif; ?>
</header>

<section class="admin-summary-grid" aria-label="Résumé de l’inventaire">
    <article class="admin-summary-card"><span class="dashboard-kpi-icon dashboard-icon-blue" aria-hidden="true">▣</span><span class="admin-summary-label">Références trouvées</span><strong><?= count($equipments) ?></strong><small>Selon les filtres sélectionnés</small></article>
    <article class="admin-summary-card"><span class="dashboard-kpi-icon dashboard-icon-green" aria-hidden="true">✓</span><span class="admin-summary-label">Disponibles</span><strong><?= $availableCount ?></strong><small>Références louables</small></article>
    <article class="admin-summary-card"><span class="dashboard-kpi-icon dashboard-icon-amber" aria-hidden="true">!</span><span class="admin-summary-label">À surveiller</span><strong><?= count($lowStock) ?></strong><small>Au seuil d’alerte ou en dessous</small></article>
    <article class="admin-summary-card"><span class="dashboard-kpi-icon dashboard-icon-violet" aria-hidden="true">◷</span><span class="admin-summary-label">Maintenance / dommage</span><strong><?= $maintenanceCount ?></strong><small>Références hors service normal</small></article>
</section>

<section class="dashboard-panel admin-content-panel">
    <div class="dashboard-panel-heading">
        <div><h2>Parc matériel</h2><p>Recherchez et filtrez les références de l’inventaire.</p></div>
    </div>
    <form method="get" action="/equipments" class="admin-filter-form">
        <label class="sr-only" for="equipment-query">Recherche</label>
        <input id="equipment-query" type="search" name="query" placeholder="Nom, code ou numéro de série" value="<?= htmlspecialchars($filters['query'] ?? '') ?>">
        <label class="sr-only" for="equipment-category">Catégorie</label>
        <select id="equipment-category" name="category_id">
            <option value="">Toutes les catégories</option>
            <?php foreach ($categories as $category): ?>
                <option value="<?= (int)$category['id'] ?>" <?= ($filters['category_id'] ?? '') == $category['id'] ? 'selected' : '' ?>><?= htmlspecialchars($category['name']) ?></option>
            <?php endforeach; ?>
        </select>
        <label class="sr-only" for="equipment-status">État</label>
        <select id="equipment-status" name="status">
            <option value="">Tous les états</option>
            <option value="available" <?= ($filters['status'] ?? '') === 'available' ? 'selected' : '' ?>>Disponible</option>
            <option value="rented" <?= ($filters['status'] ?? '') === 'rented' ? 'selected' : '' ?>>En location</option>
            <option value="maintenance" <?= ($filters['status'] ?? '') === 'maintenance' ? 'selected' : '' ?>>En maintenance</option>
            <option value="damaged" <?= ($filters['status'] ?? '') === 'damaged' ? 'selected' : '' ?>>Endommagé</option>
        </select>
        <button type="submit">Filtrer</button>
    </form>

    <?php if (!empty($lowStock)): ?>
        <div class="admin-inline-alert">
            <strong><?= count($lowStock) ?> alerte(s) de stock</strong>
            <span><?php foreach (array_slice($lowStock, 0, 4) as $index => $item): ?><?= $index > 0 ? ' · ' : '' ?><?= htmlspecialchars($item['name']) ?> (<?= (int)$item['stock_level'] ?>/<?= (int)$item['alert_threshold'] ?>)<?php endforeach; ?></span>
            <a href="/equipments?status=available">Vérifier <span aria-hidden="true">→</span></a>
        </div>
    <?php endif; ?>

    <?php if ($equipments): ?>
        <div class="dashboard-table-wrap">
            <table class="dashboard-table admin-table">
                <thead><tr><th>Équipement</th><th>Catégorie</th><th>Code</th><th>État</th><th>Tarif / jour</th><th>Stock</th><?php if ($canManageEquipment): ?><th>Actions</th><?php endif; ?></tr></thead>
                <tbody>
                    <?php foreach ($equipments as $equipment): ?>
                        <?php $stockIsLow = (int)$equipment['stock_level'] <= (int)$equipment['alert_threshold']; ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($equipment['name']) ?></strong><small class="admin-table-subtext"><?= htmlspecialchars($equipment['serial_number'] ?? 'Sans numéro de série') ?></small></td>
                            <td><?= htmlspecialchars($equipment['category_name'] ?? 'Sans catégorie') ?></td>
                            <td><span class="admin-code"><?= htmlspecialchars($equipment['code']) ?></span></td>
                            <td><span class="status-tag status-<?= htmlspecialchars($equipment['status']) ?>"><?= htmlspecialchars($statusLabels[$equipment['status']] ?? $equipment['status']) ?></span></td>
                            <td><strong><?= number_format((float)$equipment['daily_rate'], 2, ',', ' ') ?> €</strong></td>
                            <td><span class="<?= $stockIsLow ? 'admin-stock-low' : 'admin-stock-ok' ?>"><?= (int)$equipment['stock_level'] ?> unité(s)</span><small class="admin-table-subtext">Seuil : <?= (int)$equipment['alert_threshold'] ?></small></td>
                            <?php if ($canManageEquipment): ?>
                                <td><div class="admin-row-actions"><a href="/equipments/edit?id=<?= (int)$equipment['id'] ?>">Modifier</a><a class="admin-delete-link" href="/equipments/delete?id=<?= (int)$equipment['id'] ?>" onclick="return confirm('Supprimer cet équipement ?')">Supprimer</a></div></td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="dashboard-empty-state">Aucun équipement ne correspond à ces filtres.</div>
    <?php endif; ?>
</section>

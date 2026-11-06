<?php
$equipmentTotal = array_sum(array_map(static fn (array $category): int => (int)$category['equipment_count'], $categories));
$availableTotal = array_sum(array_map(static fn (array $category): int => (int)$category['available_count'], $categories));
?>
<header class="admin-page-heading">
    <div>
        <p class="dashboard-breadcrumb">INVENTAIRE <span>/</span> ORGANISATION</p>
        <h1>Catégories</h1>
        <p>Structurez le catalogue et retrouvez rapidement vos familles de matériel.</p>
    </div>
    <a class="button-link dashboard-primary-action" href="/categories/create"><span aria-hidden="true">＋</span> Nouvelle catégorie</a>
</header>

<section class="admin-summary-grid admin-summary-grid-three" aria-label="Résumé des catégories">
    <article class="admin-summary-card"><span class="dashboard-kpi-icon dashboard-icon-blue" aria-hidden="true">◈</span><span class="admin-summary-label">Catégories</span><strong><?= count($categories) ?></strong><small>Familles dans le catalogue</small></article>
    <article class="admin-summary-card"><span class="dashboard-kpi-icon dashboard-icon-violet" aria-hidden="true">▣</span><span class="admin-summary-label">Références classées</span><strong><?= $equipmentTotal ?></strong><small>Équipements rattachés à une catégorie</small></article>
    <article class="admin-summary-card"><span class="dashboard-kpi-icon dashboard-icon-green" aria-hidden="true">✓</span><span class="admin-summary-label">Disponibles</span><strong><?= $availableTotal ?></strong><small>Équipements actuellement louables</small></article>
</section>

<section class="admin-category-grid" aria-label="Catégories du catalogue">
    <?php foreach ($categories as $category): ?>
        <article class="dashboard-panel admin-category-card">
            <div class="admin-category-card-top">
                <span class="admin-category-icon" aria-hidden="true">◈</span>
                <span class="admin-category-id">CAT-<?= str_pad((string)$category['id'], 3, '0', STR_PAD_LEFT) ?></span>
            </div>
            <h2><?= htmlspecialchars($category['name']) ?></h2>
            <p><?= htmlspecialchars($category['description'] ?: 'Aucune description renseignée.') ?></p>
            <div class="admin-category-metrics">
                <span><strong><?= (int)$category['equipment_count'] ?></strong> référence(s)</span>
                <span><strong><?= (int)$category['available_count'] ?></strong> disponible(s)</span>
            </div>
            <div class="admin-row-actions">
                <a href="/categories/edit?id=<?= (int)$category['id'] ?>">Modifier</a>
                <a class="admin-delete-link" href="/categories/delete?id=<?= (int)$category['id'] ?>" onclick="return confirm('Supprimer cette catégorie ?')">Supprimer</a>
            </div>
        </article>
    <?php endforeach; ?>
    <?php if (!$categories): ?>
        <div class="dashboard-panel dashboard-empty-state">Aucune catégorie n’est encore créée.</div>
    <?php endif; ?>
</section>

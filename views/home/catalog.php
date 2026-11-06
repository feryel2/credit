<?php
$currentUser = Session::get('user');
$availableCategories = count(array_unique(array_filter(array_column($equipments, 'category_id'))));
$startingRate = null;
foreach ($equipments as $equipment) {
    $rate = (float)$equipment['daily_rate'];
    $startingRate = $startingRate === null ? $rate : min($startingRate, $rate);
}
?>
<header class="admin-page-heading catalog-page-heading">
    <div>
        <p class="dashboard-breadcrumb"><?= ($currentUser['role'] ?? '') === 'client' ? 'ESPACE CLIENT' : 'EQUIRENT' ?> <span>/</span> CATALOGUE</p>
        <h1>Le matériel pour vos projets</h1>
        <p>Comparez les équipements et tarifs, puis envoyez votre demande de location.</p>
    </div>
</header>

<section class="admin-summary-grid catalog-summary-grid" aria-label="Résumé du catalogue">
    <article class="admin-summary-card"><span class="dashboard-kpi-icon dashboard-icon-blue" aria-hidden="true">▣</span><span class="admin-summary-label">Équipements trouvés</span><strong><?= count($equipments) ?></strong><small>Correspondant à votre recherche</small></article>
    <article class="admin-summary-card"><span class="dashboard-kpi-icon dashboard-icon-green" aria-hidden="true">◈</span><span class="admin-summary-label">Catégories</span><strong><?= $availableCategories ?></strong><small>Dans cette sélection</small></article>
    <article class="admin-summary-card"><span class="dashboard-kpi-icon dashboard-icon-violet" aria-hidden="true">€</span><span class="admin-summary-label">Tarif journalier dès</span><strong><?= $startingRate === null ? '—' : number_format($startingRate, 2, ',', ' ') . ' €' ?></strong><small>Prix affiché par équipement</small></article>
</section>

<section class="dashboard-panel admin-content-panel catalog-content-panel">
    <div class="dashboard-panel-heading">
        <div><h2>Parcourir les équipements</h2><p>Filtrez par mot-clé ou catégorie.</p></div>
    </div>
    <form method="get" action="/catalog" class="admin-filter-form catalog-filter-form">
        <label class="sr-only" for="catalog-query">Recherche d’équipement</label>
        <input id="catalog-query" type="search" name="query" placeholder="Caméra, projecteur, code..." value="<?= htmlspecialchars($filters['query'] ?? '') ?>">
        <label class="sr-only" for="catalog-category">Catégorie</label>
        <select id="catalog-category" name="category_id">
            <option value="">Toutes les catégories</option>
            <?php foreach ($categories as $category): ?>
                <option value="<?= (int)$category['id'] ?>" <?= ($filters['category_id'] ?? '') == $category['id'] ? 'selected' : '' ?>><?= htmlspecialchars($category['name']) ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit">Rechercher</button>
    </form>

    <?php if ($equipments): ?>
        <div class="catalog-product-grid">
            <?php foreach ($equipments as $equipment): ?>
                <article class="home-equipment-card catalog-product-card">
                    <?php require __DIR__ . '/../partials/equipment_visual.php'; ?>
                    <div class="home-equipment-content">
                        <p class="home-equipment-category"><?= htmlspecialchars($equipment['category_name'] ?? 'Équipement') ?></p>
                        <h3><?= htmlspecialchars($equipment['name']) ?></h3>
                        <div class="home-equipment-bottom">
                            <p><strong><?= number_format((float)$equipment['daily_rate'], 2, ',', ' ') ?> €</strong><span> / jour</span></p>
                            <span class="home-available"><span></span> Au catalogue</span>
                        </div>
                        <a class="home-equipment-link" href="<?= ($currentUser['role'] ?? '') === 'client' ? '/locations/create?equipment_id=' . (int)$equipment['id'] : '/login' ?>">
                            <?= ($currentUser['role'] ?? '') === 'client' ? 'Demander ce matériel' : 'Se connecter pour réserver' ?> <span aria-hidden="true">→</span>
                        </a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="admin-empty-state">
            <span aria-hidden="true">⌕</span>
            <strong>Aucun équipement trouvé</strong>
            <p>Modifiez votre recherche ou choisissez une autre catégorie.</p>
            <a class="button-link button-link-neutral" href="/catalog">Effacer les filtres</a>
        </div>
    <?php endif; ?>
</section>
<p class="dashboard-data-note catalog-data-note">Le tarif affiché est journalier. La disponibilité pour les dates demandées est confirmée après l’envoi de votre demande.</p>

<?php
$currentUser = Session::get('user');
$isInventoryManager = ($currentUser['role'] ?? '') === 'responsable_inventaire';
$equipmentStatusLabels = [
    'available' => 'Disponibles',
    'rented' => 'En location',
    'maintenance' => 'En maintenance',
    'damaged' => 'Endommagés',
];
$equipmentStatusClasses = [
    'available' => 'dashboard-bar-green',
    'rented' => 'dashboard-bar-blue',
    'maintenance' => 'dashboard-bar-amber',
    'damaged' => 'dashboard-bar-red',
];
$equipmentStatusTotal = max(1, count($equipments));
?>
<div class="dashboard-shell">
    <?php require __DIR__ . '/../partials/admin_sidebar.php'; ?>

    <div class="dashboard-main">
        <header class="dashboard-heading">
            <div>
                <p class="dashboard-breadcrumb">ESPACE ÉQUIPE <span>/</span> TABLEAU DE BORD</p>
                <h1>Bonjour <?= htmlspecialchars(explode(' ', trim($currentUser['name'] ?? ''))[0] ?? 'à vous') ?> <span aria-hidden="true">👋</span></h1>
                <p>Voici les indicateurs clés de votre activité aujourd’hui.</p>
            </div>
            <a class="button-link dashboard-primary-action" href="/locations">Gérer les locations <span aria-hidden="true">→</span></a>
        </header>

        <section class="dashboard-kpis" aria-label="Indicateurs clés">
            <article class="dashboard-kpi">
                <span class="dashboard-kpi-icon dashboard-icon-blue" aria-hidden="true">▣</span>
                <span class="dashboard-kpi-label">Références équipement</span>
                <strong><?= (int)$dashboardStats['equipment_count'] ?></strong>
                <span class="dashboard-kpi-foot"><?= (int)$dashboardStats['available_units'] ?> unités avec statut disponible</span>
            </article>
            <article class="dashboard-kpi">
                <span class="dashboard-kpi-icon dashboard-icon-amber" aria-hidden="true">◷</span>
                <span class="dashboard-kpi-label">Demandes à traiter</span>
                <strong><?= (int)$dashboardStats['pending_locations'] ?></strong>
                <a class="dashboard-kpi-foot dashboard-kpi-link" href="/locations">Consulter les demandes <span aria-hidden="true">→</span></a>
            </article>
            <article class="dashboard-kpi">
                <span class="dashboard-kpi-icon dashboard-icon-green" aria-hidden="true">↗</span>
                <span class="dashboard-kpi-label">Locations validées</span>
                <strong><?= (int)$dashboardStats['accepted_locations'] ?></strong>
                <span class="dashboard-kpi-foot"><?= (int)$dashboardStats['returned_locations'] ?> location(s) retournée(s)</span>
            </article>
            <article class="dashboard-kpi">
                <span class="dashboard-kpi-icon dashboard-icon-violet" aria-hidden="true">€</span>
                <span class="dashboard-kpi-label">Paiements enregistrés ce mois</span>
                <strong><?= number_format((float)$dashboardStats['collected_this_month'], 2, ',', ' ') ?> €</strong>
                <span class="dashboard-kpi-foot">Total enregistré : <?= number_format((float)$dashboardStats['collected_total'], 2, ',', ' ') ?> €</span>
            </article>
        </section>

        <?php if (!empty($lowStock)): ?>
            <section class="dashboard-alert">
                <span class="dashboard-alert-icon" aria-hidden="true">!</span>
                <div>
                    <strong><?= count($lowStock) ?> alerte(s) de stock à surveiller</strong>
                    <p><?php foreach (array_slice($lowStock, 0, 3) as $index => $item): ?><?= $index > 0 ? ' · ' : '' ?><?= htmlspecialchars($item['name']) ?> (<?= (int)$item['stock_level'] ?>)<?php endforeach; ?></p>
                </div>
                <a href="/equipments">Voir l’inventaire <span aria-hidden="true">→</span></a>
            </section>
        <?php endif; ?>

        <section class="dashboard-charts" aria-label="Statistiques d’activité">
            <article class="dashboard-panel">
                <div class="dashboard-panel-heading">
                    <div><h2>État du parc</h2><p><?= count($equipments) ?> références dans l’inventaire</p></div>
                    <a href="/equipments">Inventaire <span aria-hidden="true">↗</span></a>
                </div>
                <div class="dashboard-status-chart" role="img" aria-label="Répartition des équipements par statut">
                    <?php foreach ($dashboardStats['inventory_status'] as $status => $count): ?>
                        <?php if ($count > 0): ?>
                            <span class="<?= $equipmentStatusClasses[$status] ?>" style="width: <?= (int)round($count / $equipmentStatusTotal * 100) ?>%"></span>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
                <div class="dashboard-legend">
                    <?php foreach ($dashboardStats['inventory_status'] as $status => $count): ?>
                        <div><span class="dashboard-legend-dot <?= $equipmentStatusClasses[$status] ?>"></span><span><?= $equipmentStatusLabels[$status] ?></span><strong><?= (int)$count ?></strong></div>
                    <?php endforeach; ?>
                </div>
            </article>

            <article class="dashboard-panel">
                <div class="dashboard-panel-heading">
                    <div><h2>Demandes de location</h2><p>Suivi des demandes par statut</p></div>
                    <a href="/locations">Locations <span aria-hidden="true">↗</span></a>
                </div>
                <div class="dashboard-rental-status">
                    <?php foreach ($dashboardStats['location_chart'] as $status): ?>
                        <a class="dashboard-rental-status-item" href="<?= htmlspecialchars($status['url']) ?>">
                            <span class="status-tag status-<?= htmlspecialchars($status['status']) ?>"><?= htmlspecialchars($status['label']) ?></span>
                            <strong><?= (int)$status['count'] ?></strong>
                        </a>
                    <?php endforeach; ?>
                </div>
            </article>

            <article class="dashboard-panel dashboard-payment-panel">
                <div class="dashboard-panel-heading">
                    <div><h2>Encaissements enregistrés</h2><p>Suivi mensuel des paiements saisis</p></div>
                    <span class="dashboard-chart-period">6 derniers mois</span>
                </div>
                <div class="dashboard-month-chart" role="img" aria-label="Paiements enregistrés par mois sur les six derniers mois">
                    <?php foreach ($dashboardStats['payment_chart'] as $month): ?>
                        <div class="dashboard-month-column" title="<?= htmlspecialchars($month['label']) ?> : <?= number_format((float)$month['total'], 2, ',', ' ') ?> €">
                            <span class="dashboard-month-bar" style="height: <?= (int)$month['height'] ?>%"></span>
                            <strong><?= number_format((float)$month['total'], 0, ',', ' ') ?> €</strong>
                            <small><?= htmlspecialchars($month['label']) ?></small>
                        </div>
                    <?php endforeach; ?>
                </div>
                <p class="dashboard-chart-note">Montants issus des paiements enregistrés manuellement, sans transaction bancaire automatique.</p>
            </article>

            <article class="dashboard-panel">
                <div class="dashboard-panel-heading">
                    <div><h2>Références par catégorie</h2><p>Répartition du catalogue</p></div>
                    <a href="<?= $isInventoryManager ? '/categories' : '/equipments' ?>"><?= $isInventoryManager ? 'Catégories' : 'Inventaire' ?> <span aria-hidden="true">↗</span></a>
                </div>
                <?php if ($dashboardStats['category_chart']): ?>
                    <div class="dashboard-category-chart">
                        <?php foreach ($dashboardStats['category_chart'] as $category): ?>
                            <div class="dashboard-category-row">
                                <span><?= htmlspecialchars($category['name']) ?></span>
                                <span class="dashboard-category-track"><span style="width: <?= (int)$category['width'] ?>%"></span></span>
                                <strong><?= (int)$category['count'] ?></strong>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="dashboard-empty-state">Aucun équipement n’est encore enregistré.</p>
                <?php endif; ?>
            </article>
        </section>

        <section class="dashboard-panel dashboard-recent-panel">
            <div class="dashboard-panel-heading">
                <div><h2>Dernières demandes</h2><p>Les demandes récentes, tous statuts confondus</p></div>
                <a href="/locations">Toutes les locations <span aria-hidden="true">→</span></a>
            </div>
            <?php if ($locations): ?>
                <div class="dashboard-table-wrap">
                    <table class="dashboard-table">
                        <thead><tr><th>Client</th><th>Équipement</th><th>Période</th><th>Montant</th><th>Statut</th></tr></thead>
                        <tbody>
                            <?php foreach (array_slice($locations, 0, 5) as $location): ?>
                                <?php $locationStatusLabel = $statusLabels[$location['status']] ?? $location['status']; ?>
                                <tr>
                                    <td><?= htmlspecialchars($location['client_name'] ?? 'Client') ?></td>
                                    <td><?= htmlspecialchars($location['equipment_name'] ?? 'Équipement') ?></td>
                                    <td><?= htmlspecialchars($location['start_date'] ?? '') ?> → <?= htmlspecialchars($location['end_date'] ?? '') ?></td>
                                    <td><?= number_format((float)$location['total_amount'], 2, ',', ' ') ?> €</td>
                                    <td><span class="status-tag status-<?= htmlspecialchars($location['status']) ?>"><?= htmlspecialchars($locationStatusLabel) ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="dashboard-empty-state">Les nouvelles demandes apparaîtront ici.</p>
            <?php endif; ?>
        </section>
        <p class="dashboard-data-note">Les indicateurs sont calculés à partir des données actuellement enregistrées.</p>
    </div>
</div>

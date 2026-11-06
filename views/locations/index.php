<?php
$currentUser = Session::get('user');
$isClient = ($currentUser['role'] ?? '') === 'client';
$pendingCount = count(array_filter($locations, static fn (array $location): bool => $location['status'] === 'pending'));
$activeCount = count(array_filter($locations, static fn (array $location): bool => $location['status'] === 'accepted'));
$outstandingBalance = array_sum(array_map(static fn (array $location): float => (float)$location['balance_due'], $locations));
$statusLabels = ['pending' => 'En attente', 'accepted' => 'Validée', 'rejected' => 'Refusée', 'returned' => 'Retournée'];
?>
<header class="admin-page-heading">
    <div>
        <p class="dashboard-breadcrumb"><?= $isClient ? 'ESPACE CLIENT' : 'GESTION COMMERCIALE' ?> <span>/</span> LOCATIONS</p>
        <h1><?= $isClient ? 'Mes locations' : 'Demandes de location' ?></h1>
        <p><?= $isClient ? 'Retrouvez le statut, les documents et les paiements de vos locations.' : 'Suivez les demandes, les retours et les soldes de location.' ?></p>
    </div>
    <?php if (!$isClient): ?>
        <a class="button-link dashboard-primary-action" href="/locations/create"><span aria-hidden="true">＋</span> Nouvelle location</a>
    <?php endif; ?>
</header>

<section class="admin-summary-grid" aria-label="Résumé des locations">
    <article class="admin-summary-card"><span class="dashboard-kpi-icon dashboard-icon-blue" aria-hidden="true">⇄</span><span class="admin-summary-label"><?= $isClient ? 'Mes demandes' : 'Locations affichées' ?></span><strong><?= count($locations) ?></strong><small>Selon votre espace</small></article>
    <?php if (!$isClient): ?>
        <article class="admin-summary-card"><span class="dashboard-kpi-icon dashboard-icon-amber" aria-hidden="true">◷</span><span class="admin-summary-label">À traiter</span><strong><?= $pendingCount ?></strong><small>Demandes en attente</small></article>
    <?php endif; ?>
    <article class="admin-summary-card"><span class="dashboard-kpi-icon dashboard-icon-green" aria-hidden="true">✓</span><span class="admin-summary-label">En cours</span><strong><?= $activeCount ?></strong><small>Locations validées</small></article>
    <article class="admin-summary-card"><span class="dashboard-kpi-icon dashboard-icon-violet" aria-hidden="true">€</span><span class="admin-summary-label">Solde restant</span><strong><?= number_format($outstandingBalance, 2, ',', ' ') ?> €</strong><small><?= $isClient ? 'Solde de vos locations' : 'Sur les locations affichées' ?></small></article>
</section>

<section class="dashboard-panel admin-content-panel">
    <div class="dashboard-panel-heading">
        <div><h2>Suivi des dossiers</h2><p><?= $isClient ? 'Vos réservations et leurs documents associés.' : 'Demandes, validations, retours et paiements.' ?></p></div>
        <?php if (!$isClient): ?><span class="admin-table-count"><?= $pendingCount ?> en attente</span><?php endif; ?>
    </div>

    <?php if ($locations): ?>
        <div class="dashboard-table-wrap">
            <table class="dashboard-table admin-table admin-locations-table">
                <thead><tr><?php if (!$isClient): ?><th>Client</th><?php endif; ?><th>Matériel</th><th>Période</th><th>Montant / solde</th><th>Statut</th><th>Actions</th></tr></thead>
                <tbody>
                    <?php foreach ($locations as $location): ?>
                        <?php $canEdit = !$isClient || $location['status'] === 'pending'; ?>
                        <tr>
                            <?php if (!$isClient): ?><td><strong><?= htmlspecialchars($location['client_name'] ?? 'Client') ?></strong><small class="admin-table-subtext">Dossier #<?= (int)$location['id'] ?></small></td><?php endif; ?>
                            <td><strong><?= htmlspecialchars($location['equipment_name'] ?? 'Équipement') ?></strong><small class="admin-table-subtext"><?= (int)$location['quantity'] ?> unité(s)</small></td>
                            <td><?= htmlspecialchars($location['start_date'] ?? '') ?><small class="admin-table-subtext">au <?= htmlspecialchars($location['end_date'] ?? '') ?></small></td>
                            <td><strong><?= number_format((float)$location['total_amount'], 2, ',', ' ') ?> €</strong><small class="admin-table-subtext">Réglé <?= number_format((float)$location['paid_amount'], 2, ',', ' ') ?> € · Reste <?= number_format((float)$location['balance_due'], 2, ',', ' ') ?> €</small></td>
                            <td><span class="status-tag status-<?= htmlspecialchars($location['status']) ?>"><?= htmlspecialchars($statusLabels[$location['status']] ?? $location['status']) ?></span></td>
                            <td>
                                <div class="admin-location-actions">
                                    <?php if ($canEdit): ?><a href="/locations/edit?id=<?= (int)$location['id'] ?>"><?= !$isClient && $location['status'] === 'pending' ? 'Traiter' : 'Modifier' ?></a><?php endif; ?>
                                    <?php if (!$isClient): ?><a class="admin-delete-link" href="/locations/delete?id=<?= (int)$location['id'] ?>" onclick="return confirm('Supprimer ce dossier ?')">Supprimer</a><?php endif; ?>
                                    <?php if (in_array($location['status'], ['accepted', 'returned'], true)): ?>
                                        <a href="/locations/download?id=<?= (int)$location['id'] ?>&type=contract" download>Contrat</a>
                                        <a href="/locations/download?id=<?= (int)$location['id'] ?>&type=invoice" download>Facture</a>
                                        <?php if ($location['status'] === 'returned'): ?><a href="/locations/download?id=<?= (int)$location['id'] ?>&type=return_receipt" download>Retour PDF</a><?php endif; ?>
                                        <?php if ($isClient): ?>
                                            <?php foreach ($location['payments'] as $payment): ?><a href="/locations/download?id=<?= (int)$location['id'] ?>&type=receipt&payment_id=<?= (int)$payment['id'] ?>" download>Reçu #<?= (int)$payment['id'] ?></a><?php endforeach; ?>
                                        <?php elseif ($location['balance_due'] > 0): ?>
                                            <a href="/locations/payment?id=<?= (int)$location['id'] ?>">Enregistrer un paiement</a>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="admin-empty-state">
            <span aria-hidden="true">⇄</span>
            <strong>Aucune location pour le moment</strong>
            <p><?= $isClient ? 'Vos demandes apparaîtront ici après leur envoi.' : 'Les nouvelles demandes de location apparaîtront dans cette liste.' ?></p>
            <?php if (!$isClient): ?><a class="button-link" href="/locations/create">Créer une location</a><?php endif; ?>
        </div>
    <?php endif; ?>
</section>

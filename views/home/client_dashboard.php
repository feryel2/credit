<?php
$currentUser = Session::get('user');
$statusLabels = ['pending' => 'En attente', 'accepted' => 'Validée', 'rejected' => 'Refusée', 'returned' => 'Retournée'];
$greetingName = explode(' ', trim($currentUser['name'] ?? ''))[0] ?? 'Bonjour';
?>
<header class="client-welcome">
    <div>
        <p class="dashboard-breadcrumb">ESPACE CLIENT <span>/</span> VUE D’ENSEMBLE</p>
        <h1>Bonjour <?= htmlspecialchars($greetingName) ?> <span aria-hidden="true">👋</span></h1>
        <p>Retrouvez vos demandes et découvrez du matériel pour vos prochains projets.</p>
    </div>
    <a class="button-link dashboard-primary-action" href="/catalog">Explorer le catalogue <span aria-hidden="true">→</span></a>
</header>

<section class="client-welcome-banner">
    <div>
        <p class="home-eyebrow">VOTRE PROCHAIN PROJET COMMENCE ICI</p>
        <h2>Le bon matériel, au bon moment.</h2>
        <p>Parcourez les équipements disponibles, comparez les tarifs et envoyez votre demande en quelques étapes.</p>
        <a class="button-link" href="/catalog">Trouver un équipement <span aria-hidden="true">→</span></a>
    </div>
    <div class="client-banner-illustration" aria-hidden="true">
        <span class="client-banner-orbit client-banner-orbit-one"></span>
        <span class="client-banner-orbit client-banner-orbit-two"></span>
        <span class="client-banner-tool">✦</span>
        <span class="client-banner-tool client-banner-tool-small">＋</span>
    </div>
</section>

<section class="client-stats-grid" aria-label="Résumé de mes locations">
    <article class="dashboard-kpi"><span class="dashboard-kpi-icon dashboard-icon-blue" aria-hidden="true">⇄</span><span class="dashboard-kpi-label">Mes demandes</span><strong><?= (int)$clientStats['total'] ?></strong><a class="dashboard-kpi-foot dashboard-kpi-link" href="/locations">Voir mes locations <span aria-hidden="true">→</span></a></article>
    <article class="dashboard-kpi"><span class="dashboard-kpi-icon dashboard-icon-amber" aria-hidden="true">◷</span><span class="dashboard-kpi-label">En attente</span><strong><?= (int)$clientStats['pending'] ?></strong><span class="dashboard-kpi-foot">Demandes à confirmer par l’équipe</span></article>
    <article class="dashboard-kpi"><span class="dashboard-kpi-icon dashboard-icon-green" aria-hidden="true">✓</span><span class="dashboard-kpi-label">Locations validées</span><strong><?= (int)$clientStats['accepted'] ?></strong><span class="dashboard-kpi-foot">Consultez vos documents de location</span></article>
    <article class="dashboard-kpi"><span class="dashboard-kpi-icon dashboard-icon-violet" aria-hidden="true">€</span><span class="dashboard-kpi-label">Solde restant</span><strong><?= number_format((float)$clientStats['balance'], 2, ',', ' ') ?> €</strong><span class="dashboard-kpi-foot">Paiements enregistrés : <?= number_format((float)$clientStats['paid'], 2, ',', ' ') ?> €</span></article>
</section>

<section class="dashboard-panel client-recent-panel">
    <div class="dashboard-panel-heading">
        <div><h2>Mes dernières locations</h2><p>Retrouvez le statut et les documents de vos demandes récentes.</p></div>
        <a href="/locations">Tout voir <span aria-hidden="true">→</span></a>
    </div>
    <?php if ($clientLocations): ?>
        <div class="dashboard-table-wrap">
            <table class="dashboard-table client-locations-table">
                <thead><tr><th>Équipement</th><th>Période</th><th>Montant</th><th>Statut</th><th>Documents</th></tr></thead>
                <tbody>
                    <?php foreach ($clientLocations as $location): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($location['equipment_name'] ?? 'Équipement') ?></strong><small class="admin-table-subtext"><?= (int)$location['quantity'] ?> unité(s)</small></td>
                            <td><?= htmlspecialchars($location['start_date'] ?? '') ?><small class="admin-table-subtext">au <?= htmlspecialchars($location['end_date'] ?? '') ?></small></td>
                            <td><strong><?= number_format((float)$location['total_amount'], 2, ',', ' ') ?> €</strong></td>
                            <td><span class="status-tag status-<?= htmlspecialchars($location['status']) ?>"><?= htmlspecialchars($statusLabels[$location['status']] ?? $location['status']) ?></span></td>
                            <td>
                                <?php if (in_array($location['status'], ['accepted', 'returned'], true)): ?>
                                    <div class="admin-location-actions">
                                        <a href="/locations/download?id=<?= (int)$location['id'] ?>&type=contract" download>Contrat</a>
                                        <a href="/locations/download?id=<?= (int)$location['id'] ?>&type=invoice" download>Facture</a>
                                        <?php if ($location['status'] === 'returned'): ?><a href="/locations/download?id=<?= (int)$location['id'] ?>&type=return_receipt" download>Reçu de retour</a><?php endif; ?>
                                        <?php foreach ($location['payments'] as $payment): ?>
                                            <a href="/locations/download?id=<?= (int)$location['id'] ?>&type=receipt&payment_id=<?= (int)$payment['id'] ?>" download>Reçu #<?= (int)$payment['id'] ?></a>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <span class="admin-table-subtext">Après validation</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="admin-empty-state">
            <span aria-hidden="true">⇄</span>
            <strong>Votre première location commence ici</strong>
            <p>Explorez le catalogue et envoyez une demande lorsque vous avez trouvé le matériel adapté.</p>
            <a class="button-link" href="/catalog">Découvrir le catalogue</a>
        </div>
    <?php endif; ?>
</section>

<section class="client-discover-section">
    <div class="dashboard-panel-heading">
        <div><h2>Disponibles pour vos projets</h2><p>Une sélection d’équipements actuellement au catalogue.</p></div>
        <a href="/catalog">Voir tout le catalogue <span aria-hidden="true">→</span></a>
    </div>
    <?php if ($equipments): ?>
        <div class="client-product-grid">
            <?php foreach (array_slice($equipments, 0, 3) as $equipment): ?>
                <article class="home-equipment-card">
                    <?php require __DIR__ . '/../partials/equipment_visual.php'; ?>
                    <div class="home-equipment-content">
                        <p class="home-equipment-category"><?= htmlspecialchars($equipment['category_name'] ?? 'Équipement') ?></p>
                        <h3><?= htmlspecialchars($equipment['name']) ?></h3>
                        <div class="home-equipment-bottom">
                            <p><strong><?= number_format((float)$equipment['daily_rate'], 2, ',', ' ') ?> €</strong><span> / jour</span></p>
                            <span class="home-available"><span></span> Au catalogue</span>
                        </div>
                        <a class="home-equipment-link" href="/locations/create?equipment_id=<?= (int)$equipment['id'] ?>">Demander ce matériel <span aria-hidden="true">→</span></a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="dashboard-panel dashboard-empty-state"><strong>Aucun équipement disponible</strong><p>Le catalogue sera actualisé dès que du matériel sera disponible.</p></div>
    <?php endif; ?>
</section>

<p class="dashboard-data-note">Les disponibilités sont confirmées par l’équipe après l’envoi de votre demande.</p>

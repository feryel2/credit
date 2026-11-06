<section class="card">
    <h1>Tableau de bord</h1>
    <div class="grid">
        <div class="card">
            <h3>Équipements</h3>
            <p><?= count($equipments) ?> éléments</p>
        </div>
        <div class="card">
            <h3>Locations</h3>
            <p><?= count($locations) ?> demandes</p>
        </div>
        <div class="card">
            <h3>Catégories</h3>
            <p><?= count($categories) ?> catégories</p>
        </div>
    </div>
</section>

<?php if (!empty($lowStock)): ?>
    <section class="alert error stock-alert">
        <strong>Alertes de stock (<?= count($lowStock) ?>)</strong>
        <ul>
            <?php foreach ($lowStock as $item): ?>
                <li><?= htmlspecialchars($item['name']) ?> : <?= (int)$item['stock_level'] ?> unité(s), seuil d’alerte <?= (int)$item['alert_threshold'] ?></li>
            <?php endforeach; ?>
        </ul>
        <a href="/equipments">Consulter l’inventaire</a>
    </section>
<?php endif; ?>

<section class="card" style="margin-top: 1.5rem;">
    <h2>Équipements récents</h2>
    <table>
        <thead>
            <tr>
                <th>Nom</th>
                <th>Code</th>
                <th>Statut</th>
                <th>Tarif</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach (array_slice($equipments, 0, 5) as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['name']) ?></td>
                    <td><?= htmlspecialchars($item['code']) ?></td>
                    <td><span class="status-tag status-<?= strtolower(str_replace(' ', '-', $item['status'])) ?>"><?= htmlspecialchars(['available' => 'Disponible', 'rented' => 'En location', 'maintenance' => 'En maintenance', 'damaged' => 'Endommagé'][$item['status']] ?? $item['status']) ?></span></td>
                    <td><?= htmlspecialchars($item['daily_rate']) ?> €</td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>

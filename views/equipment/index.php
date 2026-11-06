<section class="card">
    <?php $canManageEquipment = (Session::get('user')['role'] ?? '') === 'responsable_inventaire'; ?>
    <div class="row" style="justify-content: space-between; margin-bottom: 1rem;">
        <h1>Gestion des équipements</h1>
        <?php if ($canManageEquipment): ?>
            <a href="/equipments/create"><button>Nouveau matériel</button></a>
        <?php endif; ?>
    </div>

    <form method="get" action="/equipments" class="form-grid" style="margin-bottom: 1rem;">
        <input type="text" name="query" placeholder="Recherche multicritère" value="<?= htmlspecialchars($filters['query'] ?? '') ?>">
        <select name="category_id">
            <option value="">Toutes catégories</option>
            <?php foreach ($categories as $category): ?>
                <option value="<?= (int)$category['id'] ?>" <?= ($filters['category_id'] ?? '') == $category['id'] ? 'selected' : '' ?>><?= htmlspecialchars($category['name']) ?></option>
            <?php endforeach; ?>
        </select>
        <select name="status">
            <option value="">Tous états</option>
            <option value="available" <?= ($filters['status'] ?? '') === 'available' ? 'selected' : '' ?>>Disponible</option>
            <option value="rented" <?= ($filters['status'] ?? '') === 'rented' ? 'selected' : '' ?>>En location</option>
            <option value="maintenance" <?= ($filters['status'] ?? '') === 'maintenance' ? 'selected' : '' ?>>En maintenance</option>
            <option value="damaged" <?= ($filters['status'] ?? '') === 'damaged' ? 'selected' : '' ?>>Endommagé</option>
        </select>
        <button type="submit">Rechercher</button>
    </form>

    <?php if (!empty($lowStock)): ?>
        <div class="alert error stock-alert">
            <strong>Attention : <?= count($lowStock) ?> matériel<?= count($lowStock) > 1 ? 's' : '' ?> au seuil d’alerte ou en dessous.</strong>
            <ul>
                <?php foreach ($lowStock as $item): ?>
                    <li><?= htmlspecialchars($item['name']) ?> — <?= (int)$item['stock_level'] ?> en stock, seuil <?= (int)$item['alert_threshold'] ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th>Nom</th>
                <th>Catégorie</th>
                <th>Code</th>
                <th>Statut</th>
                <th>Tarif</th>
                <th>Stock</th>
                <?php if ($canManageEquipment): ?><th>Actions</th><?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($equipments as $equipment): ?>
                <tr>
                    <td><?= htmlspecialchars($equipment['name']) ?></td>
                    <td><?= htmlspecialchars($equipment['category_name'] ?? '') ?></td>
                    <td><?= htmlspecialchars($equipment['code']) ?></td>
                    <td><span class="status-tag status-<?= strtolower(str_replace(' ', '-', $equipment['status'])) ?>"><?= htmlspecialchars(['available' => 'Disponible', 'rented' => 'En location', 'maintenance' => 'En maintenance', 'damaged' => 'Endommagé'][$equipment['status']] ?? $equipment['status']) ?></span></td>
                    <td><?= htmlspecialchars($equipment['daily_rate']) ?> €</td>
                    <td><?= (int)$equipment['stock_level'] ?> <small>(alerte à <?= (int)$equipment['alert_threshold'] ?>)</small></td>
                    <?php if ($canManageEquipment): ?>
                        <td>
                            <a href="/equipments/edit?id=<?= (int)$equipment['id'] ?>">Modifier</a> |
                            <a href="/equipments/delete?id=<?= (int)$equipment['id'] ?>" onclick="return confirm('Supprimer ?')">Supprimer</a>
                        </td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>

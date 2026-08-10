<section class="card">
    <div class="row" style="justify-content: space-between; margin-bottom: 1rem;">
        <h1>Gestion des équipements</h1>
        <a href="/equipments/create"><button>Nouveau matériel</button></a>
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

    <table>
        <thead>
            <tr>
                <th>Nom</th>
                <th>Catégorie</th>
                <th>Code</th>
                <th>Statut</th>
                <th>Tarif</th>
                <th>Stock</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($equipments as $equipment): ?>
                <tr>
                    <td><?= htmlspecialchars($equipment['name']) ?></td>
                    <td><?= htmlspecialchars($equipment['category_name'] ?? '') ?></td>
                    <td><?= htmlspecialchars($equipment['code']) ?></td>
                    <td><span class="status-tag status-<?= strtolower(str_replace(' ', '-', $equipment['status'])) ?>"><?= htmlspecialchars($equipment['status']) ?></span></td>
                    <td><?= htmlspecialchars($equipment['daily_rate']) ?> €</td>
                    <td><?= (int)$equipment['stock_level'] ?></td>
                    <td>
                        <a href="/equipments/edit?id=<?= (int)$equipment['id'] ?>">Modifier</a> |
                        <a href="/equipments/delete?id=<?= (int)$equipment['id'] ?>" onclick="return confirm('Supprimer ?')">Supprimer</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>

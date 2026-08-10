<section class="card">
    <h1><?= $mode === 'edit' ? 'Modifier l’équipement' : 'Créer un équipement' ?></h1>
    <?php if (!empty($errors)) : ?>
        <div class="alert error">
            <?php foreach ($errors as $field => $messages): ?>
                <?php foreach ($messages as $msg): ?>
                    <div><?= htmlspecialchars($msg) ?></div>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="post" action="/equipments/<?= $mode === 'edit' ? 'edit?id=' . (int)($_GET['id'] ?? 0) : 'create' ?>">
        <div class="form-grid">
            <div>
                <label>Catégorie</label>
                <select name="category_id">
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= (int)$category['id'] ?>" <?= (($equipment['category_id'] ?? '') == $category['id']) ? 'selected' : '' ?>><?= htmlspecialchars($category['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label>Nom</label>
                <input type="text" name="name" value="<?= htmlspecialchars($equipment['name'] ?? '') ?>" required>
            </div>
            <div>
                <label>Code</label>
                <input type="text" name="code" value="<?= htmlspecialchars($equipment['code'] ?? '') ?>" required>
            </div>
            <div>
                <label>N° série</label>
                <input type="text" name="serial_number" value="<?= htmlspecialchars($equipment['serial_number'] ?? '') ?>">
            </div>
            <div>
                <label>Prix d'achat</label>
                <input type="number" step="0.01" name="purchase_price" value="<?= htmlspecialchars($equipment['purchase_price'] ?? 0) ?>">
            </div>
            <div>
                <label>Tarif journalier</label>
                <input type="number" step="0.01" name="daily_rate" value="<?= htmlspecialchars($equipment['daily_rate'] ?? 0) ?>" required>
            </div>
            <div>
                <label>État</label>
                <select name="status">
                    <option value="available" <?= (($equipment['status'] ?? 'available') === 'available') ? 'selected' : '' ?>>Disponible</option>
                    <option value="rented" <?= (($equipment['status'] ?? '') === 'rented') ? 'selected' : '' ?>>En location</option>
                    <option value="maintenance" <?= (($equipment['status'] ?? '') === 'maintenance') ? 'selected' : '' ?>>En maintenance</option>
                    <option value="damaged" <?= (($equipment['status'] ?? '') === 'damaged') ? 'selected' : '' ?>>Endommagé</option>
                </select>
            </div>
            <div>
                <label>Stock</label>
                <input type="number" name="stock_level" value="<?= htmlspecialchars($equipment['stock_level'] ?? 0) ?>">
            </div>
            <div>
                <label>Seuil alerte</label>
                <input type="number" name="alert_threshold" value="<?= htmlspecialchars($equipment['alert_threshold'] ?? 0) ?>">
            </div>
            <div style="grid-column: 1 / -1;">
                <label>Note de condition</label>
                <textarea name="condition_note"><?= htmlspecialchars($equipment['condition_note'] ?? '') ?></textarea>
            </div>
        </div>
        <div class="row" style="margin-top: 1rem;">
            <button type="submit">Enregistrer</button>
            <a href="/equipments"><button type="button">Retour</button></a>
        </div>
    </form>
</section>

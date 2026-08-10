<section class="card">
    <h1><?= $mode === 'edit' ? 'Modifier la location' : 'Créer une location' ?></h1>
    <form method="post" action="/locations/<?= $mode === 'edit' ? 'edit?id=' . (int)($_GET['id'] ?? 0) : 'create' ?>">
        <div class="form-grid">
            <div>
                <label>Équipement</label>
                <select name="equipment_id">
                    <?php foreach ($equipments as $equipment): ?>
                        <option value="<?= (int)$equipment['id'] ?>" <?= (($location['equipment_id'] ?? '') == $equipment['id']) ? 'selected' : '' ?>><?= htmlspecialchars($equipment['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label>Client</label>
                <select name="client_id">
                    <?php foreach ($clients as $client): ?>
                        <option value="<?= (int)$client['id'] ?>" <?= (($location['client_id'] ?? '') == $client['id']) ? 'selected' : '' ?>><?= htmlspecialchars($client['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label>Date de début</label>
                <input type="date" name="start_date" value="<?= htmlspecialchars($location['start_date'] ?? '') ?>" required>
            </div>
            <div>
                <label>Date de fin</label>
                <input type="date" name="end_date" value="<?= htmlspecialchars($location['end_date'] ?? '') ?>" required>
            </div>
            <div>
                <label>Durée (jours)</label>
                <input type="number" name="duration_days" value="<?= htmlspecialchars($location['duration_days'] ?? 1) ?>">
            </div>
            <div>
                <label>Quantité</label>
                <input type="number" name="quantity" value="<?= htmlspecialchars($location['quantity'] ?? 1) ?>">
            </div>
            <div>
                <label>Prix unitaire</label>
                <input type="number" step="0.01" name="unit_price" value="<?= htmlspecialchars($location['unit_price'] ?? 0) ?>">
            </div>
            <div>
                <label>Montant total</label>
                <input type="number" step="0.01" name="total_amount" value="<?= htmlspecialchars($location['total_amount'] ?? 0) ?>">
            </div>
            <div>
                <label>Statut</label>
                <select name="status">
                    <option value="pending" <?= (($location['status'] ?? 'pending') === 'pending') ? 'selected' : '' ?>>En attente</option>
                    <option value="accepted" <?= (($location['status'] ?? '') === 'accepted') ? 'selected' : '' ?>>Validée</option>
                    <option value="returned" <?= (($location['status'] ?? '') === 'returned') ? 'selected' : '' ?>>Retourné</option>
                </select>
            </div>
            <div style="grid-column: 1 / -1;">
                <label>Notes</label>
                <textarea name="notes"><?= htmlspecialchars($location['notes'] ?? '') ?></textarea>
            </div>
        </div>
        <div class="row" style="margin-top: 1rem;">
            <button type="submit">Enregistrer</button>
            <a href="/locations"><button type="button">Retour</button></a>
        </div>
    </form>
</section>

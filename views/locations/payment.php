<section class="card form-page payment-page">
    <div class="form-cover form-cover-location">
        <div>
            <p class="form-eyebrow">LOCATION · PAIEMENT</p>
            <h1>Enregistrer un paiement</h1>
            <p>Cette opération enregistre un paiement reçu par le personnel; elle ne débite pas le client.</p>
        </div>
    </div>
    <?php if (!empty($errors)): ?>
        <div class="alert error">
            <?php foreach ($errors as $messages): ?>
                <?php foreach ($messages as $message): ?>
                    <div><?= htmlspecialchars($message) ?></div>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="payment-summary">
        <p><span>Location</span><strong>#<?= (int)$location['id'] ?> · <?= htmlspecialchars($location['equipment_name']) ?></strong></p>
        <p><span>Montant total dû</span><strong><?= number_format((float)$location['total_amount'], 2, ',', ' ') ?> €</strong></p>
        <p><span>Déjà réglé</span><strong><?= number_format($paidAmount, 2, ',', ' ') ?> €</strong></p>
        <p><span>Solde restant</span><strong><?= number_format($balanceDue, 2, ',', ' ') ?> €</strong></p>
    </div>

    <form method="post" class="form-fields" action="/locations/payment?id=<?= (int)$location['id'] ?>">
        <div class="form-grid">
            <div>
                <label for="amount">Montant reçu (€)</label>
                <input id="amount" type="number" name="amount" min="0.01" max="<?= htmlspecialchars((string)$balanceDue) ?>" step="0.01" value="<?= htmlspecialchars($_POST['amount'] ?? (string)$balanceDue) ?>" required>
            </div>
            <div>
                <label for="payment_method">Mode de paiement déclaré</label>
                <select id="payment_method" name="payment_method" required>
                    <option value="">Choisir un mode</option>
                    <option value="cash" <?= ($_POST['payment_method'] ?? '') === 'cash' ? 'selected' : '' ?>>Espèces</option>
                    <option value="card" <?= ($_POST['payment_method'] ?? '') === 'card' ? 'selected' : '' ?>>Carte</option>
                    <option value="transfer" <?= ($_POST['payment_method'] ?? '') === 'transfer' ? 'selected' : '' ?>>Virement</option>
                    <option value="other" <?= ($_POST['payment_method'] ?? '') === 'other' ? 'selected' : '' ?>>Autre</option>
                </select>
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" <?= $balanceDue <= 0 ? 'disabled' : '' ?>>Confirmer le paiement reçu</button>
            <a class="button-link button-link-neutral" href="/locations">Retour aux locations</a>
        </div>
    </form>
</section>

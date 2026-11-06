<section class="card form-page">
    <?php
        $currentUser = Session::get('user');
        $isClient = ($currentUser['role'] ?? '') === 'client';
        $isStaff = in_array($currentUser['role'] ?? '', ['responsable_inventaire', 'agent_location'], true);
        $workflowStatus = $workflowStatus ?? ($location['status'] ?? '');
    ?>
    <div class="form-cover form-cover-location">
        <div>
            <p class="form-eyebrow">LOCATION · RÉSERVATION</p>
            <h1><?= $mode === 'edit' ? 'Modifier la location' : 'Préparer une location' ?></h1>
            <p>Indiquez le matériel et la période souhaitée pour cette location.</p>
        </div>
    </div>
    <form method="post" class="form-fields" action="/locations/<?= $mode === 'edit' ? 'edit?id=' . (int)($_GET['id'] ?? 0) : 'create' ?>">
        <div class="form-grid">
            <div>
                <label for="equipment_id">Équipement</label>
                <select id="equipment_id" name="equipment_id" required>
                    <?php foreach ($equipments as $equipment): ?>
                        <option value="<?= (int)$equipment['id'] ?>" <?= (($location['equipment_id'] ?? '') == $equipment['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($equipment['name']) ?><?= $equipment['status'] !== 'available' ? ' — indisponible' : '' ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php if ($isClient): ?>
                <div>
                    <label>Demandeur</label>
                    <p class="form-static-value"><?= htmlspecialchars($currentUser['name']) ?></p>
                </div>
            <?php else: ?>
                <div>
                    <label for="client_id">Client</label>
                    <select id="client_id" name="client_id" required>
                        <?php foreach ($clients as $client): ?>
                            <option value="<?= (int)$client['id'] ?>" <?= (($location['client_id'] ?? '') == $client['id']) ? 'selected' : '' ?>><?= htmlspecialchars($client['name']) ?> (<?= htmlspecialchars($client['email']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <?php endif; ?>
            <div>
                <label for="start_date">Date de début</label>
                <input id="start_date" type="date" name="start_date" value="<?= htmlspecialchars($location['start_date'] ?? '') ?>" <?= $isClient ? 'min="' . date('Y-m-d') . '"' : '' ?> required>
            </div>
            <div>
                <label for="end_date">Date de fin</label>
                <input id="end_date" type="date" name="end_date" value="<?= htmlspecialchars($location['end_date'] ?? '') ?>" required>
            </div>
            <div>
                <label>Tarif journalier</label>
                <p class="form-static-value"><?= htmlspecialchars($selectedEquipment['daily_rate'] ?? '0.00') ?> € / jour <small>Tarif calculé à partir du catalogue</small></p>
            </div>
            <div>
                <label for="quantity">Quantité demandée</label>
                <input id="quantity" type="number" name="quantity" min="1" step="1" value="<?= htmlspecialchars($location['quantity'] ?? 1) ?>" required>
            </div>
            <?php if ($isStaff && $mode === 'edit'): ?>
                <div>
                    <label for="status">Décision / état de location</label>
                    <select id="status" name="status" required>
                        <?php if ($workflowStatus === 'pending'): ?>
                            <option value="pending" <?= ($location['status'] ?? '') === 'pending' ? 'selected' : '' ?>>En attente</option>
                            <option value="accepted" <?= ($location['status'] ?? '') === 'accepted' ? 'selected' : '' ?>>Valider la demande</option>
                            <option value="rejected" <?= ($location['status'] ?? '') === 'rejected' ? 'selected' : '' ?>>Refuser la demande</option>
                        <?php elseif ($workflowStatus === 'accepted'): ?>
                            <option value="accepted" <?= ($location['status'] ?? '') === 'accepted' ? 'selected' : '' ?>>Location validée</option>
                            <option value="returned" <?= ($location['status'] ?? '') === 'returned' ? 'selected' : '' ?>>Enregistrer le retour</option>
                        <?php endif; ?>
                    </select>
                </div>
            <?php endif; ?>
            <?php if ($isStaff && $workflowStatus === 'accepted'): ?>
                <div>
                    <label for="return_date">Date de retour (si retour enregistré)</label>
                    <input id="return_date" type="date" name="return_date" value="<?= htmlspecialchars($location['return_date'] ?? date('Y-m-d')) ?>" max="<?= date('Y-m-d') ?>">
                </div>
                <div>
                    <label for="return_condition">État constaté au retour</label>
                    <select id="return_condition" name="return_condition">
                        <option value="good" <?= (($location['return_condition'] ?? 'good') === 'good') ? 'selected' : '' ?>>Bon état</option>
                        <option value="partial" <?= (($location['return_condition'] ?? '') === 'partial') ? 'selected' : '' ?>>Partiellement endommagé</option>
                        <option value="damaged" <?= (($location['return_condition'] ?? '') === 'damaged') ? 'selected' : '' ?>>Endommagé</option>
                    </select>
                </div>
                <div style="grid-column: 1 / -1;">
                    <label for="return_notes">Notes de retour</label>
                    <textarea id="return_notes" name="return_notes"><?= htmlspecialchars($location['return_notes'] ?? '') ?></textarea>
                    <small>Frais automatiques : 0 % (bon état), 10 % (partiel) ou 25 % (endommagé) du montant de location.</small>
                </div>
            <?php endif; ?>
            <div style="grid-column: 1 / -1;">
                <label for="notes">Notes</label>
                <textarea id="notes" name="notes"><?= htmlspecialchars($location['notes'] ?? '') ?></textarea>
            </div>
        </div>
        <div class="form-actions">
            <button type="submit">
                <?php if ($isClient): ?>
                    <?= $mode === 'edit' ? 'Modifier ma demande' : 'Envoyer ma demande' ?>
                <?php elseif ($mode === 'create'): ?>
                    Enregistrer la location
                <?php else: ?>
                    Enregistrer et appliquer la décision
                <?php endif; ?>
            </button>
            <a class="button-link button-link-neutral" href="/locations">Annuler</a>
        </div>
    </form>
</section>

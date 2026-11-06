<section class="card">
    <?php $currentUser = Session::get('user'); $isClient = ($currentUser['role'] ?? '') === 'client'; ?>
    <div class="row" style="justify-content: space-between; margin-bottom: 1rem;">
        <h1><?= $isClient ? 'Mes locations' : 'Demandes de location' ?></h1>
        <?php if (!$isClient): ?>
            <a href="/locations/create"><button>Nouvelle location</button></a>
        <?php endif; ?>
    </div>
    <table>
        <thead>
            <tr>
                <th>Client</th>
                <th>Équipement</th>
                <th>Période</th>
                <th>Total</th>
                <th>Statut</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($locations as $location): ?>
                <tr>
                    <td><?= htmlspecialchars($location['client_name'] ?? '') ?></td>
                    <td><?= htmlspecialchars($location['equipment_name'] ?? '') ?></td>
                    <td><?= htmlspecialchars($location['start_date']) ?> → <?= htmlspecialchars($location['end_date']) ?></td>
                    <td>
                        <?= number_format((float)$location['total_amount'], 2, ',', ' ') ?> € (<?= (int)$location['quantity'] ?> unité(s))
                        <small class="payment-balance">Réglé : <?= number_format($location['paid_amount'], 2, ',', ' ') ?> € · Reste : <?= number_format($location['balance_due'], 2, ',', ' ') ?> €</small>
                    </td>
                    <td><span class="status-tag status-<?= strtolower(str_replace(' ', '-', $location['status'])) ?>"><?= htmlspecialchars(['pending' => 'En attente', 'accepted' => 'Validée', 'rejected' => 'Refusée', 'returned' => 'Retournée'][$location['status']] ?? $location['status']) ?></span></td>
                    <td>
                        <?php if (!$isClient || $location['status'] === 'pending'): ?>
                            <a href="/locations/edit?id=<?= (int)$location['id'] ?>">Modifier</a> |
                        <?php endif; ?>
                        <?php if (!$isClient): ?>
                            <a href="/locations/delete?id=<?= (int)$location['id'] ?>" onclick="return confirm('Supprimer ?')">Supprimer</a>
                        <?php endif; ?>
                        <?php if (in_array($location['status'], ['accepted', 'returned'], true)): ?>
                            <?php if (!$isClient): ?> | <?php endif; ?>
                            <a href="/locations/download?id=<?= (int)$location['id'] ?>&type=contract" download>Contrat PDF</a>
                            | <a href="/locations/download?id=<?= (int)$location['id'] ?>&type=invoice" download>Facture PDF</a>
                            <?php if ($location['status'] === 'returned'): ?>
                                | <a href="/locations/download?id=<?= (int)$location['id'] ?>&type=return_receipt" download>Reçu de retour</a>
                            <?php endif; ?>
                            <?php if ($isClient && !empty($location['payments'])): ?>
                                <?php foreach ($location['payments'] as $payment): ?>
                                    | <a href="/locations/download?id=<?= (int)$location['id'] ?>&type=receipt&payment_id=<?= (int)$payment['id'] ?>" download>Reçu paiement #<?= (int)$payment['id'] ?></a>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            <?php if (!$isClient && $location['balance_due'] > 0): ?>
                                | <a href="/locations/payment?id=<?= (int)$location['id'] ?>">Enregistrer un paiement</a>
                            <?php endif; ?>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>

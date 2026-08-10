<section class="card">
    <div class="row" style="justify-content: space-between; margin-bottom: 1rem;">
        <h1>Demandes de location</h1>
        <a href="/locations/create"><button>Nouvelle location</button></a>
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
                    <td><?= htmlspecialchars($location['total_amount']) ?> €</td>
                    <td><span class="status-tag status-<?= strtolower(str_replace(' ', '-', $location['status'])) ?>"><?= htmlspecialchars($location['status']) ?></span></td>
                    <td>
                        <a href="/locations/edit?id=<?= (int)$location['id'] ?>">Modifier</a> |
                        <a href="/locations/delete?id=<?= (int)$location['id'] ?>" onclick="return confirm('Supprimer ?')">Supprimer</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>

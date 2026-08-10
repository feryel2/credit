<section class="card">
    <div class="row" style="justify-content: space-between; margin-bottom: 1rem;">
        <h1>Catégories d'équipements</h1>
        <a href="/categories/create"><button>Nouvelle catégorie</button></a>
    </div>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Description</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($categories as $category): ?>
                <tr>
                    <td><?= (int)$category['id'] ?></td>
                    <td><?= htmlspecialchars($category['name']) ?></td>
                    <td><?= htmlspecialchars($category['description'] ?? '') ?></td>
                    <td>
                        <a href="/categories/edit?id=<?= (int)$category['id'] ?>">Modifier</a> |
                        <a href="/categories/delete?id=<?= (int)$category['id'] ?>" onclick="return confirm('Supprimer ?')">Supprimer</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>

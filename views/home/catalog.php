<section class="card">
    <h1>Catalogue d'équipements</h1>
    <form method="get" action="/catalog" class="form-grid" style="margin-bottom: 1rem;">
        <input type="text" name="query" placeholder="Recherche" value="<?= htmlspecialchars($filters['query'] ?? '') ?>">
        <select name="category_id">
            <option value="">Toutes catégories</option>
            <?php foreach ($categories as $category): ?>
                <option value="<?= (int)$category['id'] ?>" <?= ($filters['category_id'] ?? '') == $category['id'] ? 'selected' : '' ?>><?= htmlspecialchars($category['name']) ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit">Chercher</button>
    </form>

    <div class="grid">
        <?php foreach ($equipments as $equipment): ?>
            <article class="card">
                <h3><?= htmlspecialchars($equipment['name']) ?></h3>
                <p><?= htmlspecialchars($equipment['category_name'] ?? '') ?></p>
                <p><strong>Tarif :</strong> <?= htmlspecialchars($equipment['daily_rate']) ?> € / jour</p>
                <p><strong>État :</strong> <?= htmlspecialchars($equipment['status']) ?></p>
                <a href="/locations/create"><button type="button">Réserver</button></a>
            </article>
        <?php endforeach; ?>
    </div>
</section>

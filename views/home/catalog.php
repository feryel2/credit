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
            <?php
                $equipmentCategory = mb_strtolower($equipment['category_name'] ?? '');
                $equipmentImage = str_contains($equipmentCategory, 'vid') ? 'camera.jpg' : (str_contains($equipmentCategory, 'audio') ? 'audio.jpg' : 'atelier.jpg');
            ?>
            <article class="card catalog-card">
                <img class="catalog-photo" src="/assets/images/<?= $equipmentImage ?>" alt="<?= htmlspecialchars($equipment['name']) ?>" loading="lazy">
                <div class="catalog-card-content">
                    <p class="home-equipment-category"><?= htmlspecialchars($equipment['category_name'] ?? 'Équipement') ?></p>
                    <h2><?= htmlspecialchars($equipment['name']) ?></h2>
                    <p><strong>Tarif :</strong> <?= htmlspecialchars($equipment['daily_rate']) ?> € / jour</p>
                    <p><span class="status-tag status-available">Disponible</span></p>
                <?php $currentUser = Session::get('user'); ?>
                <?php if ($currentUser): ?>
                    <a class="button-link" href="/locations/create?equipment_id=<?= (int)$equipment['id'] ?>">Réserver</a>
                <?php else: ?>
                    <a class="button-link" href="/login">Connexion pour réserver</a>
                <?php endif; ?>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</section>

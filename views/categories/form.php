<section class="card">
    <h1><?= $mode === 'edit' ? 'Modifier la catégorie' : 'Créer une catégorie' ?></h1>
    <?php if (!empty($errors)) : ?>
        <div class="alert error">
            <?php foreach ($errors as $field => $messages): ?>
                <?php foreach ($messages as $msg): ?>
                    <div><?= htmlspecialchars($msg) ?></div>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="post" action="/categories/<?= $mode === 'edit' ? 'edit?id=' . (int)($_GET['id'] ?? 0) : 'create' ?>">
        <div class="form-grid">
            <div>
                <label>Nom</label>
                <input type="text" name="name" value="<?= htmlspecialchars($category['name'] ?? '') ?>" required>
            </div>
            <div>
                <label>Description</label>
                <textarea name="description"><?= htmlspecialchars($category['description'] ?? '') ?></textarea>
            </div>
        </div>
        <div class="row" style="margin-top: 1rem;">
            <button type="submit">Enregistrer</button>
            <a href="/categories"><button type="button">Retour</button></a>
        </div>
    </form>
</section>

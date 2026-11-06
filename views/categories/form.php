<section class="card form-page">
    <div class="form-cover form-cover-category">
        <div>
            <p class="form-eyebrow">INVENTAIRE · ORGANISATION</p>
            <h1><?= $mode === 'edit' ? 'Modifier la catégorie' : 'Créer une catégorie' ?></h1>
            <p>Organisez le catalogue pour aider chacun à trouver le bon équipement.</p>
        </div>
    </div>
    <?php if (!empty($errors)) : ?>
        <div class="alert error">
            <?php foreach ($errors as $field => $messages): ?>
                <?php foreach ($messages as $msg): ?>
                    <div><?= htmlspecialchars($msg) ?></div>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="post" class="form-fields" action="/categories/<?= $mode === 'edit' ? 'edit?id=' . (int)($_GET['id'] ?? 0) : 'create' ?>">
        <div class="form-grid">
            <div>
                <label>Nom</label>
                <input type="text" name="name" value="<?= htmlspecialchars($category['name'] ?? '') ?>" required>
            </div>
            <div>
                <label>Description
                <textarea name="description"><?= htmlspecialchars($category['description'] ?? '') ?></textarea>
                </label>
            </div>
        </div>
        <div class="form-actions">
            <button type="submit"><?= $mode === 'edit' ? 'Enregistrer les modifications' : 'Créer la catégorie' ?></button>
            <a class="button-link button-link-neutral" href="/categories">Annuler</a>
        </div>
    </form>
</section>

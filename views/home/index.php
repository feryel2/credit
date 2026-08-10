<section class="card">
    <h1>Bienvenue sur EquipRent</h1>
    <p>Plateforme de gestion de location d'équipements pour les clients, agents et responsables.</p>
    <div class="row">
        <a href="/catalog"><button>Voir le catalogue</button></a>
        <a href="/login"><button>Connexion</button></a>
    </div>
</section>

<section class="card" style="margin-top: 1.5rem;">
    <h2>Équipements disponibles</h2>
    <div class="grid">
        <?php foreach (array_slice($equipments, 0, 6) as $equipment): ?>
            <article class="card">
                <h3><?= htmlspecialchars($equipment['name']) ?></h3>
                <p><?= htmlspecialchars($equipment['category_name'] ?? '') ?></p>
                <p><?= htmlspecialchars($equipment['daily_rate']) ?> € / jour</p>
            </article>
        <?php endforeach; ?>
    </div>
</section>

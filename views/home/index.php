<section class="home-hero">
    <div class="home-hero-copy">
        <p class="home-eyebrow"><span class="home-eyebrow-dot"></span> EQUIRENT · LOCATION DE MATÉRIEL</p>
        <h1>Votre prochain projet commence avec le bon équipement.</h1>
        <p class="home-intro">Chantier, tournage ou événement : trouvez le matériel qu’il vous faut, consultez son tarif journalier et envoyez votre demande de location en quelques instants.</p>
        <form class="home-search" method="get" action="/catalog">
            <label class="sr-only" for="home-search-query">Que recherchez-vous&nbsp;?</label>
            <span class="home-search-icon" aria-hidden="true">⌕</span>
            <input id="home-search-query" type="search" name="query" placeholder="Ex. caméra, projecteur, matériel de chantier">
            <button type="submit">Rechercher</button>
        </form>
        <div class="home-actions">
            <a class="home-hero-secondary" href="/catalog">Explorer tout le catalogue <span aria-hidden="true">→</span></a>
        </div>
    </div>
    <div class="home-hero-visual">
        <img class="home-hero-photo" src="/assets/images/chantier.jpg" alt="Équipement professionnel sur un chantier">
        <div class="home-visual-note home-note-top">
            <span class="home-note-icon" aria-hidden="true">↗</span>
            <span><strong>Un projet en tête&nbsp;?</strong><small>Trouvez votre matériel</small></span>
        </div>
        <div class="home-visual-note home-note-bottom">
            <span class="home-note-check" aria-hidden="true">✓</span>
            <span><strong>Tarifs affichés</strong><small>à la journée, sur le catalogue</small></span>
        </div>
    </div>
</section>

<section class="home-promise-strip" aria-label="Les atouts de la location">
    <div><span class="promise-icon" aria-hidden="true">01</span><span><strong>Le bon choix</strong><small>Un catalogue classé par univers</small></span></div>
    <div><span class="promise-icon" aria-hidden="true">02</span><span><strong>Le juste tarif</strong><small>Un prix journalier visible</small></span></div>
    <div><span class="promise-icon" aria-hidden="true">03</span><span><strong>Une demande suivie</strong><small>Retrouvez son statut en ligne</small></span></div>
</section>

<section class="home-insights" aria-labelledby="home-insights-title">
    <div class="home-insights-heading">
        <div>
            <p class="home-eyebrow">LE CATALOGUE EN UN COUP D’ŒIL</p>
            <h2 id="home-insights-title">Des repères concrets pour choisir</h2>
        </div>
        <p>Explorez les équipements et comparez les tarifs journaliers par univers.</p>
    </div>

    <div class="home-insight-stats">
        <article class="home-insight-stat">
            <span class="home-insight-stat-icon" aria-hidden="true">▦</span>
            <span><strong><?= (int)$homeStats['reference_count'] ?></strong><small>références disponibles</small></span>
        </article>
        <article class="home-insight-stat">
            <span class="home-insight-stat-icon" aria-hidden="true">◈</span>
            <span><strong><?= (int)$homeStats['category_count'] ?></strong><small>univers à explorer</small></span>
        </article>
        <article class="home-insight-stat">
            <span class="home-insight-stat-icon" aria-hidden="true">€</span>
            <span>
                <strong>
                    <?php if ($homeStats['starting_rate'] !== null): ?>
                        <?= number_format((float)$homeStats['starting_rate'], 2, ',', ' ') ?> €
                    <?php else: ?>
                        —
                    <?php endif; ?>
                </strong>
                <small>tarif journalier dès</small>
            </span>
        </article>
    </div>

    <div class="home-insight-charts">
        <article class="home-insight-chart">
            <div class="home-chart-heading">
                <span class="home-chart-icon home-chart-icon-blue" aria-hidden="true">▥</span>
                <div>
                    <h3>Les équipements par univers</h3>
                    <p>Nombre de références proposées</p>
                </div>
            </div>
            <?php if ($homeStats['popular_categories']): ?>
                <div class="home-chart-list">
                    <?php foreach ($homeStats['popular_categories'] as $category): ?>
                        <?php $categoryUrl = $category['id'] > 0 ? '/catalog?category_id=' . (int)$category['id'] : '/catalog'; ?>
                        <a class="home-chart-row" href="<?= htmlspecialchars($categoryUrl) ?>">
                            <span class="home-chart-label"><?= htmlspecialchars($category['name']) ?></span>
                            <span class="home-chart-track" aria-hidden="true"><span class="home-chart-bar home-chart-bar-blue" style="width: <?= (int)$category['bar_width'] ?>%"></span></span>
                            <strong class="home-chart-value"><?= (int)$category['references'] ?></strong>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="home-chart-empty">Les catégories disponibles apparaîtront ici.</p>
            <?php endif; ?>
        </article>

        <article class="home-insight-chart">
            <div class="home-chart-heading">
                <span class="home-chart-icon home-chart-icon-green" aria-hidden="true">€</span>
                <div>
                    <h3>Comparez les tarifs moyens</h3>
                    <p>Prix journalier moyen par univers</p>
                </div>
            </div>
            <?php if ($homeStats['price_categories']): ?>
                <div class="home-chart-list">
                    <?php foreach ($homeStats['price_categories'] as $category): ?>
                        <?php $categoryUrl = $category['id'] > 0 ? '/catalog?category_id=' . (int)$category['id'] : '/catalog'; ?>
                        <a class="home-chart-row" href="<?= htmlspecialchars($categoryUrl) ?>">
                            <span class="home-chart-label"><?= htmlspecialchars($category['name']) ?></span>
                            <span class="home-chart-track" aria-hidden="true"><span class="home-chart-bar home-chart-bar-green" style="width: <?= (int)$category['bar_width'] ?>%"></span></span>
                            <strong class="home-chart-value"><?= number_format((float)$category['average_rate'], 2, ',', ' ') ?> €</strong>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="home-chart-empty">Les tarifs seront affichés dès qu’un équipement sera disponible.</p>
            <?php endif; ?>
        </article>
    </div>
    <p class="home-insight-note">Données calculées à partir des équipements indiqués comme disponibles. La disponibilité pour vos dates est confirmée lors du traitement de votre demande.</p>
</section>

<?php if ($categories): ?>
    <section class="home-section home-categories">
        <div class="home-section-heading">
            <div>
                <p class="home-eyebrow">LE MATÉRIEL POUR VOS IDÉES</p>
                <h2>Que cherchez-vous aujourd’hui&nbsp;?</h2>
            </div>
            <p>Parcourez les catégories et trouvez plus vite l’équipement adapté à votre projet.</p>
        </div>
        <div class="home-category-list">
            <?php foreach (array_slice($categories, 0, 4) as $category): ?>
                <?php
                    $categoryName = mb_strtolower($category['name']);
                    $categoryImage = str_contains($categoryName, 'vid') ? 'camera.jpg' : (str_contains($categoryName, 'audio') ? 'audio.jpg' : 'atelier.jpg');
                ?>
                <a class="home-category" href="/catalog?category_id=<?= (int)$category['id'] ?>">
                    <img src="/assets/images/<?= $categoryImage ?>" alt="" loading="lazy">
                    <span class="home-category-copy">
                        <span class="home-category-name"><?= htmlspecialchars($category['name']) ?></span>
                        <?php if (!empty($category['description'])): ?>
                            <span class="home-category-description"><?= htmlspecialchars($category['description']) ?></span>
                        <?php endif; ?>
                    </span>
                    <span class="home-category-arrow" aria-hidden="true">↗</span>
                </a>
            <?php endforeach; ?>
        </div>
    </section>
<?php endif; ?>

<section class="home-section">
    <div class="home-section-heading">
        <div>
            <p class="home-eyebrow">À DÉCOUVRIR</p>
            <h2>Du matériel prêt pour vos projets</h2>
        </div>
        <p>Consultez les tarifs et choisissez votre prochaine location.</p>
    </div>
    <?php if ($equipments): ?>
        <div class="home-equipment-grid">
            <?php foreach (array_slice($equipments, 0, 6) as $equipment): ?>
                <?php
                    $currentUser = Session::get('user');
                ?>
                <article class="home-equipment-card">
                    <?php require __DIR__ . '/../partials/equipment_visual.php'; ?>
                    <div class="home-equipment-content">
                        <p class="home-equipment-category"><?= htmlspecialchars($equipment['category_name'] ?? 'Équipement') ?></p>
                        <h3><?= htmlspecialchars($equipment['name']) ?></h3>
                        <div class="home-equipment-bottom">
                            <p><strong><?= htmlspecialchars($equipment['daily_rate']) ?> €</strong><span> / jour</span></p>
                            <span class="home-available"><span></span> Disponible</span>
                        </div>
                        <a class="home-equipment-link" href="<?= $currentUser ? '/locations/create?equipment_id=' . (int)$equipment['id'] : '/login' ?>">
                            <?= $currentUser ? 'Faire une demande' : 'Se connecter pour réserver' ?> <span aria-hidden="true">→</span>
                        </a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="home-empty">
            <h3>Le catalogue se prépare</h3>
            <p>Il n’y a pas d’équipement disponible pour le moment. Revenez bientôt découvrir nos offres.</p>
        </div>
    <?php endif; ?>
    <div class="home-centered-link">
        <a class="button-link button-link-neutral" href="/catalog">Voir tout le catalogue <span aria-hidden="true">→</span></a>
    </div>
</section>

<section class="home-editorial">
    <div class="home-editorial-photo">
        <img src="/assets/images/camera.jpg" alt="Matériel de tournage pour préparer un projet vidéo" loading="lazy">
        <span class="home-editorial-caption">Du matériel adapté à chaque idée</span>
    </div>
    <div class="home-editorial-copy">
        <p class="home-eyebrow">LOUEZ SELON VOS BESOINS</p>
        <h2>Pourquoi acheter quand votre projet a seulement besoin du bon outil&nbsp;?</h2>
        <p>Choisissez le matériel utile pour la durée de votre projet. Consultez les tarifs disponibles et adressez votre demande à notre équipe de location.</p>
        <ul class="home-value-list">
            <li><span aria-hidden="true">✓</span><span><strong>Choisissez en connaissance de cause</strong><small>Les équipements et tarifs sont présentés dans le catalogue.</small></span></li>
            <li><span aria-hidden="true">✓</span><span><strong>Demandez les dates qui vous conviennent</strong><small>La disponibilité est vérifiée lors du traitement de la demande.</small></span></li>
            <li><span aria-hidden="true">✓</span><span><strong>Gardez vos documents à portée de main</strong><small>Retrouvez les documents de location depuis votre espace.</small></span></li>
        </ul>
        <a class="button-link" href="/catalog">Trouver mon équipement <span aria-hidden="true">→</span></a>
    </div>
</section>

<section class="home-section home-how" id="how-it-works">
    <div class="home-section-heading">
        <div>
            <p class="home-eyebrow">EN TOUTE SIMPLICITÉ</p>
            <h2>Votre location, étape par étape</h2>
        </div>
        <p>Une démarche claire, de la recherche du matériel au suivi de votre demande.</p>
    </div>
    <div class="home-steps">
        <article class="home-step">
            <span class="home-step-number">01</span>
            <span class="home-step-icon" aria-hidden="true">⌕</span>
            <h3>Trouvez</h3>
            <p>Recherchez un équipement ou explorez nos catégories et tarifs.</p>
        </article>
        <article class="home-step">
            <span class="home-step-number">02</span>
            <span class="home-step-icon" aria-hidden="true">↗</span>
            <h3>Envoyez votre demande</h3>
            <p>Indiquez les dates et la quantité souhaitées. L’équipe vérifie la disponibilité.</p>
        </article>
        <article class="home-step">
            <span class="home-step-number">03</span>
            <span class="home-step-icon" aria-hidden="true">✓</span>
            <h3>Suivez votre location</h3>
            <p>Consultez le statut et téléchargez les documents de location depuis votre espace.</p>
        </article>
    </div>
</section>

<?php
    $projectCategoryLinks = ['work' => '/catalog', 'video' => '/catalog', 'audio' => '/catalog'];
    foreach ($categories as $category) {
        $categoryName = mb_strtolower($category['name']);
        $categoryLink = '/catalog?category_id=' . (int)$category['id'];
        if (str_contains($categoryName, 'trav') || str_contains($categoryName, 'site') || str_contains($categoryName, 'chant')) {
            $projectCategoryLinks['work'] = $categoryLink;
        } elseif (str_contains($categoryName, 'vid')) {
            $projectCategoryLinks['video'] = $categoryLink;
        } elseif (str_contains($categoryName, 'audio') || str_contains($categoryName, 'son')) {
            $projectCategoryLinks['audio'] = $categoryLink;
        }
    }
?>
<section class="home-projects">
    <div class="home-projects-heading">
        <p class="home-eyebrow">UN BESOIN, UN PROJET</p>
        <h2>Le matériel suit le rythme de vos idées.</h2>
        <p>Découvrez des équipements pour vos usages professionnels et créatifs.</p>
    </div>
    <div class="home-project-grid">
        <a class="home-project-card" href="<?= htmlspecialchars($projectCategoryLinks['work']) ?>">
            <img src="/assets/images/atelier.jpg" alt="" loading="lazy">
            <span><strong>Travaux &amp; chantier</strong><small>Équipez vos interventions</small></span>
            <span class="home-project-arrow" aria-hidden="true">↗</span>
        </a>
        <a class="home-project-card" href="<?= htmlspecialchars($projectCategoryLinks['video']) ?>">
            <img src="/assets/images/camera.jpg" alt="" loading="lazy">
            <span><strong>Image &amp; vidéo</strong><small>Préparez vos tournages</small></span>
            <span class="home-project-arrow" aria-hidden="true">↗</span>
        </a>
        <a class="home-project-card" href="<?= htmlspecialchars($projectCategoryLinks['audio']) ?>">
            <img src="/assets/images/audio.jpg" alt="" loading="lazy">
            <span><strong>Son &amp; événementiel</strong><small>Donnez de la voix à vos événements</small></span>
            <span class="home-project-arrow" aria-hidden="true">↗</span>
        </a>
    </div>
</section>

<section class="home-final-cta">
    <div>
        <p class="home-eyebrow">ON PASSE À L’ACTION&nbsp;?</p>
        <h2>Votre prochain projet n’attend que le bon équipement.</h2>
        <p>Explorez le catalogue et envoyez votre demande de location.</p>
    </div>
    <a class="button-link" href="/catalog">Découvrir le catalogue <span aria-hidden="true">→</span></a>
</section>

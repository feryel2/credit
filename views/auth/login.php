<section class="auth-layout">
    <div class="auth-photo">
        <span class="auth-photo-label">EQUIRENT · À VOTRE SERVICE</span>
        <h1>Vos projets méritent le bon matériel.</h1>
        <p>Retrouvez votre espace pour suivre vos locations et préparer la suite.</p>
    </div>
    <div class="card auth-form">
        <p class="form-eyebrow">ESPACE PERSONNEL</p>
        <h2>Heureux de vous revoir</h2>
        <p class="form-subtitle">Connectez-vous pour continuer.</p>
        <?php if (!empty($error)) : ?>
            <div class="alert error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="post" action="/login" class="form-fields">
            <label>Adresse email
                <input type="email" name="email" placeholder="nom@exemple.fr" autocomplete="username" required>
            </label>
            <label>Mot de passe
                <input type="password" name="password" placeholder="Votre mot de passe" autocomplete="current-password" required>
            </label>
            <button type="submit">Se connecter <span aria-hidden="true">→</span></button>
        </form>
        <details class="demo">
            <summary>Comptes de démonstration</summary>
            <ul>
                <li>admin@location.local / password123</li>
                <li>agent@location.local / password123</li>
                <li>client@location.local / password123</li>
            </ul>
        </details>
    </div>
</section>

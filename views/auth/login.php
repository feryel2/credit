<section class="card auth-form">
    <h1>Connexion</h1>
    <?php if (!empty($error)) : ?>
        <div class="alert error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="post" action="/login">
        <label>Adresse email
            <input type="email" name="email" required>
        </label>
        <label>Mot de passe
            <input type="password" name="password" required>
        </label>
        <button type="submit">Se connecter</button>
    </form>
    <div class="demo">
        <p>Comptes de démonstration :</p>
        <ul>
            <li>admin@location.local / password123</li>
            <li>agent@location.local / password123</li>
            <li>client@location.local / password123</li>
        </ul>
    </div>
</section>

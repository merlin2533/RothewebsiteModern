<?php declare(strict_types=1); ?>
<div class="login-card">
    <div class="login-card__brand">
        <span class="login-card__logo">ROTHE</span>
        <span class="login-card__sub">Transporte – Admin</span>
    </div>
    <h1 class="login-card__title">Anmeldung</h1>
    <?php if (!empty($error)): ?>
        <div class="flash flash--error" role="alert">
            <span><?= e($error) ?></span>
        </div>
    <?php endif; ?>
    <form method="post" action="/admin/login" novalidate>
        <?= \App\Core\Csrf::field() ?>
        <div class="form-group">
            <label class="form-label" for="login-username">Benutzername</label>
            <input
                class="form-input"
                type="text"
                id="login-username"
                name="username"
                autocomplete="username"
                required
                autofocus
            >
        </div>
        <div class="form-group">
            <label class="form-label" for="login-password">Passwort</label>
            <input
                class="form-input"
                type="password"
                id="login-password"
                name="password"
                autocomplete="current-password"
                required
            >
        </div>
        <button type="submit" class="btn-primary btn-block">Anmelden</button>
    </form>
</div>

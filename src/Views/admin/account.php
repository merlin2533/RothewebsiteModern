<?php declare(strict_types=1); ?>
<div class="page-head">
    <h1>Mein Konto</h1>
    <p class="page-head__sub">Eingeloggt als <strong><?= e($user['username'] ?? '') ?></strong></p>
</div>

<section class="card">
    <header class="card__head">
        <h2>Passwort ändern</h2>
        <p class="muted">Mindestens 12 Zeichen, mit Groß-, Kleinbuchstaben und Ziffer.</p>
    </header>

    <form method="post" action="/admin/account/password" autocomplete="off" novalidate>
        <?= \App\Core\Csrf::field() ?>

        <div class="form-group">
            <label class="form-label" for="current_password">Aktuelles Passwort</label>
            <input class="form-input" type="password" id="current_password" name="current_password"
                   autocomplete="current-password" required>
        </div>

        <div class="input-row">
            <div class="form-group">
                <label class="form-label" for="new_password">Neues Passwort</label>
                <input class="form-input" type="password" id="new_password" name="new_password"
                       autocomplete="new-password" minlength="12" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="confirm_password">Neues Passwort wiederholen</label>
                <input class="form-input" type="password" id="confirm_password" name="confirm_password"
                       autocomplete="new-password" minlength="12" required>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-primary">Passwort ändern</button>
        </div>
    </form>
</section>

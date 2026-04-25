<?php declare(strict_types=1); ?>
<div class="page-header">
    <h1 class="page-header__title">Dashboard</h1>
    <p class="page-header__sub">Willkommen zurück, <strong><?= e((\App\Core\Auth::user())['username'] ?? '') ?></strong>.</p>
</div>

<section class="stats-grid" aria-label="Statistiken">
    <div class="stat-card">
        <div class="stat-card__num"><?= (int) ($stats['pages'] ?? 0) ?></div>
        <div class="stat-card__label">Seiten</div>
        <a href="/admin/pages" class="stat-card__link">Verwalten →</a>
    </div>
    <div class="stat-card">
        <div class="stat-card__num"><?= (int) ($stats['vehicles'] ?? 0) ?></div>
        <div class="stat-card__label">Fahrzeuge</div>
        <a href="/admin/vehicles" class="stat-card__link">Verwalten →</a>
    </div>
    <div class="stat-card">
        <div class="stat-card__num"><?= (int) ($stats['services'] ?? 0) ?></div>
        <div class="stat-card__label">Leistungen</div>
        <a href="/admin/services" class="stat-card__link">Verwalten →</a>
    </div>
    <div class="stat-card">
        <div class="stat-card__num"><?= (int) ($stats['timeline'] ?? 0) ?></div>
        <div class="stat-card__label">Zeitstrahl-Einträge</div>
        <a href="/admin/timeline" class="stat-card__link">Verwalten →</a>
    </div>
    <div class="stat-card">
        <div class="stat-card__num"><?= (int) ($stats['media'] ?? 0) ?></div>
        <div class="stat-card__label">Medien</div>
        <a href="/admin/media" class="stat-card__link">Verwalten →</a>
    </div>
</section>

<section class="quick-access">
    <h2 class="section-title">Schnellzugriff</h2>
    <ul class="quick-links">
        <li><a href="/admin/pages" class="quick-link">
            <span class="quick-link__icon">📄</span>
            <span class="quick-link__text">Seiten bearbeiten</span>
        </a></li>
        <li><a href="/admin/vehicles" class="quick-link">
            <span class="quick-link__icon">🚛</span>
            <span class="quick-link__text">Fahrzeuge verwalten</span>
        </a></li>
        <li><a href="/admin/vehicles/new" class="quick-link">
            <span class="quick-link__icon">➕</span>
            <span class="quick-link__text">Neues Fahrzeug anlegen</span>
        </a></li>
        <li><a href="/admin/services" class="quick-link">
            <span class="quick-link__icon">⚙️</span>
            <span class="quick-link__text">Leistungen bearbeiten</span>
        </a></li>
        <li><a href="/admin/timeline" class="quick-link">
            <span class="quick-link__icon">🗓️</span>
            <span class="quick-link__text">Zeitstrahl pflegen</span>
        </a></li>
        <li><a href="/admin/media" class="quick-link">
            <span class="quick-link__icon">🖼️</span>
            <span class="quick-link__text">Medien-Bibliothek</span>
        </a></li>
        <li><a href="/admin/settings" class="quick-link">
            <span class="quick-link__icon">⚙️</span>
            <span class="quick-link__text">Einstellungen</span>
        </a></li>
        <li><a href="/" target="_blank" rel="noopener" class="quick-link">
            <span class="quick-link__icon">🌐</span>
            <span class="quick-link__text">Website ansehen</span>
        </a></li>
    </ul>
</section>

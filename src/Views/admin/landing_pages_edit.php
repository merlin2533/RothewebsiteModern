<?php
/** @var array|null $lp */
declare(strict_types=1);
$isNew = $lp === null;
$action = $isNew ? '/admin/landing-pages' : '/admin/landing-pages/' . (int) $lp['id'];
?>
<div class="page-head">
    <h1><?= $isNew ? 'Neue Landingpage' : 'Landingpage bearbeiten' ?></h1>
    <a class="btn-ghost" href="/admin/landing-pages">← Zurück</a>
</div>

<form method="post" action="<?= e($action) ?>" novalidate>
    <?= \App\Core\Csrf::field() ?>

    <div class="input-row">
        <div class="form-group">
            <label class="form-label" for="slug">Slug (URL-Pfad ohne führendes /)</label>
            <input class="form-input" type="text" id="slug" name="slug" required pattern="[a-z0-9-]+"
                   value="<?= e((string) ($lp['slug'] ?? '')) ?>">
        </div>
        <div class="form-group">
            <label class="form-label" for="kind">Typ</label>
            <select class="form-input" id="kind" name="kind">
                <?php foreach (['industry' => 'Branche', 'city' => 'Stadt/Region', 'campaign' => 'Kampagne'] as $k => $label): ?>
                    <option value="<?= e($k) ?>" <?= ($lp['kind'] ?? 'industry') === $k ? 'selected' : '' ?>><?= e($label) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label class="form-label" for="sort_order">Sortierung</label>
            <input class="form-input" type="number" id="sort_order" name="sort_order"
                   value="<?= e((string) ($lp['sort_order'] ?? 0)) ?>">
        </div>
    </div>

    <div class="form-group">
        <label class="form-label" for="title">Titel</label>
        <input class="form-input" type="text" id="title" name="title" required
               value="<?= e((string) ($lp['title'] ?? '')) ?>">
    </div>

    <div class="form-group">
        <label class="form-label" for="eyebrow">Eyebrow (Kicker über der Headline)</label>
        <input class="form-input" type="text" id="eyebrow" name="eyebrow"
               value="<?= e((string) ($lp['eyebrow'] ?? '')) ?>">
    </div>

    <div class="input-row">
        <div class="form-group">
            <label class="form-label" for="headline">Hero-Headline</label>
            <input class="form-input" type="text" id="headline" name="headline"
                   value="<?= e((string) ($lp['headline'] ?? '')) ?>">
        </div>
        <div class="form-group">
            <label class="form-label" for="cta_label">CTA-Label</label>
            <input class="form-input" type="text" id="cta_label" name="cta_label"
                   value="<?= e((string) ($lp['cta_label'] ?? 'Jetzt anfragen')) ?>">
        </div>
    </div>

    <div class="form-group">
        <label class="form-label" for="sub">Sub-Headline</label>
        <input class="form-input" type="text" id="sub" name="sub"
               value="<?= e((string) ($lp['sub'] ?? '')) ?>">
    </div>

    <div class="form-group">
        <label class="form-label" for="intro_html">Intro (HTML, kurz, oberhalb der Cards)</label>
        <textarea class="form-input" id="intro_html" name="intro_html" rows="4"><?= e((string) ($lp['intro_html'] ?? '')) ?></textarea>
    </div>

    <div class="form-group">
        <label class="form-label" for="body_html">Body (HTML, ausführlich, am Ende der Seite)</label>
        <textarea class="form-input" id="body_html" name="body_html" rows="10"><?= e((string) ($lp['body_html'] ?? '')) ?></textarea>
    </div>

    <div class="input-row">
        <div class="form-group">
            <label class="form-label" for="related_vehicles">Fahrzeug-Slugs (kommagetrennt)</label>
            <input class="form-input" type="text" id="related_vehicles" name="related_vehicles"
                   placeholder="tieflader-sattelauflieger,tautliner-sattelzug"
                   value="<?= e((string) ($lp['related_vehicles'] ?? '')) ?>">
        </div>
        <div class="form-group">
            <label class="form-label" for="related_services">Leistungen-Slugs (kommagetrennt)</label>
            <input class="form-input" type="text" id="related_services" name="related_services"
                   placeholder="maschinentransporte,lagerung"
                   value="<?= e((string) ($lp['related_services'] ?? '')) ?>">
        </div>
    </div>

    <div class="input-row">
        <div class="form-group">
            <label class="form-label" for="primary_phone">Override Telefon (für Call-Tracking)</label>
            <input class="form-input" type="text" id="primary_phone" name="primary_phone"
                   value="<?= e((string) ($lp['primary_phone'] ?? '')) ?>">
        </div>
        <div class="form-group">
            <label class="form-label" for="primary_email">Override E-Mail</label>
            <input class="form-input" type="text" id="primary_email" name="primary_email"
                   value="<?= e((string) ($lp['primary_email'] ?? '')) ?>">
        </div>
        <div class="form-group">
            <label class="form-label" for="region_name">Region (für Schema, optional)</label>
            <input class="form-input" type="text" id="region_name" name="region_name"
                   value="<?= e((string) ($lp['region_name'] ?? '')) ?>">
        </div>
    </div>

    <div class="form-group">
        <label class="form-label" for="meta_title">Meta-Title (50–60 Zeichen)</label>
        <input class="form-input" type="text" id="meta_title" name="meta_title" maxlength="80"
               value="<?= e((string) ($lp['meta_title'] ?? '')) ?>">
    </div>

    <div class="form-group">
        <label class="form-label" for="meta_description">Meta-Description (140–160 Zeichen)</label>
        <textarea class="form-input" id="meta_description" name="meta_description" rows="2" maxlength="200"><?= e((string) ($lp['meta_description'] ?? '')) ?></textarea>
    </div>

    <div class="form-group form-group--checkbox">
        <label>
            <input type="checkbox" name="is_published" value="1"
                   <?= ($lp === null || (int) $lp['is_published'] === 1) ? 'checked' : '' ?>>
            Veröffentlicht
        </label>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn-primary">Speichern</button>
    </div>
</form>

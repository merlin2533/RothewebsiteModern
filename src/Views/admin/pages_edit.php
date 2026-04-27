<?php declare(strict_types=1); ?>
<?php
$standardSlugs = ['home','ueber-uns','leistungen','fahrzeuge','karriere','kontakt','impressum','datenschutz'];
$isStandard = in_array($page['slug'] ?? '', $standardSlugs, true);
?>
<div class="page-header">
    <h1 class="page-header__title">Seite bearbeiten: <em><?= e($page['title'] ?? '') ?></em></h1>
    <a href="/admin/pages" class="btn-ghost btn-sm">← Zurück zur Liste</a>
</div>

<form method="post" action="/admin/pages/<?= (int) $page['id'] ?>">
    <?= \App\Core\Csrf::field() ?>

    <!-- Tabs -->
    <div class="tabs" role="tablist" aria-label="Formular-Bereiche">
        <button type="button" class="tabs__tab" data-tab="tab-content" aria-selected="true" role="tab" aria-controls="tab-content">Inhalt</button>
        <button type="button" class="tabs__tab" data-tab="tab-seo" aria-selected="false" role="tab" aria-controls="tab-seo">SEO</button>
        <button type="button" class="tabs__tab" data-tab="tab-images" aria-selected="false" role="tab" aria-controls="tab-images">Bilder</button>
    </div>

    <!-- Tab: Inhalt -->
    <div id="tab-content" class="tabs__panel" data-tab-panel role="tabpanel">
        <div class="form-group">
            <label class="form-label" for="page-slug">Slug (URL-Pfad)</label>
            <input
                class="form-input"
                type="text"
                id="page-slug"
                name="slug"
                value="<?= e($page['slug'] ?? '') ?>"
                <?= $isStandard ? 'readonly aria-describedby="slug-hint"' : '' ?>
            >
            <?php if ($isStandard): ?>
                <p class="form-hint" id="slug-hint">Standard-Slug – wird für interne Links verwendet und sollte nicht geändert werden.</p>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label class="form-label" for="page-title">Seitentitel (H1)</label>
            <input
                class="form-input"
                type="text"
                id="page-title"
                name="title"
                value="<?= e($page['title'] ?? '') ?>"
                required
            >
        </div>

        <div class="input-row">
            <div class="form-group">
                <label class="form-label" for="page-hero-headline">Hero-Überschrift</label>
                <input
                    class="form-input"
                    type="text"
                    id="page-hero-headline"
                    name="hero_headline"
                    value="<?= e($page['hero_headline'] ?? '') ?>"
                >
            </div>
            <div class="form-group">
                <label class="form-label" for="page-hero-sub">Hero-Unterzeile</label>
                <input
                    class="form-input"
                    type="text"
                    id="page-hero-sub"
                    name="hero_sub"
                    value="<?= e($page['hero_sub'] ?? '') ?>"
                >
            </div>
        </div>

        <div class="form-group">
            <label class="form-label" for="page-content">Inhalt (HTML)</label>
            <p class="form-hint">Erlaubte Tags: <code>p, h2, h3, h4, ul, ol, li, strong, em, a, br, blockquote</code></p>
            <textarea
                class="form-input form-textarea form-textarea--large"
                id="page-content"
                name="content_html"
                rows="20"
            ><?= e($page['content_html'] ?? '') ?></textarea>
        </div>

        <div class="input-row">
            <div class="form-group">
                <label class="form-label" for="page-sort">Reihenfolge</label>
                <input
                    class="form-input form-input--narrow"
                    type="number"
                    id="page-sort"
                    name="sort_order"
                    value="<?= (int) ($page['sort_order'] ?? 0) ?>"
                    min="0"
                >
            </div>
            <div class="form-group form-group--checkbox">
                <label class="form-label--checkbox">
                    <input
                        type="checkbox"
                        name="is_published"
                        value="1"
                        <?= (int) ($page['is_published'] ?? 1) === 1 ? 'checked' : '' ?>
                    >
                    Veröffentlicht
                </label>
            </div>
        </div>
    </div>

    <!-- Tab: SEO -->
    <div id="tab-seo" class="tabs__panel" data-tab-panel hidden role="tabpanel">
        <div class="form-group">
            <label class="form-label" for="page-meta-title">
                Meta-Titel
                <small class="counter-hint" data-counter-target="meta_title" data-min="50" data-max="60"></small>
            </label>
            <input
                class="form-input"
                type="text"
                id="page-meta-title"
                name="meta_title"
                value="<?= e($page['meta_title'] ?? '') ?>"
                maxlength="120"
                aria-describedby="meta-title-hint"
            >
            <p class="form-hint" id="meta-title-hint">Ideal: 50–60 Zeichen. Wird im Browser-Tab und Google-Suchergebnis angezeigt.</p>
        </div>

        <div class="form-group">
            <label class="form-label" for="page-meta-desc">
                Meta-Beschreibung
                <small class="counter-hint" data-counter-target="meta_description" data-min="140" data-max="160"></small>
            </label>
            <textarea
                class="form-input form-textarea"
                id="page-meta-desc"
                name="meta_description"
                rows="3"
                maxlength="300"
                aria-describedby="meta-desc-hint"
            ><?= e($page['meta_description'] ?? '') ?></textarea>
            <p class="form-hint" id="meta-desc-hint">Ideal: 140–160 Zeichen. Sichtbar im Google-Suchergebnis.</p>
        </div>
    </div>

    <!-- Tab: Bilder -->
    <div id="tab-images" class="tabs__panel" data-tab-panel hidden role="tabpanel">
        <?= \App\Core\View::partial('admin_media_picker', [
            'name'  => 'hero_image_id',
            'value' => $page['hero_image_id'] ?? null,
            'media' => $media,
            'label' => 'Hero-Bild',
        ]) ?>

        <?= \App\Core\View::partial('admin_media_picker', [
            'name'  => 'og_image_id',
            'value' => $page['og_image_id'] ?? null,
            'media' => $media,
            'label' => 'OG-Bild (Open Graph / Social Media) – mind. 1200 × 630 px',
        ]) ?>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn-primary">Speichern</button>
        <a href="/admin/pages" class="btn-ghost">Abbrechen</a>
    </div>
</form>

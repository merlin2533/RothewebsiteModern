<?php declare(strict_types=1); ?>
<div class="page-head">
    <div>
        <h1>Medien-Bibliothek</h1>
        <p class="page-head__sub">Originalbilder aus uploads/ + Web-Varianten unter assets/images/from-original/.</p>
    </div>
    <form method="post" action="/admin/media/sync" style="display:inline">
        <?= \App\Core\Csrf::field() ?>
        <button type="submit" class="btn-primary"
                data-confirm="Web-Varianten aus uploads/ neu generieren? Das kann ein paar Sekunden dauern.">
            Medien neu generieren
        </button>
    </form>
</div>

<!-- Upload-Form -->
<section class="upload-section">
    <h2 class="section-title">Bild hochladen</h2>
    <p class="form-hint">Akzeptiert: JPEG, PNG, WebP, SVG, GIF – max. 8 MB. <strong>Pflicht-Alt-Text für SEO/Barrierefreiheit.</strong></p>
    <form
        method="post"
        action="/admin/media/upload"
        enctype="multipart/form-data"
        class="upload-form"
    >
        <?= \App\Core\Csrf::field() ?>
        <div class="upload-form__fields">
            <div class="form-group">
                <label class="form-label" for="upload-file">Datei auswählen</label>
                <input
                    class="form-input"
                    type="file"
                    id="upload-file"
                    name="file"
                    accept="image/jpeg,image/png,image/webp,image/svg+xml,image/gif"
                    data-file-preview="upload-preview"
                    required
                >
                <img id="upload-preview" class="file-preview" src="" alt="Vorschau" hidden>
            </div>
            <div class="form-group">
                <label class="form-label" for="upload-alt">
                    Alt-Text
                    <span class="badge badge--required" aria-label="Pflichtfeld">Pflicht</span>
                </label>
                <input
                    class="form-input"
                    type="text"
                    id="upload-alt"
                    name="alt_text"
                    placeholder="Beschreibung des Bildes für Screenreader und SEO"
                    required
                    aria-describedby="alt-hint"
                >
                <p class="form-hint" id="alt-hint">Beschreibt den Bildinhalt präzise (z.B. „Tieflader-Sattelzug mit Maschinentransport auf Autobahn").</p>
            </div>
        </div>
        <button type="submit" class="btn-primary">Bild hochladen</button>
    </form>
</section>

<!-- Medien-Grid -->
<section class="media-section">
    <h2 class="section-title">Alle Bilder (<?= count($media) ?>)</h2>

    <?php if (empty($media)): ?>
        <p class="empty-row">Noch keine Bilder vorhanden.</p>
    <?php else: ?>
        <div class="media-grid">
            <?php foreach ($media as $m): ?>
            <div class="media-card">
                <div class="media-card__img-wrap">
                    <?php $mUrl = media_url($m); ?>
                    <?php if ($mUrl): ?>
                        <img
                            src="<?= e($mUrl) ?>"
                            alt="<?= e($m['alt_text'] ?? '') ?>"
                            class="media-card__img"
                            loading="lazy"
                            decoding="async"
                        >
                    <?php else: ?>
                        <div class="media-card__no-img" aria-hidden="true">🖼</div>
                    <?php endif; ?>
                </div>
                <div class="media-card__info">
                    <p class="media-card__filename" title="<?= e($m['filename'] ?? '') ?>"><?= e($m['original_name'] ?? $m['filename'] ?? '–') ?></p>
                    <?php if (!empty($m['width']) && !empty($m['height'])): ?>
                        <p class="media-card__meta"><?= (int) $m['width'] ?> × <?= (int) $m['height'] ?> px</p>
                    <?php endif; ?>
                    <?php if (!empty($m['size_bytes'])): ?>
                        <p class="media-card__meta"><?= round((int) $m['size_bytes'] / 1024) ?> kB</p>
                    <?php endif; ?>
                    <p class="media-card__id">ID: <?= (int) $m['id'] ?></p>
                </div>

                <!-- Alt-Text inline bearbeiten -->
                <form method="post" action="/admin/media/<?= (int) $m['id'] ?>/alt" class="media-card__alt-form">
                    <?= \App\Core\Csrf::field() ?>
                    <label class="form-label form-label--sm" for="alt-<?= (int) $m['id'] ?>">Alt-Text</label>
                    <div class="media-card__alt-row">
                        <input
                            class="form-input form-input--sm"
                            type="text"
                            id="alt-<?= (int) $m['id'] ?>"
                            name="alt_text"
                            value="<?= e($m['alt_text'] ?? '') ?>"
                            placeholder="Alt-Text eingeben..."
                        >
                        <button type="submit" class="btn-ghost btn-sm">OK</button>
                    </div>
                </form>

                <!-- Löschen -->
                <form method="post" action="/admin/media/<?= (int) $m['id'] ?>/delete" class="media-card__delete-form">
                    <?= \App\Core\Csrf::field() ?>
                    <button
                        type="submit"
                        class="btn-danger btn-sm"
                        data-confirm="Bild »<?= e($m['original_name'] ?? $m['filename'] ?? '') ?>« wirklich löschen? Es kann nicht rückgängig gemacht werden."
                    >Löschen</button>
                </form>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

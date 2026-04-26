<?php declare(strict_types=1); ?>
<?php $isNew = $vehicle === null; ?>
<div class="page-header">
    <h1 class="page-header__title"><?= $isNew ? 'Neues Fahrzeug' : ('Fahrzeug: ' . e($vehicle['name'])) ?></h1>
    <a href="/admin/vehicles" class="btn-ghost btn-sm">← Zurück zur Liste</a>
</div>

<form method="post" action="<?= $isNew ? '/admin/vehicles' : '/admin/vehicles/' . (int) $vehicle['id'] ?>">
    <?= \App\Core\Csrf::field() ?>

    <div class="form-section">
        <h2 class="form-section__title">Allgemein</h2>
        <div class="input-row">
            <div class="form-group">
                <label class="form-label" for="v-slug">Slug (URL-Pfad)</label>
                <input
                    class="form-input"
                    type="text"
                    id="v-slug"
                    name="slug"
                    value="<?= e($vehicle['slug'] ?? '') ?>"
                    data-slug-target
                    required
                >
            </div>
            <div class="form-group">
                <label class="form-label" for="v-name">Name</label>
                <input
                    class="form-input"
                    type="text"
                    id="v-name"
                    name="name"
                    value="<?= e($vehicle['name'] ?? '') ?>"
                    data-slug-source
                    required
                >
            </div>
        </div>

        <div class="form-group">
            <label class="form-label" for="v-marketing">Marketing-Text</label>
            <textarea
                class="form-input form-textarea"
                id="v-marketing"
                name="marketing_text"
                rows="5"
            ><?= e($vehicle['marketing_text'] ?? '') ?></textarea>
        </div>
    </div>

    <div class="form-section">
        <div class="warning-block" role="note" aria-label="Hinweis technische Daten">
            <span class="warning-block__icon" aria-hidden="true">⚠</span>
            <div>
                <strong>Technische Daten</strong> – diese Werte stammen aus den Originaldaten. Bitte nur in Rücksprache anpassen, da sie auf Datenblättern und Angeboten gleichbleiben müssen.
            </div>
        </div>

        <h2 class="form-section__title">Technische Daten</h2>

        <div class="input-row">
            <div class="form-group">
                <label class="form-label" for="v-length">Länge (m)</label>
                <input
                    class="form-input"
                    type="number"
                    id="v-length"
                    name="length_m"
                    value="<?= e((string) ($vehicle['length_m'] ?? '')) ?>"
                    step="0.01"
                    min="0"
                >
            </div>
            <div class="form-group">
                <label class="form-label" for="v-width">Breite (m)</label>
                <input
                    class="form-input"
                    type="number"
                    id="v-width"
                    name="width_m"
                    value="<?= e((string) ($vehicle['width_m'] ?? '')) ?>"
                    step="0.01"
                    min="0"
                >
            </div>
            <div class="form-group">
                <label class="form-label" for="v-height">Höhe (m)</label>
                <input
                    class="form-input"
                    type="number"
                    id="v-height"
                    name="height_m"
                    value="<?= e((string) ($vehicle['height_m'] ?? '')) ?>"
                    step="0.01"
                    min="0"
                >
            </div>
        </div>

        <div class="input-row">
            <div class="form-group">
                <label class="form-label" for="v-payload">Nutzlast (kg)</label>
                <input
                    class="form-input"
                    type="number"
                    id="v-payload"
                    name="payload_kg"
                    value="<?= e((string) ($vehicle['payload_kg'] ?? '')) ?>"
                    min="0"
                >
            </div>
            <div class="form-group">
                <label class="form-label" for="v-pallets">Euro-Paletten</label>
                <input
                    class="form-input"
                    type="number"
                    id="v-pallets"
                    name="euro_pallets"
                    value="<?= e((string) ($vehicle['euro_pallets'] ?? '')) ?>"
                    min="0"
                >
            </div>
            <div class="form-group">
                <label class="form-label" for="v-axles">Achsen</label>
                <input
                    class="form-input"
                    type="number"
                    id="v-axles"
                    name="axles"
                    value="<?= e((string) ($vehicle['axles'] ?? '')) ?>"
                    min="0"
                >
            </div>
        </div>

        <div class="form-group">
            <label class="form-label" for="v-features">Besondere Merkmale</label>
            <textarea
                class="form-input form-textarea"
                id="v-features"
                name="special_features"
                rows="3"
            ><?= e($vehicle['special_features'] ?? '') ?></textarea>
        </div>
    </div>

    <div class="form-section">
        <h2 class="form-section__title">Bild &amp; Status</h2>

        <div class="form-group">
            <label class="form-label" for="v-image">Fahrzeugbild</label>
            <select class="form-input form-select" id="v-image" name="image_id">
                <option value="">— kein Bild —</option>
                <?php foreach ($media as $m): ?>
                    <option value="<?= (int) $m['id'] ?>" <?= (int) ($vehicle['image_id'] ?? 0) === (int) $m['id'] ? 'selected' : '' ?>>
                        <?= e($m['original_name'] ?? $m['filename']) ?><?= !empty($m['alt_text']) ? ' – ' . e($m['alt_text']) : '' ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="input-row">
            <div class="form-group">
                <label class="form-label" for="v-sort">Reihenfolge</label>
                <input
                    class="form-input form-input--narrow"
                    type="number"
                    id="v-sort"
                    name="sort_order"
                    value="<?= (int) ($vehicle['sort_order'] ?? 0) ?>"
                    min="0"
                >
            </div>
            <div class="form-group form-group--checkbox">
                <label class="form-label--checkbox">
                    <input
                        type="checkbox"
                        name="is_active"
                        value="1"
                        <?= (int) ($vehicle['is_active'] ?? 1) === 1 ? 'checked' : '' ?>
                    >
                    Aktiv (auf der Website sichtbar)
                </label>
            </div>
        </div>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn-primary"><?= $isNew ? 'Fahrzeug anlegen' : 'Speichern' ?></button>
        <a href="/admin/vehicles" class="btn-ghost">Abbrechen</a>
    </div>
</form>

<?php if (!$isNew): ?>
<div class="danger-zone">
    <h2 class="danger-zone__title">Gefahrenzone</h2>
    <p>Das Fahrzeug wird dauerhaft gelöscht. Diese Aktion kann nicht rückgängig gemacht werden.</p>
    <form method="post" action="/admin/vehicles/<?= (int) $vehicle['id'] ?>/delete">
        <?= \App\Core\Csrf::field() ?>
        <button type="submit" class="btn-danger" data-confirm="Fahrzeug »<?= e($vehicle['name']) ?>« wirklich dauerhaft löschen?">
            Fahrzeug löschen
        </button>
    </form>
</div>
<?php endif; ?>

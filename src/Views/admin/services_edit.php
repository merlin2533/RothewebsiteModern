<?php declare(strict_types=1); ?>
<?php $isNew = $service === null; ?>
<div class="page-header">
    <h1 class="page-header__title"><?= $isNew ? 'Neue Leistung' : ('Leistung: ' . e($service['title'])) ?></h1>
    <a href="/admin/services" class="btn-ghost btn-sm">← Zurück zur Liste</a>
</div>

<form method="post" action="<?= $isNew ? '/admin/services' : '/admin/services/' . (int) $service['id'] ?>">
    <?= \App\Core\Csrf::field() ?>

    <div class="input-row">
        <div class="form-group">
            <label class="form-label" for="s-slug">Slug</label>
            <input
                class="form-input"
                type="text"
                id="s-slug"
                name="slug"
                value="<?= e($service['slug'] ?? '') ?>"
                data-slug-target
                required
            >
        </div>
        <div class="form-group">
            <label class="form-label" for="s-title">Titel</label>
            <input
                class="form-input"
                type="text"
                id="s-title"
                name="title"
                value="<?= e($service['title'] ?? '') ?>"
                data-slug-source
                required
            >
        </div>
    </div>

    <div class="form-group">
        <label class="form-label" for="s-summary">Kurztext (Zusammenfassung)</label>
        <textarea
            class="form-input form-textarea"
            id="s-summary"
            name="summary"
            rows="3"
        ><?= e($service['summary'] ?? '') ?></textarea>
    </div>

    <div class="form-group">
        <label class="form-label" for="s-body">Volltext (HTML)</label>
        <p class="form-hint">Erlaubte Tags: <code>p, h2, h3, h4, ul, ol, li, strong, em, a, br, blockquote</code></p>
        <textarea
            class="form-input form-textarea form-textarea--large"
            id="s-body"
            name="body_html"
            rows="15"
        ><?= e($service['body_html'] ?? '') ?></textarea>
    </div>

    <div class="form-group">
        <label class="form-label" for="s-icon">Icon</label>
        <select class="form-input form-select" id="s-icon" name="icon_key">
            <option value="">— kein Icon —</option>
            <?php
            $icons = ['truck' => 'LKW/Truck', 'tieflader' => 'Tieflader', 'warehouse' => 'Lager/Warehouse', 'forklift' => 'Stapler/Forklift', 'phone' => 'Telefon', 'mail' => 'E-Mail'];
            foreach ($icons as $key => $label):
            ?>
                <option value="<?= e($key) ?>" <?= ($service['icon_key'] ?? '') === $key ? 'selected' : '' ?>><?= e($label) ?> (<?= e($key) ?>)</option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="input-row">
        <div class="form-group">
            <label class="form-label" for="s-sort">Reihenfolge</label>
            <input
                class="form-input form-input--narrow"
                type="number"
                id="s-sort"
                name="sort_order"
                value="<?= (int) ($service['sort_order'] ?? 0) ?>"
                min="0"
            >
        </div>
        <div class="form-group form-group--checkbox">
            <label class="form-label--checkbox">
                <input
                    type="checkbox"
                    name="is_active"
                    value="1"
                    <?= (int) ($service['is_active'] ?? 1) === 1 ? 'checked' : '' ?>
                >
                Aktiv (auf der Website sichtbar)
            </label>
        </div>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn-primary"><?= $isNew ? 'Leistung anlegen' : 'Speichern' ?></button>
        <a href="/admin/services" class="btn-ghost">Abbrechen</a>
    </div>
</form>

<?php if (!$isNew): ?>
<div class="danger-zone">
    <h2 class="danger-zone__title">Gefahrenzone</h2>
    <p>Die Leistung wird dauerhaft gelöscht.</p>
    <form method="post" action="/admin/services/<?= (int) $service['id'] ?>/delete">
        <?= \App\Core\Csrf::field() ?>
        <button type="submit" class="btn-danger" data-confirm="Leistung »<?= e($service['title']) ?>« wirklich löschen?">
            Leistung löschen
        </button>
    </form>
</div>
<?php endif; ?>

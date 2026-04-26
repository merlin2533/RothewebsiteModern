<?php declare(strict_types=1); ?>
<?php $isNew = $event === null; ?>
<div class="page-header">
    <h1 class="page-header__title"><?= $isNew ? 'Neuer Zeitstrahl-Eintrag' : ('Eintrag: ' . (int) ($event['year'] ?? 0) . ' – ' . e($event['title'] ?? '')) ?></h1>
    <a href="/admin/timeline" class="btn-ghost btn-sm">← Zurück zur Liste</a>
</div>

<form method="post" action="<?= $isNew ? '/admin/timeline' : '/admin/timeline/' . (int) $event['id'] ?>">
    <?= \App\Core\Csrf::field() ?>

    <div class="input-row">
        <div class="form-group">
            <label class="form-label" for="tl-year">Jahr</label>
            <input
                class="form-input form-input--narrow"
                type="number"
                id="tl-year"
                name="year"
                value="<?= (int) ($event['year'] ?? (int) date('Y')) ?>"
                min="1900"
                max="2100"
                required
            >
        </div>
        <div class="form-group">
            <label class="form-label" for="tl-title">Titel</label>
            <input
                class="form-input"
                type="text"
                id="tl-title"
                name="title"
                value="<?= e($event['title'] ?? '') ?>"
                required
            >
        </div>
    </div>

    <div class="form-group">
        <label class="form-label" for="tl-desc">Beschreibung</label>
        <textarea
            class="form-input form-textarea"
            id="tl-desc"
            name="description"
            rows="4"
        ><?= e($event['description'] ?? '') ?></textarea>
    </div>

    <div class="input-row">
        <div class="form-group">
            <label class="form-label" for="tl-sort">Reihenfolge</label>
            <input
                class="form-input form-input--narrow"
                type="number"
                id="tl-sort"
                name="sort_order"
                value="<?= (int) ($event['sort_order'] ?? 0) ?>"
                min="0"
            >
        </div>
        <div class="form-group form-group--checkbox">
            <label class="form-label--checkbox">
                <input
                    type="checkbox"
                    name="is_active"
                    value="1"
                    <?= (int) ($event['is_active'] ?? 1) === 1 ? 'checked' : '' ?>
                >
                Aktiv (auf der Website sichtbar)
            </label>
        </div>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn-primary"><?= $isNew ? 'Eintrag anlegen' : 'Speichern' ?></button>
        <a href="/admin/timeline" class="btn-ghost">Abbrechen</a>
    </div>
</form>

<?php if (!$isNew): ?>
<div class="danger-zone">
    <h2 class="danger-zone__title">Gefahrenzone</h2>
    <p>Der Zeitstrahl-Eintrag wird dauerhaft gelöscht.</p>
    <form method="post" action="/admin/timeline/<?= (int) $event['id'] ?>/delete">
        <?= \App\Core\Csrf::field() ?>
        <button type="submit" class="btn-danger" data-confirm="Eintrag »<?= e($event['title'] ?? '') ?>« wirklich löschen?">
            Eintrag löschen
        </button>
    </form>
</div>
<?php endif; ?>

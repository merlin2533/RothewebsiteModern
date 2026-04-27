<?php
/** @var array|null $r */
declare(strict_types=1);
$isNew = $r === null;
$action = $isNew ? '/admin/redirects' : '/admin/redirects/' . (int) $r['id'];
?>
<div class="page-head">
    <h1><?= $isNew ? 'Neuer Redirect' : 'Redirect bearbeiten' ?></h1>
    <a class="btn-ghost" href="/admin/redirects">← Zurück</a>
</div>

<form method="post" action="<?= e($action) ?>" novalidate>
    <?= \App\Core\Csrf::field() ?>

    <div class="input-row">
        <div class="form-group">
            <label class="form-label" for="from_path">Von (Pfad, mit /)</label>
            <input class="form-input" type="text" id="from_path" name="from_path" required
                   placeholder="/alte-seite"
                   value="<?= e((string) ($r['from_path'] ?? '')) ?>">
        </div>
        <div class="form-group">
            <label class="form-label" for="to_path">Nach (Pfad oder voll-URL)</label>
            <input class="form-input" type="text" id="to_path" name="to_path" required
                   placeholder="/neue-seite"
                   value="<?= e((string) ($r['to_path'] ?? '')) ?>">
        </div>
        <div class="form-group">
            <label class="form-label" for="status_code">HTTP-Status</label>
            <select class="form-input" id="status_code" name="status_code">
                <?php foreach ([301 => '301 (permanent)', 302 => '302 (temporär)', 307 => '307 (temp. method-preserving)', 308 => '308 (perm. method-preserving)'] as $c => $label): ?>
                    <option value="<?= (int) $c ?>" <?= ((int) ($r['status_code'] ?? 301)) === $c ? 'selected' : '' ?>><?= e($label) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <div class="form-group form-group--checkbox">
        <label>
            <input type="checkbox" name="is_active" value="1"
                   <?= ($r === null || (int) $r['is_active'] === 1) ? 'checked' : '' ?>>
            Aktiv
        </label>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn-primary">Speichern</button>
    </div>
</form>

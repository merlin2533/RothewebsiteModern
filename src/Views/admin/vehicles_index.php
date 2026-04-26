<?php declare(strict_types=1); ?>
<div class="page-header">
    <h1 class="page-header__title">Fahrzeuge</h1>
    <a href="/admin/vehicles/new" class="btn-primary">+ Neues Fahrzeug</a>
</div>

<div class="table-wrap">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Sort.</th>
                <th>Name</th>
                <th>Slug</th>
                <th>L × B × H</th>
                <th>Nutzlast</th>
                <th>Aktiv</th>
                <th class="col-actions">Aktionen</th>
            </tr>
        </thead>
        <tbody>
        <?php if (empty($vehicles)): ?>
            <tr><td colspan="7" class="empty-row">Keine Fahrzeuge vorhanden.</td></tr>
        <?php else: ?>
            <?php foreach ($vehicles as $v): ?>
            <tr>
                <td><?= (int) ($v['sort_order'] ?? 0) ?></td>
                <td><strong><?= e($v['name']) ?></strong></td>
                <td><code><?= e($v['slug'] ?? '') ?></code></td>
                <td class="nowrap">
                    <?= format_meters(isset($v['length_m']) ? (float) $v['length_m'] : null) ?>
                    &times; <?= format_meters(isset($v['width_m']) ? (float) $v['width_m'] : null) ?>
                    &times; <?= format_meters(isset($v['height_m']) ? (float) $v['height_m'] : null) ?>
                </td>
                <td><?= format_kg(isset($v['payload_kg']) ? (int) $v['payload_kg'] : null) ?></td>
                <td>
                    <?php if ((int) ($v['is_active'] ?? 0) === 1): ?>
                        <span class="badge badge--green">Ja</span>
                    <?php else: ?>
                        <span class="badge badge--grey">Nein</span>
                    <?php endif; ?>
                </td>
                <td class="col-actions">
                    <a href="/admin/vehicles/<?= (int) $v['id'] ?>/edit" class="btn-ghost btn-sm">Bearbeiten</a>
                    <form method="post" action="/admin/vehicles/<?= (int) $v['id'] ?>/delete" class="inline-form">
                        <?= \App\Core\Csrf::field() ?>
                        <button type="submit" class="btn-danger btn-sm" data-confirm="Fahrzeug »<?= e($v['name']) ?>« wirklich löschen?">Löschen</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>

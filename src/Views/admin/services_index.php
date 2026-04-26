<?php declare(strict_types=1); ?>
<div class="page-header">
    <h1 class="page-header__title">Leistungen</h1>
    <a href="/admin/services/new" class="btn-primary">+ Neue Leistung</a>
</div>

<div class="table-wrap">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Sort.</th>
                <th>Titel</th>
                <th>Slug</th>
                <th>Icon</th>
                <th>Aktiv</th>
                <th class="col-actions">Aktionen</th>
            </tr>
        </thead>
        <tbody>
        <?php if (empty($services)): ?>
            <tr><td colspan="6" class="empty-row">Keine Leistungen vorhanden.</td></tr>
        <?php else: ?>
            <?php foreach ($services as $s): ?>
            <tr>
                <td><?= (int) ($s['sort_order'] ?? 0) ?></td>
                <td><strong><?= e($s['title']) ?></strong></td>
                <td><code><?= e($s['slug'] ?? '') ?></code></td>
                <td><code><?= e($s['icon_key'] ?? '') ?></code></td>
                <td>
                    <?php if ((int) ($s['is_active'] ?? 0) === 1): ?>
                        <span class="badge badge--green">Ja</span>
                    <?php else: ?>
                        <span class="badge badge--grey">Nein</span>
                    <?php endif; ?>
                </td>
                <td class="col-actions">
                    <a href="/admin/services/<?= (int) $s['id'] ?>/edit" class="btn-ghost btn-sm">Bearbeiten</a>
                    <form method="post" action="/admin/services/<?= (int) $s['id'] ?>/delete" class="inline-form">
                        <?= \App\Core\Csrf::field() ?>
                        <button type="submit" class="btn-danger btn-sm" data-confirm="Leistung »<?= e($s['title']) ?>« wirklich löschen?">Löschen</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>

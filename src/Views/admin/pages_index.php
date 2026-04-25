<?php declare(strict_types=1); ?>
<div class="page-header">
    <h1 class="page-header__title">Seiten</h1>
    <p class="page-header__sub">Alle statischen Seiten der Website.</p>
</div>

<div class="table-wrap">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Slug</th>
                <th>Titel</th>
                <th>Veröffentlicht</th>
                <th>Reihenfolge</th>
                <th>Geändert</th>
                <th class="col-actions">Aktionen</th>
            </tr>
        </thead>
        <tbody>
        <?php if (empty($pages)): ?>
            <tr><td colspan="6" class="empty-row">Keine Seiten vorhanden.</td></tr>
        <?php else: ?>
            <?php foreach ($pages as $p): ?>
            <tr>
                <td><code><?= e($p['slug']) ?></code></td>
                <td><?= e($p['title']) ?></td>
                <td>
                    <?php if ((int) $p['is_published'] === 1): ?>
                        <span class="badge badge--green">Ja</span>
                    <?php else: ?>
                        <span class="badge badge--grey">Nein</span>
                    <?php endif; ?>
                </td>
                <td><?= e((string) ($p['sort_order'] ?? '')) ?></td>
                <td><?= e($p['updated_at'] ?? '–') ?></td>
                <td class="col-actions">
                    <a href="/admin/pages/<?= (int) $p['id'] ?>/edit" class="btn-ghost btn-sm">Bearbeiten</a>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>

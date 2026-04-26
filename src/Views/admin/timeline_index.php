<?php declare(strict_types=1); ?>
<div class="page-header">
    <h1 class="page-header__title">Zeitstrahl</h1>
    <a href="/admin/timeline/new" class="btn-primary">+ Neuer Eintrag</a>
</div>

<div class="table-wrap">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Sort.</th>
                <th>Jahr</th>
                <th>Titel</th>
                <th>Aktiv</th>
                <th class="col-actions">Aktionen</th>
            </tr>
        </thead>
        <tbody>
        <?php if (empty($events)): ?>
            <tr><td colspan="5" class="empty-row">Keine Einträge vorhanden.</td></tr>
        <?php else: ?>
            <?php foreach ($events as $ev): ?>
            <tr>
                <td><?= (int) ($ev['sort_order'] ?? 0) ?></td>
                <td><strong><?= (int) ($ev['year'] ?? 0) ?></strong></td>
                <td><?= e($ev['title'] ?? '') ?></td>
                <td>
                    <?php if ((int) ($ev['is_active'] ?? 0) === 1): ?>
                        <span class="badge badge--green">Ja</span>
                    <?php else: ?>
                        <span class="badge badge--grey">Nein</span>
                    <?php endif; ?>
                </td>
                <td class="col-actions">
                    <a href="/admin/timeline/<?= (int) $ev['id'] ?>/edit" class="btn-ghost btn-sm">Bearbeiten</a>
                    <form method="post" action="/admin/timeline/<?= (int) $ev['id'] ?>/delete" class="inline-form">
                        <?= \App\Core\Csrf::field() ?>
                        <button type="submit" class="btn-danger btn-sm" data-confirm="Eintrag »<?= e($ev['title'] ?? '') ?>« wirklich löschen?">Löschen</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>

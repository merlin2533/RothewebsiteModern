<?php declare(strict_types=1); ?>
<div class="page-head">
    <div>
        <h1>FAQ</h1>
        <p class="page-head__sub">Häufige Fragen – als FAQPage-Schema und sichtbar im Frontend.</p>
    </div>
    <a class="btn-primary" href="/admin/faqs/new">Neue FAQ</a>
</div>

<table class="data-table">
    <thead>
        <tr>
            <th>Sort.</th>
            <th>Seite</th>
            <th>Frage</th>
            <th>Aktiv</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($faqs as $f): ?>
            <tr>
                <td><?= (int) $f['sort_order'] ?></td>
                <td><code><?= e($f['page_slug']) ?></code></td>
                <td><?= e($f['question']) ?></td>
                <td>
                    <?php if ((int) $f['is_active'] === 1): ?>
                        <span class="badge badge--ok">aktiv</span>
                    <?php else: ?>
                        <span class="badge badge--muted">inaktiv</span>
                    <?php endif; ?>
                </td>
                <td class="data-table__actions">
                    <a class="btn-ghost btn-sm" href="/admin/faqs/<?= (int) $f['id'] ?>/edit">Bearbeiten</a>
                    <form method="post" action="/admin/faqs/<?= (int) $f['id'] ?>/delete" style="display:inline">
                        <?= \App\Core\Csrf::field() ?>
                        <button type="submit" class="btn-danger btn-sm" data-confirm="FAQ wirklich löschen?">Löschen</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if (!$faqs): ?>
            <tr><td colspan="5" class="muted">Noch keine FAQs.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

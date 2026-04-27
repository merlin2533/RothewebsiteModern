<?php declare(strict_types=1); ?>
<div class="page-head">
    <div>
        <h1>Redirects (301/302)</h1>
        <p class="page-head__sub">Pflegbare Weiterleitungs-Map. Wird vor jedem Routing geprüft.</p>
    </div>
    <a class="btn-primary" href="/admin/redirects/new">Neuer Redirect</a>
</div>

<table class="data-table">
    <thead>
        <tr>
            <th>Von</th>
            <th>Nach</th>
            <th>Code</th>
            <th>Aktiv</th>
            <th>Hits</th>
            <th>Letzter Hit</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($rows as $r): ?>
            <tr>
                <td><code><?= e((string) $r['from_path']) ?></code></td>
                <td><code><?= e((string) $r['to_path']) ?></code></td>
                <td><?= (int) $r['status_code'] ?></td>
                <td>
                    <?php if ((int) $r['is_active'] === 1): ?>
                        <span class="badge badge--ok">aktiv</span>
                    <?php else: ?>
                        <span class="badge badge--muted">inaktiv</span>
                    <?php endif; ?>
                </td>
                <td><?= (int) $r['hits'] ?></td>
                <td class="muted"><?= e((string) ($r['last_hit_at'] ?? '–')) ?></td>
                <td class="data-table__actions">
                    <a class="btn-ghost btn-sm" href="/admin/redirects/<?= (int) $r['id'] ?>/edit">Bearbeiten</a>
                    <form method="post" action="/admin/redirects/<?= (int) $r['id'] ?>/delete" style="display:inline">
                        <?= \App\Core\Csrf::field() ?>
                        <button type="submit" class="btn-danger btn-sm" data-confirm="Redirect löschen?">Löschen</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if (!$rows): ?>
            <tr><td colspan="7" class="muted">Noch keine Redirects.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

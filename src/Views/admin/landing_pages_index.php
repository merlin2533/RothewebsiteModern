<?php declare(strict_types=1); ?>
<div class="page-head">
    <div>
        <h1>Landingpages</h1>
        <p class="page-head__sub">Branchen- und Stadt-Landingpages fuer SEO und SEA. Slug = oeffentliche URL.</p>
    </div>
    <a class="btn-primary" href="/admin/landing-pages/new">Neue Landingpage</a>
</div>

<table class="data-table">
    <thead>
        <tr>
            <th>Sort</th>
            <th>Typ</th>
            <th>Slug</th>
            <th>Titel</th>
            <th>Veröff.</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($rows as $r): ?>
            <tr>
                <td><?= (int) $r['sort_order'] ?></td>
                <td><span class="badge"><?= e((string) $r['kind']) ?></span></td>
                <td><a href="/<?= e((string) $r['slug']) ?>" target="_blank" rel="noopener"><code>/<?= e((string) $r['slug']) ?></code></a></td>
                <td><?= e((string) $r['title']) ?></td>
                <td>
                    <?php if ((int) $r['is_published'] === 1): ?>
                        <span class="badge badge--ok">live</span>
                    <?php else: ?>
                        <span class="badge badge--muted">Entwurf</span>
                    <?php endif; ?>
                </td>
                <td class="data-table__actions">
                    <a class="btn-ghost btn-sm" href="/admin/landing-pages/<?= (int) $r['id'] ?>/edit">Bearbeiten</a>
                    <form method="post" action="/admin/landing-pages/<?= (int) $r['id'] ?>/delete" style="display:inline">
                        <?= \App\Core\Csrf::field() ?>
                        <button type="submit" class="btn-danger btn-sm" data-confirm="Landingpage löschen?">Löschen</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if (!$rows): ?>
            <tr><td colspan="6" class="muted">Noch keine Landingpages.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

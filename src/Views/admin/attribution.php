<?php declare(strict_types=1); ?>
<div class="page-head">
    <h1>Attribution</h1>
    <p class="page-head__sub">Woher kommen Besucher? UTM-Parameter, Klick-IDs, Referrer – pseudonymisiert (IP gehasht).</p>
</div>

<div class="stats-grid">
    <section class="card">
        <header class="card__head"><h2>Top-Quellen</h2></header>
        <table class="data-table data-table--compact">
            <thead><tr><th>Quelle</th><th>Sessions</th><th>Visits</th></tr></thead>
            <tbody>
                <?php foreach ($topSources as $r): ?>
                    <tr>
                        <td><code><?= e((string) $r['src']) ?></code></td>
                        <td><?= (int) $r['n'] ?></td>
                        <td><?= (int) $r['total_visits'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </section>

    <section class="card">
        <header class="card__head"><h2>Top-Kampagnen</h2></header>
        <table class="data-table data-table--compact">
            <thead><tr><th>Kampagne</th><th>Sessions</th></tr></thead>
            <tbody>
                <?php foreach ($topCampaigns as $r): ?>
                    <tr>
                        <td><code><?= e((string) $r['camp']) ?></code></td>
                        <td><?= (int) $r['n'] ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$topCampaigns): ?>
                    <tr><td colspan="2" class="muted">Keine Kampagnen erfasst.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </section>
</div>

<section class="card" style="margin-top: 2rem;">
    <header class="card__head"><h2>Letzte Sessions (200)</h2></header>
    <table class="data-table">
        <thead>
            <tr>
                <th>Erstkontakt</th>
                <th>Letzter Besuch</th>
                <th>Visits</th>
                <th>Quelle</th>
                <th>Medium</th>
                <th>Kampagne</th>
                <th>Landing</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($rows as $r): ?>
                <tr>
                    <td><code><?= e((string) $r['first_seen']) ?></code></td>
                    <td><code><?= e((string) $r['last_seen']) ?></code></td>
                    <td><?= (int) $r['visits'] ?></td>
                    <td><?= e((string) ($r['utm_source'] ?? '')) ?></td>
                    <td><?= e((string) ($r['utm_medium'] ?? '')) ?></td>
                    <td><?= e((string) ($r['utm_campaign'] ?? '')) ?></td>
                    <td class="muted"><code><?= e(substr((string) ($r['landing_url'] ?? ''), 0, 60)) ?></code></td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$rows): ?>
                <tr><td colspan="7" class="muted">Noch keine Daten – sobald Besucher mit UTMs oder Klick-IDs kommen, fuellt sich diese Liste.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</section>

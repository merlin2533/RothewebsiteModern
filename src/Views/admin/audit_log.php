<?php declare(strict_types=1); ?>
<div class="page-head">
    <h1>Audit-Log</h1>
    <p class="page-head__sub">Letzte 200 Admin-Aktionen.</p>
</div>

<table class="data-table">
    <thead>
        <tr>
            <th>Zeit</th>
            <th>User</th>
            <th>Aktion</th>
            <th>Objekt</th>
            <th>IP</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($entries as $e): ?>
            <tr>
                <td><code><?= e($e['created_at']) ?></code></td>
                <td><?= e($e['username'] ?? '-') ?></td>
                <td><code><?= e($e['action']) ?></code></td>
                <td>
                    <?php if (!empty($e['entity'])): ?>
                        <code><?= e($e['entity']) ?>#<?= (int) $e['entity_id'] ?></code>
                    <?php else: ?>
                        –
                    <?php endif; ?>
                </td>
                <td><code><?= e($e['ip'] ?? '') ?></code></td>
            </tr>
        <?php endforeach; ?>
        <?php if (!$entries): ?>
            <tr><td colspan="5" class="muted">Noch keine Eintraege.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

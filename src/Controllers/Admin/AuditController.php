<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\AuditLogger;
use App\Core\View;

final class AuditController
{
    public function index(array $args): void
    {
        Auth::requireLogin();
        echo View::admin('audit_log', [
            'entries' => AuditLogger::recent(200),
        ]);
    }

    public function attribution(array $args): void
    {
        Auth::requireLogin();
        $pdo = $GLOBALS['pdo'];
        $rows = $pdo->query('
            SELECT * FROM lead_attributions
            ORDER BY last_seen DESC
            LIMIT 200
        ')->fetchAll();

        // Aggregat: Top-Quellen / Kampagnen
        $topSources = $pdo->query('
            SELECT COALESCE(utm_source, "(direct)") AS src, COUNT(*) AS n,
                   SUM(visits) AS total_visits
            FROM lead_attributions
            GROUP BY src ORDER BY n DESC LIMIT 20
        ')->fetchAll();

        $topCampaigns = $pdo->query('
            SELECT utm_campaign AS camp, COUNT(*) AS n
            FROM lead_attributions
            WHERE utm_campaign IS NOT NULL
            GROUP BY camp ORDER BY n DESC LIMIT 20
        ')->fetchAll();

        echo View::admin('attribution', [
            'rows'         => $rows,
            'topSources'   => $topSources,
            'topCampaigns' => $topCampaigns,
        ]);
    }
}

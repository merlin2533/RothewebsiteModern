<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\View;

final class DashboardController
{
    public function index(array $args): void
    {
        Auth::requireLogin();
        $pdo = $GLOBALS['pdo'];
        $stats = [
            'pages'    => (int) $pdo->query('SELECT COUNT(*) FROM pages')->fetchColumn(),
            'vehicles' => (int) $pdo->query('SELECT COUNT(*) FROM vehicles')->fetchColumn(),
            'services' => (int) $pdo->query('SELECT COUNT(*) FROM services')->fetchColumn(),
            'media'    => (int) $pdo->query('SELECT COUNT(*) FROM media')->fetchColumn(),
            'timeline' => (int) $pdo->query('SELECT COUNT(*) FROM timeline_events')->fetchColumn(),
        ];
        $user = Auth::user();
        echo View::admin('dashboard', [
            'stats' => $stats,
            'user'  => $user,
        ]);
    }
}

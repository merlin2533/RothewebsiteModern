<?php declare(strict_types=1); ?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex,nofollow">
    <title>Admin – Rothe-Transporte</title>
    <link rel="stylesheet" href="<?= asset('css/admin.css') ?>">
</head>
<body class="admin-body<?= ($_template ?? '') === 'login' ? ' admin-body--login' : '' ?>">

<?php if (($_template ?? '') === 'login'): ?>
    <div class="login-wrap">
        <?= $content ?>
    </div>
<?php else: ?>
    <!-- Topbar -->
    <header class="admin-topbar" role="banner">
        <button class="admin-topbar__hamburger" id="sidebarToggle" aria-label="Menü öffnen" aria-expanded="false" aria-controls="adminSidebar">
            <span></span><span></span><span></span>
        </button>
        <a class="admin-topbar__brand" href="/admin">ROTHE <strong>Admin</strong></a>
        <div class="admin-topbar__user">
            <?php $__user = \App\Core\Auth::user(); ?>
            <span class="admin-topbar__username"><?= e($__user['username'] ?? '') ?></span>
            <form method="post" action="/admin/logout" class="admin-topbar__logout-form">
                <?= \App\Core\Csrf::field() ?>
                <button type="submit" class="btn-ghost btn-sm">Abmelden</button>
            </form>
        </div>
    </header>

    <!-- Sidebar overlay (mobile) -->
    <div class="sidebar-overlay" id="sidebarOverlay" aria-hidden="true"></div>

    <div class="admin-layout">
        <!-- Sidebar -->
        <nav class="admin-sidebar" id="adminSidebar" aria-label="Admin-Navigation">
            <ul class="admin-nav">
                <li>
                    <a href="/admin"
                       <?= ($_template ?? '') === 'dashboard' ? 'aria-current="page"' : '' ?>>
                        <svg aria-hidden="true" viewBox="0 0 20 20" fill="currentColor"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7A1 1 0 003 11h1v6a1 1 0 001 1h4v-4h2v4h4a1 1 0 001-1v-6h1a1 1 0 00.707-1.707l-7-7z"/></svg>
                        Dashboard
                    </a>
                </li>
                <li>
                    <a href="/admin/pages"
                       <?= str_starts_with($_template ?? '', 'pages') ? 'aria-current="page"' : '' ?>>
                        <svg aria-hidden="true" viewBox="0 0 20 20" fill="currentColor"><path d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"/></svg>
                        Seiten
                    </a>
                </li>
                <li>
                    <a href="/admin/vehicles"
                       <?= str_starts_with($_template ?? '', 'vehicles') ? 'aria-current="page"' : '' ?>>
                        <svg aria-hidden="true" viewBox="0 0 20 20" fill="currentColor"><path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zm7 0a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM1 3h1l1.5 6H16l1-4H5.5"/></svg>
                        Fahrzeuge
                    </a>
                </li>
                <li>
                    <a href="/admin/services"
                       <?= str_starts_with($_template ?? '', 'services') ? 'aria-current="page"' : '' ?>>
                        <svg aria-hidden="true" viewBox="0 0 20 20" fill="currentColor"><path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/><path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5z" clip-rule="evenodd"/></svg>
                        Leistungen
                    </a>
                </li>
                <li>
                    <a href="/admin/timeline"
                       <?= str_starts_with($_template ?? '', 'timeline') ? 'aria-current="page"' : '' ?>>
                        <svg aria-hidden="true" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/></svg>
                        Zeitstrahl
                    </a>
                </li>
                <li>
                    <a href="/admin/media"
                       <?= str_starts_with($_template ?? '', 'media') ? 'aria-current="page"' : '' ?>>
                        <svg aria-hidden="true" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/></svg>
                        Medien
                    </a>
                </li>
                <li>
                    <a href="/admin/faqs"
                       <?= str_starts_with($_template ?? '', 'faqs') ? 'aria-current="page"' : '' ?>>
                        <svg aria-hidden="true" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 100 2 1 1 0 000-2zm-1 4v4h2v-4H9z" clip-rule="evenodd"/></svg>
                        FAQ
                    </a>
                </li>
                <li>
                    <a href="/admin/landing-pages"
                       <?= str_starts_with($_template ?? '', 'landing_pages') ? 'aria-current="page"' : '' ?>>
                        <svg aria-hidden="true" viewBox="0 0 20 20" fill="currentColor"><path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zm0 6a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zm10-1a1 1 0 00-1 1v6a1 1 0 001 1h3a1 1 0 001-1v-6a1 1 0 00-1-1h-3z"/></svg>
                        Landingpages
                    </a>
                </li>
                <li>
                    <a href="/admin/redirects"
                       <?= str_starts_with($_template ?? '', 'redirects') ? 'aria-current="page"' : '' ?>>
                        <svg aria-hidden="true" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M12.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                        Redirects
                    </a>
                </li>
                <li>
                    <a href="/admin/settings"
                       <?= ($_template ?? '') === 'settings' ? 'aria-current="page"' : '' ?>>
                        <svg aria-hidden="true" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/></svg>
                        Einstellungen
                    </a>
                </li>
                <li>
                    <a href="/admin/audit"
                       <?= ($_template ?? '') === 'audit_log' ? 'aria-current="page"' : '' ?>>
                        <svg aria-hidden="true" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/></svg>
                        Audit-Log
                    </a>
                </li>
                <li>
                    <a href="/admin/attribution"
                       <?= ($_template ?? '') === 'attribution' ? 'aria-current="page"' : '' ?>>
                        <svg aria-hidden="true" viewBox="0 0 20 20" fill="currentColor"><path d="M2 10a8 8 0 1116 0 8 8 0 01-16 0zm8-7a1 1 0 011 1v4.5l3 1.7a1 1 0 11-1 1.7l-3.5-2A1 1 0 019 9V4a1 1 0 011-1z"/></svg>
                        Attribution
                    </a>
                </li>
                <li>
                    <a href="/admin/account"
                       <?= ($_template ?? '') === 'account' ? 'aria-current="page"' : '' ?>>
                        <svg aria-hidden="true" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/></svg>
                        Mein Konto
                    </a>
                </li>
            </ul>
            <div class="admin-nav__footer">
                <a href="/" target="_blank" rel="noopener" class="admin-nav__site-link">
                    <svg aria-hidden="true" viewBox="0 0 20 20" fill="currentColor"><path d="M11 3a1 1 0 100 2h2.586l-6.293 6.293a1 1 0 101.414 1.414L15 6.414V9a1 1 0 102 0V4a1 1 0 00-1-1h-5z"/><path d="M5 5a2 2 0 00-2 2v8a2 2 0 002 2h8a2 2 0 002-2v-3a1 1 0 10-2 0v3H5V7h3a1 1 0 000-2H5z"/></svg>
                    Website ansehen
                </a>
            </div>
        </nav>

        <!-- Main content -->
        <main class="admin-main" id="main-content">
            <?php $__success = flash('success'); $__error = flash('error'); ?>
            <?php if ($__success): ?>
                <div class="flash flash--success" role="alert">
                    <span><?= e($__success) ?></span>
                    <button class="flash__dismiss" type="button" aria-label="Schließen">×</button>
                </div>
            <?php endif; ?>
            <?php if ($__error): ?>
                <div class="flash flash--error" role="alert">
                    <span><?= e($__error) ?></span>
                    <button class="flash__dismiss" type="button" aria-label="Schließen">×</button>
                </div>
            <?php endif; ?>
            <?= $content ?>
        </main>
    </div>
<?php endif; ?>

<script src="<?= asset('js/admin.js') ?>" defer></script>
</body>
</html>

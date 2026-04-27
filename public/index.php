<?php

declare(strict_types=1);

// ── Bootstrap with self-healing fallback ─────────────────────────────────────
// If anything during bootstrap (missing .env, unwritable data/, missing
// migrations, empty DB) fails, redirect the visitor to the installer
// instead of returning a bare 500. This keeps fresh deployments accessible.
try {
    require __DIR__ . '/../src/bootstrap.php';

    // Smoke-check: is the schema actually present? If not, the installer
    // has not run yet on this hosting account.
    if (!is_file(__DIR__ . '/../data/installed.lock')) {
        $needInstall = true;
        try {
            $pdo = $GLOBALS['pdo'] ?? null;
            if ($pdo instanceof \PDO) {
                $pdo->query('SELECT 1 FROM pages LIMIT 1');
                $needInstall = false;
            }
        } catch (\Throwable) {
            $needInstall = true;
        }
        if ($needInstall && !str_starts_with((string) ($_SERVER['REQUEST_URI'] ?? ''), '/install.php')) {
            header('Location: /install.php', true, 302);
            exit;
        }
    }
} catch (\Throwable $e) {
    // Hard failure – render a helpful page instead of a blank 500.
    http_response_code(500);
    header('Content-Type: text/html; charset=utf-8');
    $msg = htmlspecialchars($e->getMessage(), ENT_QUOTES);
    echo '<!doctype html><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">';
    echo '<title>Setup erforderlich – Rothe-Transporte</title>';
    echo '<style>body{font:16px/1.6 system-ui,-apple-system,Segoe UI,sans-serif;max-width:680px;margin:3rem auto;padding:0 1.5rem;color:#0F1419;background:#F4F2EE}h1{color:#0B2545}a{color:#C2410C}code,pre{font-family:ui-monospace,Menlo,monospace}pre{background:#050F22;color:#fff;padding:1rem;border-radius:8px;overflow-x:auto}.card{background:#fff;border:1px solid #E3E8EF;border-radius:12px;padding:2rem;box-shadow:0 16px 36px -24px rgba(11,37,69,.2)}</style>';
    echo '<div class="card"><h1>Setup erforderlich</h1>';
    echo '<p>Die Webseite kann noch nicht geladen werden, weil das initiale Setup fehlt oder unvollstaendig ist.</p>';
    echo '<p><strong>Naechste Schritte:</strong></p>';
    echo '<ol>';
    echo '<li>Pruefen, ob der DocumentRoot auf <code>public/</code> zeigt.</li>';
    echo '<li>Pruefen, ob <code>data/</code> und <code>uploads/</code> beschreibbar sind (CHMOD 750/755).</li>';
    echo '<li>Den Installer aufrufen: <a href="/install.php">/install.php</a></li>';
    echo '</ol>';
    echo '<p>Technische Meldung (fuer den Admin):</p><pre>' . $msg . '</pre>';
    echo '<p style="color:#6B7785;font-size:0.9em">Vollstaendige Anleitung: <a href="https://github.com/merlin2533/rothewebsitemodern/blob/HEAD/DEPLOY.md">DEPLOY.md</a></p>';
    echo '</div>';
    exit;
}

use App\Core\Router;
use App\Controllers\HomeController;
use App\Controllers\AboutController;
use App\Controllers\ServicesController;
use App\Controllers\VehiclesController;
use App\Controllers\CareerController;
use App\Controllers\ContactController;
use App\Controllers\LegalController;
use App\Controllers\SitemapController;
use App\Controllers\LandingPagesController;
use App\Controllers\OgImageController;
use App\Controllers\TrackController;
use App\Controllers\Admin\AuthController as AdminAuth;
use App\Controllers\Admin\DashboardController as AdminDashboard;
use App\Controllers\Admin\PagesController as AdminPages;
use App\Controllers\Admin\VehiclesController as AdminVehicles;
use App\Controllers\Admin\ServicesController as AdminServices;
use App\Controllers\Admin\TimelineController as AdminTimeline;
use App\Controllers\Admin\MediaController as AdminMedia;
use App\Controllers\Admin\SettingsController as AdminSettings;
use App\Controllers\Admin\FaqsController as AdminFaqs;
use App\Controllers\Admin\AuditController as AdminAudit;
use App\Controllers\Admin\LandingPagesController as AdminLandingPages;
use App\Controllers\Admin\RedirectsController as AdminRedirects;

$router = new Router();

// ── Frontend ─────────────────────────────────────────────────────────────────
$router->get('/',                      [HomeController::class, 'index']);
$router->get('/ueber-uns',             [AboutController::class, 'index']);
$router->get('/leistungen',            [ServicesController::class, 'index']);
$router->get('/leistungen/{slug}',     [ServicesController::class, 'show']);
$router->get('/fahrzeuge',             [VehiclesController::class, 'index']);
$router->get('/fahrzeuge/{slug}',      [VehiclesController::class, 'show']);
$router->get('/karriere',              [CareerController::class, 'index']);
$router->get('/kontakt',               [ContactController::class, 'index']);
$router->get('/impressum',             [LegalController::class, 'imprint']);
$router->get('/datenschutz',           [LegalController::class, 'privacy']);
$router->get('/sitemap.xml',           [SitemapController::class, 'xml']);
$router->get('/sitemap_index.xml',     [SitemapController::class, 'index']);
$router->get('/sitemap-images.xml',    [SitemapController::class, 'imageSitemap']);
$router->get('/robots.txt',            [SitemapController::class, 'robots']);
$router->get('/llms.txt',              [SitemapController::class, 'llmsTxt']);
$router->get('/ai.txt',                [SitemapController::class, 'aiTxt']);

// ── Dynamic OG image per vehicle (PHP+GD, file-cached) ───────────────────
$router->get('/og/vehicle/{slug}.png', [OgImageController::class, 'vehicle']);

// ── Server-side tagging endpoint (vendor-neutral relay) ──────────────────
$router->post('/api/track',            [TrackController::class, 'event']);

// ── Admin ────────────────────────────────────────────────────────────────────
$router->get('/admin',                 [AdminDashboard::class, 'index']);
$router->get('/admin/login',           [AdminAuth::class, 'showLogin']);
$router->post('/admin/login',          [AdminAuth::class, 'login']);
$router->post('/admin/logout',         [AdminAuth::class, 'logout']);
$router->get('/admin/account',         [AdminAuth::class, 'showAccount']);
$router->post('/admin/account/password', [AdminAuth::class, 'changePassword']);

$router->get('/admin/pages',                   [AdminPages::class, 'index']);
$router->get('/admin/pages/{id}/edit',         [AdminPages::class, 'edit']);
$router->post('/admin/pages/{id}',             [AdminPages::class, 'update']);

$router->get('/admin/vehicles',                [AdminVehicles::class, 'index']);
$router->get('/admin/vehicles/new',            [AdminVehicles::class, 'create']);
$router->post('/admin/vehicles',               [AdminVehicles::class, 'store']);
$router->get('/admin/vehicles/{id}/edit',      [AdminVehicles::class, 'edit']);
$router->post('/admin/vehicles/{id}',          [AdminVehicles::class, 'update']);
$router->post('/admin/vehicles/{id}/delete',   [AdminVehicles::class, 'delete']);

$router->get('/admin/services',                [AdminServices::class, 'index']);
$router->get('/admin/services/new',            [AdminServices::class, 'create']);
$router->post('/admin/services',               [AdminServices::class, 'store']);
$router->get('/admin/services/{id}/edit',      [AdminServices::class, 'edit']);
$router->post('/admin/services/{id}',          [AdminServices::class, 'update']);
$router->post('/admin/services/{id}/delete',   [AdminServices::class, 'delete']);

$router->get('/admin/timeline',                [AdminTimeline::class, 'index']);
$router->get('/admin/timeline/new',            [AdminTimeline::class, 'create']);
$router->post('/admin/timeline',               [AdminTimeline::class, 'store']);
$router->get('/admin/timeline/{id}/edit',      [AdminTimeline::class, 'edit']);
$router->post('/admin/timeline/{id}',          [AdminTimeline::class, 'update']);
$router->post('/admin/timeline/{id}/delete',   [AdminTimeline::class, 'delete']);

$router->get('/admin/media',                   [AdminMedia::class, 'index']);
$router->post('/admin/media/upload',           [AdminMedia::class, 'upload']);
$router->post('/admin/media/{id}/alt',         [AdminMedia::class, 'updateAlt']);
$router->post('/admin/media/{id}/delete',      [AdminMedia::class, 'delete']);

$router->get('/admin/settings',                [AdminSettings::class, 'edit']);
$router->post('/admin/settings',               [AdminSettings::class, 'update']);

$router->get('/admin/faqs',                    [AdminFaqs::class, 'index']);
$router->get('/admin/faqs/new',                [AdminFaqs::class, 'create']);
$router->post('/admin/faqs',                   [AdminFaqs::class, 'store']);
$router->get('/admin/faqs/{id}/edit',          [AdminFaqs::class, 'edit']);
$router->post('/admin/faqs/{id}',              [AdminFaqs::class, 'update']);
$router->post('/admin/faqs/{id}/delete',       [AdminFaqs::class, 'delete']);

$router->get('/admin/audit',                   [AdminAudit::class, 'index']);
$router->get('/admin/attribution',             [AdminAudit::class, 'attribution']);

$router->get('/admin/landing-pages',           [AdminLandingPages::class, 'index']);
$router->get('/admin/landing-pages/new',       [AdminLandingPages::class, 'create']);
$router->post('/admin/landing-pages',          [AdminLandingPages::class, 'store']);
$router->get('/admin/landing-pages/{id}/edit', [AdminLandingPages::class, 'edit']);
$router->post('/admin/landing-pages/{id}',     [AdminLandingPages::class, 'update']);
$router->post('/admin/landing-pages/{id}/delete', [AdminLandingPages::class, 'delete']);

$router->get('/admin/redirects',               [AdminRedirects::class, 'index']);
$router->get('/admin/redirects/new',           [AdminRedirects::class, 'create']);
$router->post('/admin/redirects',              [AdminRedirects::class, 'store']);
$router->get('/admin/redirects/{id}/edit',     [AdminRedirects::class, 'edit']);
$router->post('/admin/redirects/{id}',         [AdminRedirects::class, 'update']);
$router->post('/admin/redirects/{id}/delete',  [AdminRedirects::class, 'delete']);

// ── Catch-all: Landing pages (industry / city) ───────────────────────────
// Registered LAST so it does not shadow known top-level paths. The
// LandingPagesController itself returns 404 when the slug is reserved
// (admin, api, og, install, sitemap, robots, etc.) or when no DB row
// matches the slug.
$router->get('/{slug}',                [LandingPagesController::class, 'show']);

// ── Dispatch ─────────────────────────────────────────────────────────────────
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$uri    = parse_url((string) ($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH) ?: '/';

$router->dispatch($method, $uri);

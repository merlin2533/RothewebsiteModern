<?php

declare(strict_types=1);

require __DIR__ . '/../src/bootstrap.php';

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
$router->get('/sitemap-images.xml',    [SitemapController::class, 'imageSitemap']);
$router->get('/robots.txt',            [SitemapController::class, 'robots']);
$router->get('/llms.txt',              [SitemapController::class, 'llmsTxt']);
$router->get('/ai.txt',                [SitemapController::class, 'aiTxt']);

// ── Dynamic OG image per vehicle (PHP+GD, file-cached) ───────────────────
$router->get('/og/vehicle/{slug}.png', [OgImageController::class, 'vehicle']);

// ── Server-side tagging endpoint (vendor-neutral relay) ──────────────────
$router->post('/api/track',            [TrackController::class, 'event']);

// ── Landing pages (industry / city) – registered LAST so it doesn't shadow
// known top-level paths like /fahrzeuge or /admin (slug constraint matches all
// non-slash, but the router walks routes in registration order).
$router->get('/{slug}',                [LandingPagesController::class, 'show']);

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

// ── Dispatch ─────────────────────────────────────────────────────────────────
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$uri    = parse_url((string) ($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH) ?: '/';

$router->dispatch($method, $uri);

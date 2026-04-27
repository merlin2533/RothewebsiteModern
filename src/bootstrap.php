<?php

declare(strict_types=1);

// ── Config ────────────────────────────────────────────────────────────────────
$config = require __DIR__ . '/config.php';
$GLOBALS['config'] = $config;

// ── Error reporting ───────────────────────────────────────────────────────────
$isDev = ($config['app_env'] ?? 'production') === 'development';
error_reporting(E_ALL);
ini_set('display_errors', $isDev ? '1' : '0');
ini_set('log_errors', '1');
ini_set('error_log', $config['base_dir'] . '/data/php-errors.log');
@mkdir($config['base_dir'] . '/data', 0750, true);

// ── Autoload (PSR-4-ish for namespace App\) ──────────────────────────────────
spl_autoload_register(static function (string $class) use ($config): void {
    $prefix = 'App\\';
    if (!str_starts_with($class, $prefix)) {
        return;
    }
    $relative = substr($class, strlen($prefix));
    // App\Core\Foo → src/Foo.php (Core lives directly in src/)
    if (str_starts_with($relative, 'Core\\')) {
        $relative = substr($relative, 5);
    }
    $file = $config['base_dir'] . '/src/' . str_replace('\\', '/', $relative) . '.php';
    if (is_readable($file)) {
        require $file;
    }
});

// ── Helpers ───────────────────────────────────────────────────────────────────
require_once __DIR__ . '/helpers.php';

// ── Sessions (in data/sessions) ───────────────────────────────────────────────
$sessionDir = $config['base_dir'] . '/data/sessions';
@mkdir($sessionDir, 0750, true);
session_save_path($sessionDir);

$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https')
    || (($_SERVER['SERVER_PORT'] ?? 0) == 443);

// SameSite=Lax instead of Strict: Strict drops the cookie when the user
// arrives via any cross-site context (e.g. clicking a link from email,
// search engine, the post-redirect after login). Lax keeps the session
// stable for normal navigation while still blocking CSRF-relevant
// scenarios. Our explicit CSRF token gives the additional protection.
session_set_cookie_params([
    'lifetime' => 0,
    'path'     => '/',
    'domain'   => '',
    'secure'   => $isHttps,
    'httponly' => true,
    'samesite' => 'Lax',
]);
ini_set('session.use_strict_mode', '1');
ini_set('session.gc_maxlifetime', (string) ($config['session_lifetime'] ?? 3600));

// ── Database & repositories ───────────────────────────────────────────────────
use App\Core\Database;

$pdo = Database::getInstance($config['db_path']);
$GLOBALS['pdo'] = $pdo;

$pageRepo     = new \App\Repositories\PageRepository($pdo);
$vehicleRepo  = new \App\Repositories\VehicleRepository($pdo);
$serviceRepo  = new \App\Repositories\ServiceRepository($pdo);
$timelineRepo = new \App\Repositories\TimelineRepository($pdo);
$settingRepo  = new \App\Repositories\SettingRepository($pdo);
$mediaRepo    = new \App\Repositories\MediaRepository($pdo);
$faqRepo      = new \App\Repositories\FaqRepository($pdo);
$landingRepo  = new \App\Repositories\LandingPageRepository($pdo);
$redirectRepo = new \App\Repositories\RedirectRepository($pdo);

$GLOBALS['pageRepo']     = $pageRepo;
$GLOBALS['vehicleRepo']  = $vehicleRepo;
$GLOBALS['serviceRepo']  = $serviceRepo;
$GLOBALS['timelineRepo'] = $timelineRepo;
$GLOBALS['settingRepo']  = $settingRepo;
$GLOBALS['mediaRepo']    = $mediaRepo;
$GLOBALS['faqRepo']      = $faqRepo;
$GLOBALS['landingRepo']  = $landingRepo;
$GLOBALS['redirectRepo'] = $redirectRepo;

// ── 301 Redirect Map (DB-pflegbar) – vor allem anderen pruefen ────────────
\App\Core\RedirectMap::handle($redirectRepo);

// ── UTM / Click-ID Capture (silent, GET-only, no admin/asset paths) ───────
\App\Core\UtmCapture::run();

<?php

declare(strict_types=1);

/**
 * Configuration loader.
 * Reads .env if present, otherwise falls back to .env.example defaults.
 * Returns a plain array – no global state, no side effects.
 */

$baseDir = dirname(__DIR__);

// ── Very simple .env loader ──────────────────────────────────────────────────
$envFile = $baseDir . '/.env';
if (!is_readable($envFile)) {
    $envFile = $baseDir . '/.env.example';
}

$env = [];
if (is_readable($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        // Skip comments and lines without '='
        if ($line === '' || $line[0] === '#' || !str_contains($line, '=')) {
            continue;
        }
        [$key, $value] = explode('=', $line, 2);
        $env[trim($key)] = trim($value);
    }
}

// Helper to read env with fallback default
$e = static function (string $key, string $default = '') use ($env): string {
    return $env[$key] ?? $default;
};

// ── Build config array ────────────────────────────────────────────────────────
$dbPath = $e('DB_PATH', 'data/database.sqlite');
if (!str_starts_with($dbPath, '/')) {
    $dbPath = $baseDir . '/' . $dbPath;
}

$uploadsPath = $e('UPLOADS_PATH', 'uploads');
if (!str_starts_with($uploadsPath, '/')) {
    $uploadsPath = $baseDir . '/' . $uploadsPath;
}

return [
    'site_url'              => rtrim($e('SITE_URL', 'https://rothe-transporte.de'), '/'),
    'db_path'               => $dbPath,
    'uploads_path'          => $uploadsPath,
    'uploads_url'           => '/uploads',
    'admin_default_user'    => $e('ADMIN_DEFAULT_USER', 'admin'),
    'admin_default_password'=> $e('ADMIN_DEFAULT_PASSWORD', 'ChangeMe!2026'),
    'session_lifetime'      => (int) $e('SESSION_LIFETIME', '3600'),
    'app_env'               => $e('APP_ENV', 'production'),
    'base_dir'              => $baseDir,
];

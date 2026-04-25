<?php

declare(strict_types=1);

if (!function_exists('e')) {
    function e(?string $s): string
    {
        return htmlspecialchars((string) $s, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
}

if (!function_exists('asset')) {
    function asset(string $path): string
    {
        return '/assets/' . ltrim($path, '/');
    }
}

if (!function_exists('url')) {
    function url(string $path = ''): string
    {
        $config = $GLOBALS['config'] ?? [];
        $base = rtrim($config['site_url'] ?? '', '/');
        return $base . '/' . ltrim($path, '/');
    }
}

if (!function_exists('active_nav')) {
    function active_nav(string $slug, string $current): string
    {
        return $slug === $current ? ' aria-current="page"' : '';
    }
}

if (!function_exists('format_meters')) {
    function format_meters(?float $m): string
    {
        if ($m === null) {
            return '–';
        }
        return number_format($m, 2, ',', '.') . ' m';
    }
}

if (!function_exists('format_kg')) {
    function format_kg(?int $kg): string
    {
        if ($kg === null) {
            return '–';
        }
        if ($kg % 1000 === 0) {
            return ($kg / 1000) . ' t';
        }
        return number_format($kg, 0, ',', '.') . ' kg';
    }
}

if (!function_exists('settings')) {
    /** @return string|array<string,string> */
    function settings(?string $key = null, ?string $default = null): mixed
    {
        $repo = $GLOBALS['settingRepo'] ?? null;
        if (!$repo) {
            return $key === null ? [] : ($default ?? '');
        }
        if ($key === null) {
            return $repo->all();
        }
        return $repo->get($key, $default ?? '');
    }
}

if (!function_exists('setting')) {
    function setting(string $key, string $default = ''): string
    {
        return (string) settings($key, $default);
    }
}

if (!function_exists('media_url')) {
    function media_url(?array $media): string
    {
        if (!$media || empty($media['filename'])) {
            return '';
        }
        return '/uploads/' . ltrim((string) $media['filename'], '/');
    }
}

if (!function_exists('redirect')) {
    function redirect(string $path, int $status = 302): void
    {
        header('Location: ' . $path, true, $status);
        exit;
    }
}

if (!function_exists('flash')) {
    function flash(string $key, ?string $value = null): ?string
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        if ($value !== null) {
            $_SESSION['_flash'][$key] = $value;
            return null;
        }
        $val = $_SESSION['_flash'][$key] ?? null;
        unset($_SESSION['_flash'][$key]);
        return $val;
    }
}

if (!function_exists('post')) {
    function post(string $key, ?string $default = null): ?string
    {
        $v = $_POST[$key] ?? null;
        return $v !== null ? trim((string) $v) : $default;
    }
}

if (!function_exists('safe_html')) {
    /**
     * Whitelist-based HTML sanitiser for admin-supplied rich text.
     * Allowed tags: p, h2, h3, h4, ul, ol, li, strong, em, a, br, img, blockquote.
     */
    function safe_html(string $html): string
    {
        $allowed = '<p><h2><h3><h4><ul><ol><li><strong><em><a><br><img><blockquote>';
        $clean = strip_tags($html, $allowed);
        // Strip on* attributes and javascript: URIs
        $clean = preg_replace('#\s*on[a-z]+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)#i', '', $clean) ?? $clean;
        $clean = preg_replace('#\s*(href|src)\s*=\s*("\s*javascript:[^"]*"|\'\s*javascript:[^\']*\')#i', ' $1="#"', $clean) ?? $clean;
        return $clean;
    }
}

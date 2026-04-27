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
        $rel  = '/assets/' . ltrim($path, '/');
        $base = $GLOBALS['config']['base_dir'] ?? null;
        if ($base) {
            $abs = $base . '/public' . $rel;
            if (is_file($abs)) {
                $rel .= '?v=' . substr((string) filemtime($abs), -8);
            }
        }
        return $rel;
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
    function media_url(?array $media, int $width = 1600, string $format = 'jpg'): string
    {
        if (!$media || empty($media['filename'])) {
            return '';
        }
        return (string) (\App\Core\MediaResolver::variantUrl($media, $width, $format) ?? '');
    }
}

if (!function_exists('media_picture')) {
    /**
     * Render a responsive <picture> element with WebP + JPG sources and srcset.
     *
     * @param array|null $media   Row from the `media` table
     * @param string     $alt     Alt text (falls back to media.alt_text)
     * @param string     $sizes   CSS sizes attribute (default 100vw)
     * @param array      $widths  Variants to emit (descending). Defaults to 1600/1200/800
     * @param string     $loading 'lazy' | 'eager'
     * @param string     $cls     Additional CSS classes for the <img>
     */
    function media_picture(?array $media, string $alt = '', string $sizes = '100vw',
                           array $widths = [1600, 1200, 800], string $loading = 'lazy',
                           string $cls = '', string $fetchpriority = ''): string
    {
        if (!$media) return '';
        $altText = $alt !== '' ? $alt : (string) ($media['alt_text'] ?? '');

        $jpgSrcset  = [];
        $webpSrcset = [];
        $largestJpg = '';
        // $widths ist absteigend sortiert (1600, 1200, 800) – der ERSTE
        // erfolgreiche jpg-URL ist die groesste Variante.
        foreach ($widths as $w) {
            $j = \App\Core\MediaResolver::variantUrl($media, (int) $w, 'jpg');
            $p = \App\Core\MediaResolver::variantUrl($media, (int) $w, 'webp');
            if ($j) {
                $jpgSrcset[] = $j . ' ' . (int) $w . 'w';
                if ($largestJpg === '') $largestJpg = $j;
            }
            if ($p) { $webpSrcset[] = $p . ' ' . (int) $w . 'w'; }
        }
        if ($largestJpg === '') {
            // Fallback fuer Pre-Resize-Eintraege
            $largestJpg = '/uploads/' . ltrim((string) $media['filename'], '/');
        }

        $w = (int) ($media['width']  ?? 0);
        $h = (int) ($media['height'] ?? 0);
        $dim = ($w && $h) ? sprintf(' width="%d" height="%d"', $w, $h) : '';
        $clsAttr = $cls !== '' ? ' class="' . e($cls) . '"' : '';
        $fpAttr  = $fetchpriority !== '' ? ' fetchpriority="' . e($fetchpriority) . '"' : '';

        $html  = '<picture>';
        if ($webpSrcset) {
            $html .= '<source type="image/webp" srcset="' . e(implode(', ', $webpSrcset)) . '" sizes="' . e($sizes) . '">';
        }
        if ($jpgSrcset) {
            $html .= '<source type="image/jpeg" srcset="' . e(implode(', ', $jpgSrcset)) . '" sizes="' . e($sizes) . '">';
        }
        $html .= '<img src="' . e($largestJpg) . '" alt="' . e($altText) . '" loading="' . e($loading) . '" decoding="async"' . $fpAttr . $dim . $clsAttr . '>';
        $html .= '</picture>';
        return $html;
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

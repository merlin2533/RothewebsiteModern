<?php

declare(strict_types=1);

/**
 * fetch_originals.php
 * ============================================================================
 * Crawlt rothe-transporte.de und laedt alle gefundenen Bilder herunter.
 *
 * - Folgt einer kleinen, festen Liste an Seiten (kein wildes Web-Crawling).
 * - Sammelt alle <img>, og:image, <source srcset> und CSS background-image
 *   URLs, die auf rothe-transporte.de zeigen.
 * - Speichert sie unter public/assets/images/from-original/<filename>.
 * - Schreibt eine Manifest-Datei (JSON) mit Quelle und Alt-Text pro Bild,
 *   damit man im Admin spaeter zuordnen kann, welches Bild zu welcher
 *   Seite/Fahrzeug gehoert.
 *
 * CLI-Aufruf:
 *   php scripts/fetch_originals.php
 *   SOURCE_HOST=https://rothe-transporte.de php scripts/fetch_originals.php
 *
 * Web-Aufruf (vom Hosting-Server, falls SSH fehlt):
 *   /scripts/fetch_originals.php   (kann ueber install.php gestartet werden)
 *
 * Wenn die Quell-Site nicht erreichbar ist (HTTP != 200), bricht das Skript
 * mit einem klaren Hinweis ab und veraendert nichts.
 */

$baseDir   = dirname(__DIR__);
$source    = rtrim(getenv('SOURCE_HOST') ?: 'https://rothe-transporte.de', '/');
$outDir    = $baseDir . '/public/assets/images/from-original';
$manifestF = $baseDir . '/public/assets/images/from-original/_manifest.json';
@mkdir($outDir, 0755, true);

$pages = [
    '/'                  => 'home',
    '/elementor-1287/'   => 'about',     // Ueber uns
    '/elementor-1248/'   => 'vehicles',  // Fahrzeuge
    '/elementor-1854/'   => 'career',    // Karriere
];

$ua = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 RotheTransportFetcher/1.0';

function http_get(string $url, string $ua): array
{
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_USERAGENT      => $ua,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT        => 30,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_SSL_VERIFYHOST => 2,
    ]);
    $body = curl_exec($ch);
    $code = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
    $type = (string) curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
    $err  = curl_error($ch);
    curl_close($ch);
    return ['code' => $code, 'body' => $body === false ? '' : $body, 'type' => $type, 'err' => $err];
}

function clean_filename(string $url): string
{
    $path = (string) parse_url($url, PHP_URL_PATH);
    $name = basename($path);
    $name = preg_replace('/[^A-Za-z0-9._-]+/', '_', $name) ?: 'asset';
    if ($name === '' || str_starts_with($name, '.')) {
        $name = 'asset_' . substr(md5($url), 0, 8);
    }
    return $name;
}

function strip_size_suffix(string $name): string
{
    // Wordpress liefert oft  foo-1024x768.jpg / foo-scaled.jpg / foo-150x150.png
    $name = preg_replace('/-\d+x\d+(\.[a-z]+)$/i', '$1', $name) ?? $name;
    $name = preg_replace('/-scaled(\.[a-z]+)$/i',    '$1', $name) ?? $name;
    return $name;
}

// ── 1. Pages laden ──────────────────────────────────────────────────────────
echo "── Probe {$source}/ ...\n";
$probe = http_get($source . '/', $ua);
if ($probe['code'] !== 200) {
    fwrite(STDERR, "  ✗ Quell-Site nicht erreichbar: HTTP {$probe['code']}\n");
    fwrite(STDERR, "    {$probe['err']}\n");
    fwrite(STDERR, "  Tip: Skript auf einem Server starten, der rothe-transporte.de abrufen kann.\n");
    exit(1);
}
echo "  ✓ erreichbar.\n\n";

$found = [];           // url => ['source_pages' => [...], 'alts' => [...]]
$pageHtml = [];

foreach ($pages as $path => $key) {
    echo "── Lese {$source}{$path} ...\n";
    $resp = http_get($source . $path, $ua);
    if ($resp['code'] !== 200) {
        echo "  ⚠ HTTP {$resp['code']}, ueberspringe.\n";
        continue;
    }
    $pageHtml[$key] = $resp['body'];
    $html = $resp['body'];

    // <img src="..." alt="...">
    if (preg_match_all('/<img\b[^>]*?>/i', $html, $imgs)) {
        foreach ($imgs[0] as $tag) {
            preg_match('/\bsrc=["\']([^"\']+)["\']/i', $tag, $m);
            if (!$m) continue;
            $src = $m[1];
            $alt = '';
            if (preg_match('/\balt=["\']([^"\']*)["\']/i', $tag, $am)) $alt = $am[1];

            // Resolve relative URLs
            if (str_starts_with($src, '//')) $src = 'https:' . $src;
            elseif (str_starts_with($src, '/')) $src = $source . $src;
            elseif (!preg_match('#^https?://#i', $src)) $src = $source . '/' . ltrim($src, '/');

            // Only same-host
            if (!str_contains($src, parse_url($source, PHP_URL_HOST) ?? 'rothe-transporte.de')) continue;

            // Skip data URIs
            if (str_starts_with($src, 'data:')) continue;

            $found[$src]['source_pages'][$key] = $key;
            if ($alt !== '') $found[$src]['alts'][] = $alt;
        }
    }

    // og:image / twitter:image
    if (preg_match_all('/<meta\s+(?:property|name)=["\'](?:og:image|twitter:image)["\']\s+content=["\']([^"\']+)["\']/i', $html, $m)) {
        foreach ($m[1] as $u) {
            if (str_starts_with($u, '//')) $u = 'https:' . $u;
            if (!preg_match('#^https?://#i', $u)) continue;
            $found[$u]['source_pages']['og']  = 'og:image';
        }
    }

    // srcset – pick largest
    if (preg_match_all('/\bsrcset=["\']([^"\']+)["\']/i', $html, $m)) {
        foreach ($m[1] as $set) {
            $candidates = explode(',', $set);
            $best = '';
            $bestW = 0;
            foreach ($candidates as $c) {
                $parts = preg_split('/\s+/', trim($c));
                if (!$parts) continue;
                $u = $parts[0];
                $w = isset($parts[1]) && preg_match('/^(\d+)w$/', $parts[1], $wm) ? (int) $wm[1] : 0;
                if ($w > $bestW) { $best = $u; $bestW = $w; }
            }
            if ($best) {
                if (!preg_match('#^https?://#i', $best)) $best = $source . '/' . ltrim($best, '/');
                $found[$best]['source_pages'][$key] = $key;
            }
        }
    }

    // CSS background-image: url(...)
    if (preg_match_all('/url\(\s*["\']?([^"\')\s]+\.(?:jpe?g|png|webp|gif|svg))/i', $html, $m)) {
        foreach ($m[1] as $u) {
            if (str_starts_with($u, '//')) $u = 'https:' . $u;
            elseif (str_starts_with($u, '/')) $u = $source . $u;
            elseif (!preg_match('#^https?://#i', $u)) $u = $source . '/' . ltrim($u, '/');
            $found[$u]['source_pages'][$key . '/css'] = $key . '/css';
        }
    }

    // logo specifically
    if (preg_match('/<link\s+rel=["\']icon["\']\s+[^>]*?href=["\']([^"\']+)["\']/i', $html, $m)) {
        $u = $m[1];
        if (str_starts_with($u, '/')) $u = $source . $u;
        $found[$u]['source_pages']['favicon'] = 'favicon';
    }

    echo "  ✓ {$path}: " . count($found) . " distinct image URLs so far.\n";
}

// ── 2. Bilder runterladen ──────────────────────────────────────────────────
$manifest = [];
$ok = 0; $fail = 0;
echo "\n── Lade " . count($found) . " Bilder herunter...\n";
foreach ($found as $url => $info) {
    // Kleinere Wordpress-Crops: bevorzuge Original (ohne -123x456)
    // Wir laden trotzdem die URL wie sie ist; der Dateiname strippt aber den Suffix.
    $name = clean_filename($url);
    $clean = strip_size_suffix($name);
    $target = $outDir . '/' . $clean;

    if (is_file($target)) {
        echo "  · {$clean} (existiert bereits)\n";
        $manifest[] = [
            'file'         => $clean,
            'source_url'   => $url,
            'source_pages' => array_values($info['source_pages'] ?? []),
            'alt'          => $info['alts'][0] ?? '',
            'status'       => 'cached',
        ];
        $ok++;
        continue;
    }

    $resp = http_get($url, $ua);
    if ($resp['code'] !== 200 || $resp['body'] === '') {
        echo "  ✗ {$url}  HTTP {$resp['code']}\n";
        $fail++;
        continue;
    }

    // mime sanity
    if (!preg_match('#^image/#i', $resp['type']) && !preg_match('/\.(jpe?g|png|webp|gif|svg|ico)$/i', $clean)) {
        echo "  ⚠ {$url} ist kein Bild ({$resp['type']}), ueberspringe.\n";
        $fail++;
        continue;
    }

    file_put_contents($target, $resp['body']);
    $bytes = strlen($resp['body']);
    echo "  ✓ {$clean} (" . number_format($bytes) . " B)\n";

    $manifest[] = [
        'file'         => $clean,
        'source_url'   => $url,
        'source_pages' => array_values($info['source_pages'] ?? []),
        'alt'          => $info['alts'][0] ?? '',
        'bytes'        => $bytes,
        'mime'         => $resp['type'],
        'status'       => 'downloaded',
    ];
    $ok++;
}

// ── 3. Manifest schreiben ──────────────────────────────────────────────────
file_put_contents(
    $manifestF,
    json_encode([
        'fetched_at' => date('c'),
        'source'     => $source,
        'count'      => count($manifest),
        'images'     => $manifest,
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT)
);

echo "\n── Zusammenfassung ──────────────────────────────────────\n";
echo "  Erfolgreich:  {$ok}\n";
echo "  Fehlgeschlagen: {$fail}\n";
echo "  Gesamt:       " . count($manifest) . "\n";
echo "  Manifest:     {$manifestF}\n";
echo "  Bilder unter: {$outDir}/\n\n";

if ($ok > 0) {
    echo "Naechste Schritte:\n";
    echo "  1. Inhalte sichten: ls -la {$outDir}\n";
    echo "  2. Manifest pruefen: cat {$manifestF} | jq '.images[] | {file,alt,source_pages}'\n";
    echo "  3. Verzeichnis ins Repo committen.\n";
    echo "  4. Im Admin (/admin/media) hochladen oder direkt aus from-original/ referenzieren.\n";
}

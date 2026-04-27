<?php

declare(strict_types=1);

/**
 * resize_uploads.php
 * ============================================================================
 * Liest jedes Bild aus uploads/ (oder aus dem Argument-Verzeichnis), erzeugt
 * mehrere Web-optimierte Varianten (JPG + WebP) und schreibt sie nach
 * public/assets/images/from-original/.
 *
 * Quellen werden NICHT veraendert. Originale bleiben in uploads/ liegen.
 * Output:
 *   public/assets/images/from-original/<basename>-<width>.jpg     (q=82)
 *   public/assets/images/from-original/<basename>-<width>.webp    (q=82)
 *   public/assets/images/from-original/_manifest.json             (Mapping)
 *
 * Variantenbreiten: 2400, 1600, 1200, 800. Up-scaling wird vermieden.
 *
 * CLI:  php scripts/resize_uploads.php
 *       php scripts/resize_uploads.php /pfad/zu/quelle
 */

set_time_limit(0);
ini_set('memory_limit', '512M');

$baseDir   = dirname(__DIR__);
$srcDir    = $argv[1] ?? ($baseDir . '/uploads');
$outDir    = $baseDir . '/public/assets/images/from-original';
$manifestF = $outDir . '/_manifest.json';
@mkdir($outDir, 0755, true);

if (!extension_loaded('gd')) {
    fwrite(STDERR, "ERROR: PHP GD-Extension fehlt.\n");
    exit(1);
}

$widths = [2400, 1600, 1200, 800];
$jpgQ   = 82;
$webpQ  = 82;

// Datei-Filter: nur Bilder, keine Helfer-Dateien
$skipNames = ['.htaccess', '.gitkeep', 'urls.txt', 'Download-Media.bat', 'Download-Media.ps1'];
$skipExts  = ['mp4', 'mov', 'avi', 'webm', 'pdf', 'txt', 'bat', 'ps1', 'zip'];

function clean_basename(string $file): string
{
    $name = pathinfo($file, PATHINFO_FILENAME);
    // Strip WordPress crop suffix and "scaled"
    $name = preg_replace('/-\d+x\d+$/', '', $name) ?? $name;
    $name = preg_replace('/-scaled$/', '', $name) ?? $name;
    // Normalize – allow only ASCII letters, digits, dash, underscore
    $name = preg_replace('/[^A-Za-z0-9._-]+/', '_', $name) ?? $name;
    return strtolower($name);
}

function load_image(string $path, string $mime)
{
    return match ($mime) {
        'image/jpeg' => @imagecreatefromjpeg($path),
        'image/png'  => @imagecreatefrompng($path),
        'image/webp' => @imagecreatefromwebp($path),
        'image/gif'  => @imagecreatefromgif($path),
        default      => false,
    };
}

$files = scandir($srcDir) ?: [];
$processed = 0;
$skipped   = 0;
$errors    = 0;
$manifest  = [];

echo "── Quelle: {$srcDir}\n";
echo "── Ziel:   {$outDir}\n";
echo "── Varianten: " . implode(', ', $widths) . " px (JPG q={$jpgQ}, WebP q={$webpQ})\n\n";

foreach ($files as $file) {
    if ($file === '.' || $file === '..') continue;
    $abs = $srcDir . '/' . $file;
    if (!is_file($abs)) continue;

    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    if (in_array($file, $skipNames, true) || in_array($ext, $skipExts, true)) {
        echo "  · skip (kein Bild): {$file}\n";
        $skipped++;
        continue;
    }

    $finfo = new \finfo(FILEINFO_MIME_TYPE);
    $mime  = (string) $finfo->file($abs);
    if (!str_starts_with($mime, 'image/')) {
        echo "  · skip ({$mime}): {$file}\n";
        $skipped++;
        continue;
    }
    if ($mime === 'image/svg+xml') {
        // SVG: einfach kopieren, keine Pixel-Behandlung
        $clean = clean_basename($file) . '.svg';
        copy($abs, $outDir . '/' . $clean);
        $manifest[] = ['source' => $file, 'output' => [$clean], 'type' => 'svg'];
        echo "  ✓ kopiert (SVG): {$clean}\n";
        $processed++;
        continue;
    }

    $img = load_image($abs, $mime);
    if (!$img) {
        echo "  ✗ kann nicht laden: {$file}\n";
        $errors++;
        continue;
    }

    $srcW = imagesx($img);
    $srcH = imagesy($img);
    $clean = clean_basename($file);

    if ($mime === 'image/png') {
        imagepalettetotruecolor($img);
        imagealphablending($img, true);
        imagesavealpha($img, true);
    }

    $entry = [
        'source'   => $file,
        'src_size' => [$srcW, $srcH],
        'mime'     => $mime,
        'outputs'  => [],
    ];

    foreach ($widths as $w) {
        if ($w >= $srcW) {
            // Nicht hoch-scalen – aber bei der Originalbreite einmal erzeugen,
            // damit alle Bilder mindestens eine optimierte Variante bekommen.
            if ($w !== $widths[0] || $srcW > $widths[1]) continue;
            $w = $srcW;
        }
        $h = (int) round($srcH * ($w / $srcW));

        $dst = imagecreatetruecolor($w, $h);
        if ($mime === 'image/png') {
            imagealphablending($dst, false);
            imagesavealpha($dst, true);
            $transparent = imagecolorallocatealpha($dst, 0, 0, 0, 127);
            imagefilledrectangle($dst, 0, 0, $w, $h, $transparent);
        }
        imagecopyresampled($dst, $img, 0, 0, 0, 0, $w, $h, $srcW, $srcH);

        // JPG (für Browser ohne WebP)
        $jpgName = "{$clean}-{$w}.jpg";
        imagejpeg($dst, $outDir . '/' . $jpgName, $jpgQ);

        // WebP (default für moderne Browser)
        $webpName = "{$clean}-{$w}.webp";
        imagewebp($dst, $outDir . '/' . $webpName, $webpQ);

        imagedestroy($dst);

        $entry['outputs'][] = [
            'width'  => $w,
            'height' => $h,
            'jpg'    => $jpgName,
            'webp'   => $webpName,
        ];
    }

    imagedestroy($img);

    if (!$entry['outputs']) {
        // Bild war kleiner als die kleinste Varianten-Breite – nimm Originalbreite
        $img = load_image($abs, $mime);
        if ($img) {
            $jpgName  = "{$clean}-{$srcW}.jpg";
            $webpName = "{$clean}-{$srcW}.webp";
            imagejpeg($img, $outDir . '/' . $jpgName, $jpgQ);
            imagewebp($img, $outDir . '/' . $webpName, $webpQ);
            imagedestroy($img);
            $entry['outputs'][] = ['width' => $srcW, 'height' => $srcH, 'jpg' => $jpgName, 'webp' => $webpName];
        }
    }

    echo "  ✓ {$clean}: " . count($entry['outputs']) . " Varianten\n";
    $manifest[] = $entry;
    $processed++;
}

file_put_contents(
    $manifestF,
    json_encode([
        'generated_at'  => date('c'),
        'src'           => $srcDir,
        'widths'        => $widths,
        'jpg_quality'   => $jpgQ,
        'webp_quality'  => $webpQ,
        'count'         => count($manifest),
        'images'        => $manifest,
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
);

echo "\n── Fertig ───────────────────────────────────────\n";
echo "  verarbeitet: {$processed}\n";
echo "  uebersprungen: {$skipped}\n";
echo "  Fehler:      {$errors}\n";
echo "  Manifest:    {$manifestF}\n";
echo "  Output-Dir:  {$outDir}\n";

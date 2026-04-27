<?php

declare(strict_types=1);

namespace App\Controllers;

/**
 * Dynamic OpenGraph image generator (1200×630 PNG) per vehicle.
 * - Brand-consistent: deep navy background + copper bar + spec strip.
 * - On-disk cache under data/og-cache/vehicle-{slug}-{mtime}.png so
 *   generation runs once until the vehicle row is updated.
 */
final class OgImageController
{
    private const W = 1200;
    private const H = 630;

    public function vehicle(array $args): void
    {
        $slug = preg_replace('/[^a-z0-9-]+/', '', (string) ($args['slug'] ?? ''));
        if (!$slug || !extension_loaded('gd')) {
            http_response_code(404);
            return;
        }

        $vehicle = $GLOBALS['vehicleRepo']->findBySlug($slug);
        if (!$vehicle) {
            http_response_code(404);
            return;
        }

        $cacheDir = $GLOBALS['config']['base_dir'] . '/data/og-cache';
        @mkdir($cacheDir, 0750, true);

        $mtime = strtotime((string) ($vehicle['updated_at'] ?? '')) ?: time();
        $cacheFile = $cacheDir . '/vehicle-' . $slug . '-' . $mtime . '.png';

        if (!is_file($cacheFile)) {
            $bin = $this->renderVehicle($vehicle);
            file_put_contents($cacheFile, $bin);
        }

        $stat = stat($cacheFile);
        $etag = '"' . substr(md5((string) $stat['mtime'] . $cacheFile), 0, 16) . '"';
        if (($_SERVER['HTTP_IF_NONE_MATCH'] ?? '') === $etag) {
            http_response_code(304);
            header('ETag: ' . $etag);
            exit;
        }

        header('Content-Type: image/png');
        header('Cache-Control: public, max-age=86400, stale-while-revalidate=604800');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $stat['mtime']) . ' GMT');
        header('ETag: ' . $etag);
        readfile($cacheFile);
    }

    private function renderVehicle(array $vehicle): string
    {
        $im = imagecreatetruecolor(self::W, self::H);
        $cBg     = imagecolorallocate($im, 11, 37, 69);     // primary navy
        $cAccent = imagecolorallocate($im, 194, 65, 12);    // copper
        $cText   = imagecolorallocate($im, 255, 255, 255);
        $cMuted  = imagecolorallocate($im, 173, 187, 207);  // light slate
        $cDark   = imagecolorallocate($im, 5, 15, 34);

        imagefilledrectangle($im, 0, 0, self::W, self::H, $cBg);

        // Subtle grid pattern
        $grid = imagecolorallocatealpha($im, 255, 255, 255, 120);
        for ($x = 0; $x < self::W; $x += 48) {
            imageline($im, $x, 0, $x, self::H, $grid);
        }
        for ($y = 0; $y < self::H; $y += 48) {
            imageline($im, 0, $y, self::W, $y, $grid);
        }

        // Copper accent bar at left
        imagefilledrectangle($im, 60, 80, 80, self::H - 80, $cAccent);

        // Brand wordmark top-right
        $brandFontSize = 22;
        imagestring($im, 5, self::W - 280, 60, 'ROTHE-TRANSPORTE', $cMuted);

        // Eyebrow
        imagestring($im, 4, 110, 80, '— FUHRPARK', $cAccent);

        // Big vehicle name (custom multi-line wrap with built-in font 5)
        $name = strtoupper((string) $vehicle['name']);
        $lines = $this->wrap($name, 22);
        $y = 140;
        foreach ($lines as $line) {
            // Use larger faux size by stacking strings (built-in fonts are bitmap; size 5 = 9×18)
            imagestring($im, 5, 110, $y, $line, $cText);
            $y += 28;
        }

        // Subtitle (special_features)
        $sub = (string) ($vehicle['special_features'] ?? '');
        if ($sub !== '') {
            $subLines = $this->wrap($sub, 60);
            $y += 16;
            foreach (array_slice($subLines, 0, 2) as $line) {
                imagestring($im, 3, 112, $y, $line, $cMuted);
                $y += 18;
            }
        }

        // Spec strip across the bottom
        $stripTop = self::H - 180;
        imagefilledrectangle($im, 0, $stripTop, self::W, self::H, $cDark);
        imagefilledrectangle($im, 0, $stripTop, self::W, $stripTop + 4, $cAccent);

        $cells = [];
        if (!empty($vehicle['length_m']) && !empty($vehicle['width_m']) && !empty($vehicle['height_m'])) {
            $cells[] = ['L × B × H', sprintf('%s × %s × %s m',
                number_format((float) $vehicle['length_m'], 2, ',', '.'),
                number_format((float) $vehicle['width_m'],  2, ',', '.'),
                number_format((float) $vehicle['height_m'], 2, ',', '.')
            )];
        }
        if (!empty($vehicle['payload_kg'])) {
            $cells[] = ['Nutzlast', ($vehicle['payload_kg'] / 1000) . ' t'];
        }
        if (!empty($vehicle['euro_pallets'])) {
            $cells[] = ['Euro-Paletten', (string) $vehicle['euro_pallets']];
        }
        if (!empty($vehicle['axles'])) {
            $cells[] = ['Achsen', (string) $vehicle['axles']];
        }

        if (!empty($cells)) {
            $cellW = (int) ((self::W - 220) / max(1, count($cells)));
            $cellY = $stripTop + 40;
            $cellX = 110;
            foreach ($cells as [$label, $value]) {
                imagestring($im, 3, $cellX, $cellY, strtoupper($label), $cMuted);
                imagestring($im, 5, $cellX, $cellY + 30, $value, $cText);
                $cellX += $cellW;
            }
        }

        // Bottom right hint
        imagestring($im, 2, self::W - 220, self::H - 30, 'rothe-transporte.de', $cMuted);

        ob_start();
        imagepng($im, null, 9);
        imagedestroy($im);
        return (string) ob_get_clean();
    }

    /** Naive word-wrap on max characters. */
    private function wrap(string $text, int $max): array
    {
        $words = preg_split('/\s+/u', trim($text)) ?: [];
        $lines = [];
        $cur = '';
        foreach ($words as $w) {
            if ($cur === '') {
                $cur = $w;
            } elseif (mb_strlen($cur . ' ' . $w) <= $max) {
                $cur .= ' ' . $w;
            } else {
                $lines[] = $cur;
                $cur = $w;
            }
        }
        if ($cur !== '') $lines[] = $cur;
        return $lines;
    }
}

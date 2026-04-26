<?php

declare(strict_types=1);

/**
 * Generate the full favicon set (PNG + ICO) from the brand mark.
 * Output: public/favicon.ico, public/apple-touch-icon.png,
 *         public/icon-192.png, public/icon-512.png, public/icon-1024.png
 *
 * The image is a flat brand tile in primary navy with copper bar and a
 * white "R" wordmark. Pure GD; no external tools required.
 */

if (!extension_loaded('gd')) {
    fwrite(STDERR, "ERROR: PHP GD extension required.\n");
    exit(1);
}

$pubDir = realpath(__DIR__ . '/..') . '/public';
@mkdir($pubDir, 0755, true);

// Palette
$primary = [11, 37, 69];   // #0B2545
$accent  = [194, 65, 12];  // #C2410C
$white   = [255, 255, 255];

/**
 * Render a square brand-tile of the given pixel size.
 */
function renderTile(int $size, array $primary, array $accent, array $white): \GdImage
{
    $im = imagecreatetruecolor($size, $size);
    imagealphablending($im, true);
    imagesavealpha($im, true);

    $cBg     = imagecolorallocate($im, ...$primary);
    $cAccent = imagecolorallocate($im, ...$accent);
    $cText   = imagecolorallocate($im, ...$white);

    // Background
    imagefilledrectangle($im, 0, 0, $size - 1, $size - 1, $cBg);

    // Copper bar at vertical center, ~20% height
    $barH = max(2, (int) round($size * 0.20));
    $barY = (int) round(($size - $barH) / 2);
    $barInset = (int) round($size * 0.18);
    imagefilledrectangle($im, $barInset, $barY, $size - 1 - $barInset, $barY + $barH - 1, $cAccent);

    // Big "R" letterform centered
    drawLetterR($im, $size, $cText);

    return $im;
}

/**
 * Draw a sturdy block-letter "R" using rectangles + an arc.
 * Sized relative to the canvas so it scales cleanly to any output size.
 */
function drawLetterR(\GdImage $im, int $size, int $color): void
{
    // Use the upper half (above the copper bar) for the glyph
    $glyphTop    = (int) round($size * 0.10);
    $glyphBottom = (int) round($size * 0.40); // sits above the bar
    $glyphHeight = $glyphBottom - $glyphTop;
    $glyphWidth  = (int) round($size * 0.55);
    $glyphLeft   = (int) round(($size - $glyphWidth) / 2);

    $stroke = max(2, (int) round($size * 0.08));

    // Vertical stem (left)
    imagefilledrectangle(
        $im,
        $glyphLeft,
        $glyphTop,
        $glyphLeft + $stroke,
        $glyphBottom,
        $color
    );

    // Top horizontal of the bowl
    imagefilledrectangle(
        $im,
        $glyphLeft,
        $glyphTop,
        $glyphLeft + $glyphWidth,
        $glyphTop + $stroke,
        $color
    );

    // Right vertical of the bowl (top half)
    $bowlMid = $glyphTop + (int) round($glyphHeight * 0.55);
    imagefilledrectangle(
        $im,
        $glyphLeft + $glyphWidth - $stroke,
        $glyphTop,
        $glyphLeft + $glyphWidth,
        $bowlMid,
        $color
    );

    // Bowl crossbar
    imagefilledrectangle(
        $im,
        $glyphLeft,
        $bowlMid - $stroke,
        $glyphLeft + $glyphWidth,
        $bowlMid,
        $color
    );

    // Diagonal leg
    $legSteps = $glyphBottom - $bowlMid;
    for ($i = 0; $i < $legSteps; $i++) {
        $x = $glyphLeft + (int) round($glyphWidth * 0.45) + (int) round($i * 0.55);
        imagefilledrectangle(
            $im,
            $x,
            $bowlMid + $i,
            $x + $stroke,
            $bowlMid + $i + 1,
            $color
        );
    }
}

// Render and save PNGs
$sizes = [
    'apple-touch-icon.png' => 180,
    'icon-192.png'         => 192,
    'icon-512.png'         => 512,
    'icon-1024.png'        => 1024,
    'favicon-32.png'       => 32,
    'favicon-16.png'       => 16,
];

foreach ($sizes as $name => $size) {
    $im = renderTile($size, $primary, $accent, $white);
    imagepng($im, $pubDir . '/' . $name, 9);
    imagedestroy($im);
    echo "  ✓ {$name} ({$size}×{$size})\n";
}

// Build favicon.ico from 16, 32 and 48 PNGs (multi-size ICO)
echo "Building favicon.ico (multi-size 16/32/48)…\n";
$icoPng = [];
foreach ([16, 32, 48] as $sz) {
    $im = renderTile($sz, $primary, $accent, $white);
    ob_start();
    imagepng($im, null, 9);
    $icoPng[$sz] = ob_get_clean();
    imagedestroy($im);
}
$icoBin = buildIco($icoPng);
file_put_contents($pubDir . '/favicon.ico', $icoBin);
echo "  ✓ favicon.ico (" . strlen($icoBin) . " bytes)\n";

/**
 * Pack PNG payloads into an ICO container.
 * @param array<int, string> $pngs Keyed by pixel size.
 */
function buildIco(array $pngs): string
{
    $count = count($pngs);
    // ICONDIR header: reserved(2) + type(2) + count(2)
    $out = pack('vvv', 0, 1, $count);

    $offset = 6 + 16 * $count; // header + N entries
    $entries = '';
    $blobs = '';
    foreach ($pngs as $size => $png) {
        $w = $size >= 256 ? 0 : $size;
        $h = $size >= 256 ? 0 : $size;
        $bytes = strlen($png);
        $entries .= pack(
            'CCCCvvVV',
            $w,           // width
            $h,           // height
            0,            // palette count
            0,            // reserved
            1,            // colour planes
            32,           // bits per pixel
            $bytes,       // image size
            $offset       // offset in file
        );
        $blobs .= $png;
        $offset += $bytes;
    }
    return $out . $entries . $blobs;
}

echo "\nDone. Files written to {$pubDir}/\n";

<?php

declare(strict_types=1);

/**
 * seed_media_from_originals.php
 * ============================================================================
 * Liest die optimierten Bilder aus public/assets/images/from-original/ ein,
 * legt fuer jede Original-Quelle einen Eintrag in der `media`-Tabelle an
 * (mit der 1600px-Variante als Hauptbild) und ordnet ueber Heuristik
 *   - das Logo der Settings-Tabelle zu (`hero_image_id`, `og_default_image_id`)
 *   - die Vehicle-Trailer-Renderings den jeweiligen Fahrzeugen
 *
 * Idempotent: wenn ein media-Eintrag mit dem gleichen filename existiert,
 * wird er erkannt und nicht doppelt angelegt.
 *
 * CLI: php scripts/seed_media_from_originals.php
 */

set_time_limit(0);
require __DIR__ . '/../src/bootstrap.php';

$baseDir   = dirname(__DIR__);
$srcDir    = $baseDir . '/public/assets/images/from-original';
$manifestF = $srcDir . '/_manifest.json';

if (!is_file($manifestF)) {
    fwrite(STDERR, "ERROR: Manifest fehlt – erst scripts/resize_uploads.php laufen lassen.\n");
    exit(1);
}

$manifest = json_decode((string) file_get_contents($manifestF), true);
if (!is_array($manifest['images'] ?? null)) {
    fwrite(STDERR, "ERROR: Manifest leer oder kaputt.\n");
    exit(1);
}

$pdo        = \App\Core\Database::getInstance();
$mediaRepo  = $GLOBALS['mediaRepo'];
$settingRepo= $GLOBALS['settingRepo'];
$vehicleRepo= $GLOBALS['vehicleRepo'];

/**
 * Filename-Heuristiken für Vehicle- und Logo-Zuordnung.
 * Reihenfolge ist absteigend nach Spezifität (das erste Match gewinnt).
 *
 * Quelle: ich kenne die Original-Dateinamen aus rothe-transporte.de:
 *   - Logo.png  + cropped-cropped-cropped-Logo_Vector.png  → Logo
 *   - Semi-Trailer1.png        → Tieflader
 *   - MegaTrailer01.png        → Tieflader (Mega-Variante)
 *   - StandardTrailer01.png    → Tautliner
 *   - CityTrailer02.png        → Anhaenger Alltagsheld / Stadt-LKW / Motorwagen
 */
$rules = [
    'logo'           => ['logo', 'logo_vector'],
    'tieflader-sattelauflieger' => ['semi-trailer', 'megatrailer'],
    'tautliner-sattelzug'       => ['standardtrailer', 'tautliner'],
    'motorwagen-mit-ladekran'   => ['citytrailer', 'city-trailer'],
];

function variant_for(array $entry, int $preferredW = 1600): ?array
{
    if (empty($entry['outputs'])) return null;
    // erst die preferred-Breite suchen, sonst die größte
    foreach ($entry['outputs'] as $o) {
        if ((int) ($o['width'] ?? 0) === $preferredW) return $o;
    }
    usort($entry['outputs'], fn($a, $b) => (int) $b['width'] - (int) $a['width']);
    return $entry['outputs'][0] ?? null;
}

function classify(string $sourceName, array $rules): ?string
{
    $low = strtolower($sourceName);
    foreach ($rules as $key => $patterns) {
        foreach ($patterns as $p) {
            if (str_contains($low, $p)) return $key;
        }
    }
    return null;
}

$mediaIdByVehicle  = [];
$mediaIdByLogo     = null;
$insertedCount     = 0;
$skippedCount      = 0;

echo "── Anlage von media-Eintraegen aus " . count($manifest['images']) . " Originalen…\n";

foreach ($manifest['images'] as $entry) {
    $variant = variant_for($entry, 1600);
    if (!$variant) continue;

    // public-relative Pfad als filename (so liest media_url() es im Frontend)
    $rel = 'from-original/' . $variant['jpg'];

    // Doppelt-Check
    $stmt = $pdo->prepare('SELECT id FROM media WHERE filename = ? LIMIT 1');
    $stmt->execute([$rel]);
    $existing = $stmt->fetch();

    if ($existing) {
        $mediaId = (int) $existing['id'];
        $skippedCount++;
    } else {
        $absPath = $variant['jpg']; // gerade nur Dateiname
        $absFile = dirname(__DIR__) . '/public/assets/images/from-original/' . $variant['jpg'];
        $size    = is_file($absFile) ? (int) filesize($absFile) : null;

        $altGuess = pathinfo($entry['source'], PATHINFO_FILENAME);
        $altGuess = preg_replace('/[-_]+/', ' ', $altGuess) ?? $altGuess;
        $altGuess = trim(preg_replace('/\b[A-F0-9]{20,}\b/i', '', $altGuess) ?? $altGuess);
        $altGuess = $altGuess !== '' ? ucfirst($altGuess) : 'Foto Rothe-Transporte';

        $mediaId = $mediaRepo->save([
            'filename'      => $rel,
            'original_name' => $entry['source'],
            'mime'          => 'image/jpeg',
            'width'         => $variant['width'] ?? null,
            'height'        => $variant['height'] ?? null,
            'alt_text'      => $altGuess,
            'size_bytes'    => $size,
        ]);
        $insertedCount++;
    }

    // Heuristische Zuordnung
    $cls = classify($entry['source'], $rules);
    if ($cls === 'logo' && $mediaIdByLogo === null) {
        $mediaIdByLogo = $mediaId;
    } elseif ($cls && $cls !== 'logo' && !isset($mediaIdByVehicle[$cls])) {
        $mediaIdByVehicle[$cls] = $mediaId;
    }
}

echo "  ✓ {$insertedCount} neue, {$skippedCount} bereits vorhandene media-Eintraege.\n\n";

// Vehicle-Bilder zuweisen
foreach ($mediaIdByVehicle as $vehicleSlug => $mediaId) {
    $v = $vehicleRepo->findBySlug($vehicleSlug);
    if (!$v) continue;
    if ((int) ($v['image_id'] ?? 0) === $mediaId) {
        echo "  · {$vehicleSlug} hat bereits image_id={$mediaId}\n";
        continue;
    }
    $v['image_id'] = $mediaId;
    $vehicleRepo->save($v);
    echo "  ✓ {$vehicleSlug} → image_id={$mediaId}\n";
}

// Logo / OG-Default
if ($mediaIdByLogo !== null) {
    $settingRepo->setMany([
        'og_default_image_id' => (string) $mediaIdByLogo,
    ]);
    echo "  ✓ Logo als og_default_image_id={$mediaIdByLogo} gesetzt.\n";
}

// Hero-Image: erstes Foto, das wie ein "Truck mit Maschine"-Motiv aussieht
$heroCandidates = ['photo-2022-11-05-10-06-16', 'whatsapp-bild-2025-07-17-um-07.10.26_3e815584', 'img-20240123-wa0030_nik'];
foreach ($heroCandidates as $stem) {
    $stmt = $pdo->prepare('SELECT id FROM media WHERE filename LIKE ? ORDER BY id DESC LIMIT 1');
    $stmt->execute(['from-original/' . $stem . '%']);
    if ($row = $stmt->fetch()) {
        $settingRepo->setMany(['hero_image_id' => (string) $row['id']]);
        echo "  ✓ Hero-Bild auf media id={$row['id']} ({$stem}) gesetzt.\n";
        break;
    }
}

echo "\nFertig.\n";

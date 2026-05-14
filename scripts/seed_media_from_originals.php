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
 * Die echten Fahrzeug-Renderings liegen unter uploads/ mit sprechenden
 * Namen (siehe Migration 010_fleet_from_original.sql):
 *   - tautliner-sattelzug.png       → Tautliner-Sattelzug
 *   - semitrailer.png               → Semitrailer
 *   - megatrailer.png               → Megatrailer
 *   - tiefbett-sattelanhaenger.png  → Tiefbett-Sattelanhänger
 *   - citytrailer-alltagsheld.png   → Citytrailer „Alltagsheld"
 */
$rules = [
    'logo'                      => ['logo', 'logo_vector'],
    'tautliner-sattelzug'       => ['tautliner-sattelzug', 'standardtrailer', 'tautliner'],
    'semitrailer'               => ['semitrailer', 'semi-trailer'],
    'megatrailer'               => ['megatrailer'],
    'tieflader-sattelauflieger' => ['tiefbett-sattelanhaenger', 'tiefbett'],
    'anhaenger-alltagsheld'     => ['citytrailer-alltagsheld', 'citytrailer', 'city-trailer'],
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

        // Descriptive German alt text based on vehicle classification or filename
        $vehicleAltMap = [
            'tautliner-sattelzug'       => 'Tautliner-Sattelzug von Rothe-Transporte',
            'semitrailer'               => 'Semitrailer von Rothe-Transporte für Spezialtransporte',
            'megatrailer'               => 'Megatrailer von Rothe-Transporte für voluminöse Güter',
            'tieflader-sattelauflieger' => 'Tiefbett-Sattelanhänger von Rothe-Transporte',
            'anhaenger-alltagsheld'     => 'Citytrailer „Alltagsheld" von Rothe-Transporte',
            'logo'                      => 'Rothe Transporte und Speditions GbR – Logo',
        ];
        $cls2 = classify($entry['source'], $rules);
        if ($cls2 && isset($vehicleAltMap[$cls2])) {
            $altGuess = $vehicleAltMap[$cls2];
        } else {
            $altGuess = pathinfo($entry['source'], PATHINFO_FILENAME);
            $altGuess = preg_replace('/[-_]+/', ' ', $altGuess) ?? $altGuess;
            $altGuess = trim(preg_replace('/\b[A-F0-9]{20,}\b/i', '', $altGuess) ?? $altGuess);
            $lc = strtolower($altGuess);
            if (str_contains($lc, 'whatsapp') || preg_match('/^\d{10,}/', $altGuess)) {
                $altGuess = 'Rothe-Transporte – Fahrzeug und Maschinentransport Foto';
            } elseif (str_contains($lc, 'img') || str_contains($lc, 'photo') || str_contains($lc, 'nik')) {
                $altGuess = 'Rothe-Transporte – Transportfahrzeug';
            }
            $altGuess = $altGuess !== '' ? ucfirst($altGuess) : 'Rothe-Transporte – Foto';
        }

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

// Vehicle-Bilder nur zuweisen, wenn das Fahrzeug noch kein Bild hat.
// Migration 010 setzt die Bilder bereits autoritativ – wir überschreiben sie
// hier nicht.
foreach ($mediaIdByVehicle as $vehicleSlug => $mediaId) {
    $v = $vehicleRepo->findBySlug($vehicleSlug);
    if (!$v) continue;
    if (!empty($v['image_id'])) {
        echo "  · {$vehicleSlug} hat bereits ein Bild – übersprungen\n";
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

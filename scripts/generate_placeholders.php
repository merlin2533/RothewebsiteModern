<?php

declare(strict_types=1);

/**
 * Generate brand-aligned SVG placeholders.
 *
 * Each placeholder is a self-contained SVG with:
 *   - deep navy gradient background
 *   - copper accent line / shape
 *   - actual stylised vehicle/scene illustration (not just a coloured box)
 *
 * Output: public/assets/images/placeholders/*.svg
 */

$out = __DIR__ . '/../public/assets/images/placeholders';
@mkdir($out, 0755, true);

// ── Brand palette ──────────────────────────────────────────────────────────
$primary  = '#0B2545';
$deep     = '#050F22';
$copper   = '#C2410C';
$bone     = '#F4F2EE';
$muted    = '#94A3B8';
$light    = '#FFFFFF';

// ── Reusable building blocks ────────────────────────────────────────────────

function defs(string $bgFrom, string $bgTo, string $copperColor): string
{
    return <<<XML
<defs>
  <linearGradient id="bg" x1="0" y1="0" x2="1" y2="1">
    <stop offset="0" stop-color="{$bgFrom}"/>
    <stop offset="1" stop-color="{$bgTo}"/>
  </linearGradient>
  <radialGradient id="halo" cx="0.7" cy="0.25" r="0.7">
    <stop offset="0" stop-color="#ffffff" stop-opacity="0.12"/>
    <stop offset="1" stop-color="#ffffff" stop-opacity="0"/>
  </radialGradient>
  <pattern id="grid" width="48" height="48" patternUnits="userSpaceOnUse">
    <path d="M 48 0 L 0 0 0 48" fill="none" stroke="#ffffff" stroke-opacity="0.04" stroke-width="1"/>
  </pattern>
  <linearGradient id="copperGrad" x1="0" y1="0" x2="1" y2="0">
    <stop offset="0" stop-color="{$copperColor}"/>
    <stop offset="1" stop-color="#E5734A"/>
  </linearGradient>
</defs>
XML;
}

/** Stylised tractor cab (the truck pulling unit). Origin = bottom-front of cab. */
function cab(int $x, int $y, string $color, float $opacity = 1.0): string
{
    // x = front-bottom corner, y = ground level
    $cx = $x + 110; $top = $y - 200; $hood = $y - 130; $front = $x + 130;
    return <<<SVG
<g fill="{$color}" fill-opacity="{$opacity}">
  <!-- hood -->
  <path d="M {$x},{$y} L {$x},{$hood} Q {$x},{$hood} {$x},{$hood}
           L {$front},{$hood}
           Q {$front},{$top} {$front},{$top}
           L {$cx},{$top}
           Q {$cx},{$top} {$cx},{$top}
           L {$cx},{$y} Z"/>
  <!-- windscreen highlight -->
  <path d="M {$front} {$hood} L {$cx} {$top} L {$cx} {$hood} Z" fill="#ffffff" fill-opacity="0.08"/>
</g>
SVG;
}

/** Pair of wheels at the given x-positions. */
function wheels(string $color, int ...$cx): string
{
    $g = '';
    foreach ($cx as $x) {
        $g .= "<circle cx=\"{$x}\" cy=\"\" r=\"42\" fill=\"#000\"/>";
    }
    return $g;
}

function wheelGroup(int $groundY, string $tyre, string $rim, int ...$cx): string
{
    $g = '';
    foreach ($cx as $x) {
        $cy = $groundY - 42;
        $g .= "<circle cx=\"{$x}\" cy=\"{$cy}\" r=\"42\" fill=\"{$tyre}\"/>";
        $g .= "<circle cx=\"{$x}\" cy=\"{$cy}\" r=\"16\" fill=\"{$rim}\"/>";
    }
    return $g;
}

/** Wordmark text block (multiline). */
function wordmark(int $x, int $y, string $eyebrow, string $headline, string $sub,
                  string $copperColor, string $textColor): string
{
    $eb = htmlspecialchars($eyebrow);
    $hl = htmlspecialchars($headline);
    $sb = htmlspecialchars($sub);
    return <<<SVG
<g font-family="ui-sans-serif, system-ui, -apple-system, 'Segoe UI', sans-serif">
  <rect x="{$x}" y="{$y}" width="36" height="3" fill="{$copperColor}"/>
  <text x="{$x}" y="{$y}" dy="28" font-size="14" font-weight="600"
        letter-spacing="3.5" fill="{$copperColor}" text-transform="uppercase">{$eb}</text>
  <text x="{$x}" y="{$y}" dy="92" font-size="64" font-weight="700"
        letter-spacing="-2" fill="{$textColor}">{$hl}</text>
  <text x="{$x}" y="{$y}" dy="130" font-size="20" font-weight="400"
        fill="{$textColor}" fill-opacity="0.7">{$sb}</text>
</g>
SVG;
}

// ── Vehicle illustrations (side profile, ~1500 px wide stages) ──────────────

function tieflader(int $cx, int $cy, string $body, string $copperColor): string
{
    // cx,cy = midpoint of the vehicle on the ground line (ground = cy)
    // Tieflader: tractor + low-bed semi (drops in the middle).
    $g  = '';
    // Tractor cab (left)
    $g .= cab($cx - 700, $cy, $body);
    // Low-bed silhouette: high front section, drop, long low section, raised back
    $g .= "<path d='
        M ".($cx - 460).",".($cy - 130)."
        L ".($cx - 360).",".($cy - 130)."
        L ".($cx - 320).",".($cy - 95)."
        L ".($cx - 320).",".($cy - 60)."
        L ".($cx + 380).",".($cy - 60)."
        L ".($cx + 380).",".($cy - 110)."
        L ".($cx + 460).",".($cy - 110)."
        L ".($cx + 460).",".($cy - 30)."
        L ".($cx - 460).",".($cy - 30)."
        Z' fill='{$body}'/>";
    // Copper hairline along top (industrial detail)
    $g .= "<path d='M ".($cx - 320).",".($cy - 60)." L ".($cx + 380).",".($cy - 60)."'
            stroke='{$copperColor}' stroke-width='3' fill='none'/>";
    // Wheels: 2 tractor + 4 trailer
    $g .= wheelGroup($cy, '#0a0a12', '#3a4a64',
        $cx - 660, $cx - 580,            // tractor
        $cx + 250, $cx + 320,            // trailer rear
        $cx + 380, $cx + 450             // trailer back
    );
    return $g;
}

function tautliner(int $cx, int $cy, string $body, string $copperColor, string $accent): string
{
    $g  = cab($cx - 700, $cy, $body);
    // Box trailer
    $g .= "<rect x='".($cx - 460)."' y='".($cy - 240)."' width='".(920)."' height='".(210)."' fill='{$body}'/>";
    // Vertical curtain stripes
    for ($i = 0; $i < 12; $i++) {
        $sx = $cx - 440 + ($i * 75);
        $g .= "<line x1='{$sx}' y1='".($cy - 240)."' x2='{$sx}' y2='".($cy - 30)."'
                stroke='#ffffff' stroke-opacity='0.07' stroke-width='2'/>";
    }
    // Copper top edge
    $g .= "<rect x='".($cx - 460)."' y='".($cy - 240)."' width='920' height='6' fill='{$copperColor}'/>";
    $g .= wheelGroup($cy, '#0a0a12', '#3a4a64',
        $cx - 660, $cx - 580,
        $cx + 230, $cx + 300, $cx + 380
    );
    return $g;
}

function anhaenger(int $cx, int $cy, string $body, string $copperColor): string
{
    // Just a small drawbar trailer with rear ramp.
    $g  = "<rect x='".($cx - 350)."' y='".($cy - 130)."' width='600' height='100' fill='{$body}'/>";
    // ramp tilted down at back
    $g .= "<path d='M ".($cx + 250).",".($cy - 40)." L ".($cx + 380).",".($cy)." L ".($cx + 380).",".($cy - 28)." L ".($cx + 270).",".($cy - 60)." Z'
            fill='{$body}'/>";
    // tow bar
    $g .= "<rect x='".($cx - 460)."' y='".($cy - 60)."' width='110' height='12' fill='{$body}'/>";
    // copper hairline
    $g .= "<line x1='".($cx - 350)."' y1='".($cy - 130)."' x2='".($cx + 250)."' y2='".($cy - 130)."'
            stroke='{$copperColor}' stroke-width='3'/>";
    $g .= wheelGroup($cy, '#0a0a12', '#3a4a64', $cx - 200, $cx - 80, $cx + 80);
    return $g;
}

function motorwagen(int $cx, int $cy, string $body, string $copperColor): string
{
    // Truck with mounted crane behind the cab.
    $g  = cab($cx - 600, $cy, $body);
    // Box body
    $g .= "<rect x='".($cx - 380)."' y='".($cy - 220)."' width='620' height='190' fill='{$body}'/>";
    $g .= "<rect x='".($cx - 380)."' y='".($cy - 220)."' width='620' height='5' fill='{$copperColor}'/>";
    // Crane base
    $g .= "<rect x='".($cx - 350)."' y='".($cy - 290)."' width='80' height='70' fill='{$body}'/>";
    // Crane vertical mast
    $g .= "<rect x='".($cx - 320)."' y='".($cy - 410)."' width='30' height='130' fill='{$body}'/>";
    // Crane horizontal arm
    $g .= "<path d='M ".($cx - 305).",".($cy - 410)." L ".($cx + 60).",".($cy - 380)." L ".($cx + 60).",".($cy - 360)." L ".($cx - 305).",".($cy - 380)." Z'
            fill='{$copperColor}'/>";
    // Hook
    $g .= "<line x1='".($cx + 60)."' y1='".($cy - 380)."' x2='".($cx + 60)."' y2='".($cy - 280)."' stroke='{$body}' stroke-width='3'/>";
    $g .= "<circle cx='".($cx + 60)."' cy='".($cy - 270)."' r='10' fill='{$copperColor}'/>";
    $g .= wheelGroup($cy, '#0a0a12', '#3a4a64',
        $cx - 560, $cx - 480,
        $cx + 100, $cx + 180
    );
    return $g;
}

// ── SVG factory ─────────────────────────────────────────────────────────────

function svgHero(int $w, int $h, string $eyebrow, string $headline, string $sub,
                 string $vehicle, string $bgFrom, string $bgTo,
                 string $copperColor, string $textColor): string
{
    $defs = defs($bgFrom, $bgTo, $copperColor);
    $cx = (int) ($w * 0.55);
    $cy = (int) ($h * 0.78); // ground line
    $vehicleArt = '';
    switch ($vehicle) {
        case 'tieflader':  $vehicleArt = tieflader($cx, $cy, '#1B2D4D', $copperColor); break;
        case 'tautliner':  $vehicleArt = tautliner($cx, $cy, '#1B2D4D', $copperColor, $textColor); break;
        case 'anhaenger':  $vehicleArt = anhaenger($cx, $cy, '#1B2D4D', $copperColor); break;
        case 'motorwagen': $vehicleArt = motorwagen($cx, $cy, '#1B2D4D', $copperColor); break;
        case 'crane':      $vehicleArt = motorwagen($cx, $cy, '#1B2D4D', $copperColor); break;
        case 'fleet':      // 3 silhouettes
            $vehicleArt = tautliner($cx - 400, $cy, '#13223D', $copperColor, $textColor)
                . tieflader($cx + 100, $cy, '#1B2D4D', $copperColor);
            break;
        case 'workshop':
            // workshop scene: building + truck
            $vehicleArt = "<rect x='".($cx - 500)."' y='".($cy - 320)."' width='1100' height='280' fill='#13223D'/>"
                . "<rect x='".($cx - 500)."' y='".($cy - 330)."' width='1100' height='12' fill='{$copperColor}'/>"
                . "<rect x='".($cx - 380)."' y='".($cy - 240)."' width='180' height='200' fill='{$bgFrom}'/>"
                . "<rect x='".($cx - 60)."' y='".($cy - 240)."' width='180' height='200' fill='{$bgFrom}'/>"
                . "<rect x='".($cx + 260)."' y='".($cy - 240)."' width='180' height='200' fill='{$bgFrom}'/>"
                . tautliner($cx, $cy, '#1B2D4D', $copperColor, $textColor);
            break;
        case 'road':
            $vehicleArt = "<path d='M 0 {$cy} L {$w} ".($cy - 30)." L {$w} ".($cy + 30)." L 0 ".($cy + 30)." Z'
                            fill='#13223D'/>"
                . "<line x1='0' y1='{$cy}' x2='{$w}' y2='".($cy - 30)."'
                          stroke='{$copperColor}' stroke-width='4' stroke-dasharray='30 20'/>"
                . motorwagen($cx, $cy, '#1B2D4D', $copperColor);
            break;
        case 'office':
            $vehicleArt = "<rect x='".($cx - 350)."' y='".($cy - 360)."' width='800' height='320' fill='#13223D'/>"
                . "<rect x='".($cx - 350)."' y='".($cy - 360)."' width='800' height='8' fill='{$copperColor}'/>";
            for ($r = 0; $r < 4; $r++) {
                for ($c = 0; $c < 7; $c++) {
                    $vx = $cx - 320 + ($c * 110);
                    $vy = $cy - 320 + ($r * 70);
                    $vehicleArt .= "<rect x='{$vx}' y='{$vy}' width='80' height='44' fill='{$bgFrom}'/>";
                }
            }
            break;
        default:
            $vehicleArt = tautliner($cx, $cy, '#1B2D4D', $copperColor, $textColor);
    }

    $wm = wordmark(80, (int)($h * 0.22), $eyebrow, $headline, $sub, $copperColor, $textColor);

    return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 {$w} {$h}" preserveAspectRatio="xMidYMid slice"
     role="img" aria-label="{$headline}">
  {$defs}
  <rect width="{$w}" height="{$h}" fill="url(#bg)"/>
  <rect width="{$w}" height="{$h}" fill="url(#grid)"/>
  <rect width="{$w}" height="{$h}" fill="url(#halo)"/>

  <!-- ground line -->
  <rect x="0" y="" width="{$w}" height="3" fill="#000" fill-opacity="0.25"
        transform="translate(0 ".((int)($h * 0.78)).")"/>

  <!-- vehicle / scene -->
  {$vehicleArt}

  <!-- copper accent strip bottom -->
  <rect x="0" y="" width="{$w}" height="6" fill="url(#copperGrad)"
        transform="translate(0 ".($h - 6).")"/>

  <!-- wordmark -->
  {$wm}
</svg>
SVG;
}

// ── Render set ──────────────────────────────────────────────────────────────

$set = [
    // [filename,                w,    h,    eyebrow,             headline,                  sub,                          scene,        bgFrom,    bgTo]
    ['hero-home.svg',           1920, 1080, 'MASCHINENTRANSPORT · SEIT 1978', 'Schweres bewegen.', 'Tieflader, Tautliner, Spezialanhänger – aus Walddorfhäslach in sechs Länder.', 'tieflader',   $primary, $deep],
    ['hero-about.svg',          1920,  720, 'FAMILIENBETRIEB',                'Zwei Generationen Spedition.', 'Karl-Otto und Christopher Rothe – seit 1978 in Süddeutschland.',         'workshop',    $primary, $deep],
    ['hero-services.svg',       1920,  720, 'LEISTUNGEN',                     'Vier Disziplinen.',  'Maschinen-, Spezialtransport, Lagerung, Stapler-Dienst.',                'fleet',       $primary, $deep],
    ['hero-vehicles.svg',       1920,  720, 'FUHRPARK',                       'Vier Fahrzeuge.',    'Tieflader 28 t · Tautliner 25 t · Anhänger · Motorwagen mit Ladekran.',  'fleet',       $deep,    $primary],
    ['hero-career.svg',         1920,  720, 'KARRIERE',                       'Stamm-Mannschaft.',  'Berufskraftfahrer (CE) für unsere Touren in sechs Länder.',              'road',        $primary, $deep],
    ['hero-contact.svg',        1920,  720, 'KONTAKT',                        'Direkt zur Disposition.', 'Aeckerlesweg 3 · 72141 Walddorfhäslach · 07127 18231.',              'office',      $deep,    $primary],

    ['vehicle-tieflader.svg',   1600,  900, 'TIEFLADER 28 T',                 '13,60 × 2,48 × 3,70 m', '33 Europaletten · niedrige Ladehöhe · für sperrige Maschinen.',     'tieflader',   $primary, $deep],
    ['vehicle-tautliner.svg',   1600,  900, 'TAUTLINER 25 T',                 '13,60 × 2,48 × 2,70 m', '33 Europaletten · beidseitig öffnende Schiebeplane.',                'tautliner',   $deep,    $primary],
    ['vehicle-anhaenger.svg',   1600,  900, 'ANHÄNGER „ALLTAGSHELD"',         'Klein. Wendig. Robust.', 'Auffahrrampe · Lenkachse · ebenerdiges Be- und Entladen.',          'anhaenger',   $primary, $deep],
    ['vehicle-motorwagen.svg',  1600,  900, 'MOTORWAGEN MIT LADEKRAN',        'Punktgenau abgesetzt.', 'Ladekran · Lenkachse · für enge Baustellen und Innenstadt.',         'motorwagen',  $deep,    $primary],

    ['quote-portrait.svg',      1200,  900, 'INHABER',                        '„Wir bewegen Maschinen seit 1978."', 'Karl-Otto & Christopher Rothe',                       'workshop',    $primary, $deep],
    ['og-default.svg',          1200,  630, 'ROTHE-TRANSPORTE',               'Maschinentransport seit 1978.', 'Walddorfhäslach · DE · AT · CH · FR · NL · BE',                    'tieflader',   $primary, $deep],
];

foreach ($set as $row) {
    [$file, $w, $h, $eyebrow, $headline, $sub, $scene, $bgFrom, $bgTo] = $row;
    $xml = svgHero($w, $h, $eyebrow, $headline, $sub, $scene, $bgFrom, $bgTo, $copper, $light);
    file_put_contents($out . '/' . $file, $xml);
    echo "  ✓ {$file} ({$w}×{$h})\n";
}

echo "\nGenerated " . count($set) . " SVG illustrations in {$out}\n";

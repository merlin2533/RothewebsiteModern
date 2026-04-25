<?php

declare(strict_types=1);

/**
 * Generate SVG placeholders for hero images and vehicle photos in the
 * "Editorial Industrial" colour palette (deep navy + burnt copper).
 * Output: public/assets/images/placeholders/*.svg
 */

$out = __DIR__ . '/../public/assets/images/placeholders';
@mkdir($out, 0755, true);

$primary = '#0B2545';
$primaryDeep = '#050F22';
$accent = '#C2410C';
$surface = '#F4F2EE';
$muted = '#6B7785';

function svg(int $w, int $h, string $bg, string $fg, string $accent, string $label, string $sub = ''): string
{
    $halfW = (int) ($w / 2);
    $halfH = (int) ($h / 2);
    return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 {$w} {$h}" preserveAspectRatio="xMidYMid slice" role="img" aria-label="{$label}">
  <defs>
    <linearGradient id="g" x1="0" y1="0" x2="0" y2="1">
      <stop offset="0" stop-color="{$bg}" stop-opacity="1"/>
      <stop offset="1" stop-color="{$bg}" stop-opacity="0.85"/>
    </linearGradient>
    <pattern id="p" width="48" height="48" patternUnits="userSpaceOnUse">
      <path d="M 48 0 L 0 0 0 48" fill="none" stroke="{$fg}" stroke-opacity="0.06" stroke-width="1"/>
    </pattern>
  </defs>
  <rect width="{$w}" height="{$h}" fill="url(#g)"/>
  <rect width="{$w}" height="{$h}" fill="url(#p)"/>
  <rect x="0" y="{$halfH}" width="{$w}" height="6" fill="{$accent}"/>
  <text x="{$halfW}" y="{$halfH}" dy="-24" text-anchor="middle"
        font-family="ui-sans-serif, system-ui, sans-serif" font-size="32" font-weight="600"
        fill="{$fg}" letter-spacing="-0.02em">{$label}</text>
  <text x="{$halfW}" y="{$halfH}" dy="32" text-anchor="middle"
        font-family="ui-sans-serif, system-ui, sans-serif" font-size="14" font-weight="400"
        fill="{$fg}" fill-opacity="0.7" letter-spacing="0.08em">{$sub}</text>
</svg>
SVG;
}

$sets = [
    ['hero-home.svg',           1920, 1080, $primary,      '#FFFFFF', $accent, 'ROTHE-TRANSPORTE',   'PLATZHALTER · TIEFLADER MIT MASCHINE'],
    ['hero-about.svg',          1920,  720, $primaryDeep,  '#FFFFFF', $accent, 'FAMILIENBETRIEB',    'PLATZHALTER · WERKSTATTHOF'],
    ['hero-services.svg',       1920,  720, $primary,      '#FFFFFF', $accent, 'LEISTUNGEN',         'PLATZHALTER · LADUNGSSICHERUNG'],
    ['hero-vehicles.svg',       1920,  720, $primaryDeep,  '#FFFFFF', $accent, 'FUHRPARK',           'PLATZHALTER · TIEFLADER'],
    ['hero-career.svg',         1920,  720, $primary,      '#FFFFFF', $accent, 'KARRIERE',           'PLATZHALTER · CE-FAHRER'],
    ['hero-contact.svg',        1920,  720, $primaryDeep,  '#FFFFFF', $accent, 'KONTAKT',            'PLATZHALTER · BÜRO'],
    ['vehicle-tieflader.svg',   1600,  900, $primary,      '#FFFFFF', $accent, 'TIEFLADER 28 t',     'PLATZHALTER'],
    ['vehicle-tautliner.svg',   1600,  900, $primaryDeep,  '#FFFFFF', $accent, 'TAUTLINER 25 t',     'PLATZHALTER'],
    ['vehicle-anhaenger.svg',   1600,  900, $primary,      '#FFFFFF', $accent, 'ANHÄNGER',           'PLATZHALTER'],
    ['vehicle-motorwagen.svg',  1600,  900, $primaryDeep,  '#FFFFFF', $accent, 'MOTORWAGEN',         'PLATZHALTER'],
    ['quote-portrait.svg',      1200,  900, $primary,      '#FFFFFF', $accent, 'INHABER',            'PLATZHALTER · PORTRAIT'],
    ['og-default.svg',          1200,  630, $primary,      '#FFFFFF', $accent, 'ROTHE-TRANSPORTE',   'MASCHINENTRANSPORTE · SEIT 1978'],
];

// Fix the typo with ) instead of ] in source
foreach ($sets as $set) {
    if (count($set) !== 8) continue;
    [$file, $w, $h, $bg, $fg, $ac, $lbl, $sub] = $set;
    file_put_contents($out . '/' . $file, svg($w, $h, $bg, $fg, $ac, $lbl, $sub));
    echo "  ✓ {$file}\n";
}

echo "\nGenerated SVG placeholders in {$out}\n";

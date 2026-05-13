<?php
/** @var array $page */
/** @var array $vehicle */
/** @var array<int,array> $allVehicles */
declare(strict_types=1);

$v = $vehicle;
$phoneE164 = setting('phone_e164', '+497127182310');
$email = setting('email', 'info@rothe-transporte.de');

// Schema.org Vehicle for structured data
$structured_data = [
    [
        '@context' => 'https://schema.org',
        '@type'    => 'Vehicle',
        'name'     => $v['name'],
        'description' => strip_tags((string) ($v['marketing_text'] ?? '')),
        'numberOfAxles' => $v['axles'] !== null ? (int) $v['axles'] : null,
        'cargoVolume'   => $v['euro_pallets'] !== null ? ($v['euro_pallets'] . ' Europaletten') : null,
        'weightTotal'   => $v['payload_kg'] !== null ? [
            '@type' => 'QuantitativeValue',
            'value' => (int) $v['payload_kg'],
            'unitCode' => 'KGM',
        ] : null,
    ],
    [
        '@context' => 'https://schema.org',
        '@type'    => 'BreadcrumbList',
        'itemListElement' => [
            ['@type' => 'ListItem', 'position' => 1, 'name' => 'Startseite', 'item' => url('/')],
            ['@type' => 'ListItem', 'position' => 2, 'name' => 'Fahrzeuge',  'item' => url('/fahrzeuge')],
            ['@type' => 'ListItem', 'position' => 3, 'name' => $v['name']],
        ],
    ],
];
?>
<section class="hero hero--inner" data-reveal>
  <div class="container">
    <nav aria-label="Breadcrumb" class="breadcrumb">
      <a href="/">Startseite</a> · <a href="/fahrzeuge">Fahrzeuge</a> · <span aria-current="page"><?= e($v['name']) ?></span>
    </nav>
    <p class="eyebrow">Unser Fuhrpark</p>
    <h1><?= e($v['name']) ?></h1>
    <p class="hero__sub"><?= e($v['special_features'] ?? '') ?></p>
  </div>
</section>

<section class="section vehicle-detail" data-reveal>
  <div class="container vehicle-detail__grid">
    <figure class="vehicle-detail__media">
      <?php
        $vMedia = !empty($v['image_id']) ? $GLOBALS['mediaRepo']->find((int) $v['image_id']) : null;
        if ($vMedia) {
            echo media_picture($vMedia, $v['image_alt'] ?? $v['name'],
                '(min-width: 900px) 60vw, 100vw', [1600, 1200, 800], 'eager', '', 'high');
        } else {
            $fallback = '/assets/images/placeholders/vehicle-' . preg_replace('#-.*$#', '', $v['slug']) . '.svg';
            echo '<img src="' . e($fallback) . '" alt="' . e($v['name']) . '" loading="eager">';
        }
      ?>
    </figure>
    <div class="vehicle-detail__body">
      <p class="vehicle-detail__lead"><?= e($v['marketing_text'] ?? '') ?></p>
      <dl class="spec-list spec-list--detail">
        <div><dt>Länge</dt><dd><?= e(format_meters($v['length_m'] !== null ? (float) $v['length_m'] : null)) ?></dd></div>
        <div><dt>Breite</dt><dd><?= e(format_meters($v['width_m'] !== null ? (float) $v['width_m'] : null)) ?></dd></div>
        <div><dt>Höhe</dt><dd><?= e(format_meters($v['height_m'] !== null ? (float) $v['height_m'] : null)) ?></dd></div>
        <div><dt>Nutzlast</dt><dd><?= e(format_kg($v['payload_kg'] !== null ? (int) $v['payload_kg'] : null)) ?></dd></div>
        <div><dt>Europaletten</dt><dd><?= e($v['euro_pallets'] !== null ? (string) $v['euro_pallets'] : '–') ?></dd></div>
        <div><dt>Achsen</dt><dd><?= e($v['axles'] !== null ? (string) $v['axles'] : '–') ?></dd></div>
        <?php if (!empty($v['special_features'])): ?>
        <div class="spec-list__wide"><dt>Besonderheit</dt><dd><?= e($v['special_features']) ?></dd></div>
        <?php endif; ?>
      </dl>
      <div class="vehicle-detail__cta">
        <a class="btn btn--primary" href="mailto:<?= e($email) ?>?subject=<?= rawurlencode('Anfrage: ' . $v['name']) ?>">Dieses Fahrzeug anfragen</a>
        <a class="btn btn--ghost" href="tel:<?= e($phoneE164) ?>">Lieber anrufen</a>
      </div>
    </div>
  </div>
</section>

<section class="section section--surface" data-reveal>
  <div class="container">
    <header class="section__head">
      <p class="eyebrow">Was wir sonst noch haben</p>
      <h2>Unsere weiteren Fahrzeuge</h2>
    </header>
    <ul class="card-grid card-grid--3">
      <?php foreach ($allVehicles as $other): if ($other['id'] === $v['id']) continue;
        $otherMedia = !empty($other['image_filename']) ? [
            'filename' => $other['image_filename'],
            'alt_text' => $other['image_alt'] ?? $other['name'],
            'width'    => $other['image_width'] ?? null,
            'height'   => $other['image_height'] ?? null,
        ] : null;
      ?>
      <li class="card card--vehicle-mini">
        <a href="/fahrzeuge/<?= e($other['slug']) ?>">
          <div class="card__media">
            <?php if ($otherMedia):
              echo media_picture($otherMedia, $other['image_alt'] ?? $other['name'],
                  '(min-width: 700px) 30vw, 100vw', [800, 400], 'lazy');
            else: ?>
            <img loading="lazy" decoding="async"
                 src="/assets/images/placeholders/vehicle-<?= e(preg_replace('#-.*$#', '', $other['slug'])) ?>.svg"
                 alt="<?= e($other['name']) ?>">
            <?php endif; ?>
          </div>
          <h3><?= e($other['name']) ?></h3>
          <p><?= e($other['special_features'] ?? '') ?></p>
        </a>
      </li>
      <?php endforeach; ?>
    </ul>
  </div>
</section>

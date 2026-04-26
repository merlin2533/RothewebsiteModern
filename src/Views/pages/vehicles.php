<?php
/** @var array $page */
/** @var array<int,array> $vehicles */
declare(strict_types=1);

$phoneE164 = setting('phone_e164', '+497127182310');
$phoneDisplay = setting('phone', '07127 18231');
?>
<section class="hero hero--inner" data-reveal>
  <div class="container">
    <p class="eyebrow">Fuhrpark</p>
    <h1><?= e($page['hero_headline'] ?? 'Fahrzeuge') ?></h1>
    <p class="hero__sub"><?= e($page['hero_sub'] ?? '') ?></p>
  </div>
</section>

<section class="section section--surface compare" data-reveal>
  <div class="container">
    <header class="section__head">
      <p class="eyebrow">Vergleich</p>
      <h2>Alle Fahrzeuge auf einen Blick</h2>
    </header>
    <div class="table-wrap">
      <table class="spec-compare">
        <caption class="visually-hidden">Vergleich aller Fahrzeuge mit Maßen, Nutzlast und Paletten.</caption>
        <thead>
          <tr>
            <th scope="col">Fahrzeug</th>
            <th scope="col">Länge</th>
            <th scope="col">Breite</th>
            <th scope="col">Höhe</th>
            <th scope="col">Nutzlast</th>
            <th scope="col">Paletten</th>
            <th scope="col">Achsen</th>
            <th scope="col">Besonderheit</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($vehicles as $v): ?>
          <tr>
            <th scope="row"><a href="/fahrzeuge/<?= e($v['slug']) ?>"><?= e($v['name']) ?></a></th>
            <td class="num"><?= e(format_meters($v['length_m'] !== null ? (float) $v['length_m'] : null)) ?></td>
            <td class="num"><?= e(format_meters($v['width_m'] !== null ? (float) $v['width_m'] : null)) ?></td>
            <td class="num"><?= e(format_meters($v['height_m'] !== null ? (float) $v['height_m'] : null)) ?></td>
            <td class="num"><?= e(format_kg($v['payload_kg'] !== null ? (int) $v['payload_kg'] : null)) ?></td>
            <td class="num"><?= e($v['euro_pallets'] !== null ? (string) $v['euro_pallets'] : '–') ?></td>
            <td class="num"><?= e($v['axles'] !== null ? (string) $v['axles'] : '–') ?></td>
            <td><?= e($v['special_features'] ?? '') ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</section>

<?php $i = 0; foreach ($vehicles as $v): $i++; $alt = $i % 2 === 0; ?>
<section class="section vehicle-block <?= $alt ? 'vehicle-block--alt' : '' ?>" data-reveal>
  <div class="container vehicle-block__grid">
    <div class="vehicle-block__media">
      <p class="card__index"><?= str_pad((string) $i, 2, '0', STR_PAD_LEFT) ?> / <?= e(strtoupper($v['name'])) ?></p>
      <img loading="lazy" decoding="async"
           src="<?= e($v['image_filename'] ? '/uploads/' . $v['image_filename'] : '/assets/images/placeholders/vehicle-' . preg_replace('#-.*$#', '', $v['slug']) . '.svg') ?>"
           alt="<?= e($v['image_alt'] ?? $v['name']) ?>">
    </div>
    <div class="vehicle-block__body">
      <h2><?= e($v['name']) ?></h2>
      <p class="vehicle-block__lead"><?= e($v['marketing_text'] ?? '') ?></p>
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
      <a class="btn btn--ghost" href="/fahrzeuge/<?= e($v['slug']) ?>">Detailansicht
        <svg width="16" height="16" aria-hidden="true"><use href="#icon-arrow-right"/></svg></a>
    </div>
  </div>
</section>
<?php endforeach; ?>

<section class="section section--cta cta-final" data-reveal>
  <div class="container cta-final__inner">
    <p class="eyebrow">Passendes Fahrzeug nicht dabei?</p>
    <p class="cta-final__phone industrial-num industrial-num--outline">
      <a href="tel:<?= e($phoneE164) ?>"><?= e($phoneDisplay) ?></a>
    </p>
    <p class="cta-final__sub">Wir disponieren auch Sondertransporte. Rufen Sie uns an.</p>
  </div>
</section>

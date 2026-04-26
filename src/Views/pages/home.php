<?php
/** @var array $page */
/** @var array<int,array> $services */
/** @var array<int,array> $vehicles */
declare(strict_types=1);

$phoneE164 = setting('phone_e164', '+497127182310');
$phoneDisplay = setting('phone', '07127 18231');
$email = setting('email', 'info@rothe-transporte.de');
?>
<section class="hero hero--home" data-reveal>
  <div class="hero__media" aria-hidden="true">
    <img src="/assets/images/placeholders/hero-home.svg" alt="" loading="eager" fetchpriority="high">
  </div>
  <div class="hero__panel">
    <div class="container">
      <p class="eyebrow">Maschinentransport &middot; seit <?= e(setting('founded_year', '1978')) ?></p>
      <h1 class="hero__headline"><?= e($page['hero_headline'] ?? 'Schweres bewegen.') ?></h1>
      <p class="hero__sub"><?= e($page['hero_sub'] ?? '') ?></p>
      <div class="hero__cta">
        <a class="btn btn--primary" href="mailto:<?= e($email) ?>?subject=<?= rawurlencode('Transportanfrage') ?>">
          Anfrage senden
          <svg width="16" height="16" aria-hidden="true"><use href="#icon-arrow-right"/></svg>
        </a>
        <a class="btn btn--ghost" href="/fahrzeuge">Fuhrpark ansehen</a>
      </div>
    </div>
  </div>
</section>

<section class="section section--dark stats" data-reveal>
  <div class="container stats__grid">
    <div class="stat">
      <span class="stat__num industrial-num" data-counter="<?= e(setting('founded_year', '1978')) ?>"><?= e(setting('founded_year', '1978')) ?></span>
      <span class="stat__label">Gegründet</span>
    </div>
    <div class="stat">
      <span class="stat__num industrial-num"><span data-counter="28">0</span> t</span>
      <span class="stat__label">Maximale Nutzlast</span>
    </div>
    <div class="stat">
      <span class="stat__num industrial-num"><span data-counter="6">0</span></span>
      <span class="stat__label">Länder befahren</span>
    </div>
    <div class="stat">
      <span class="stat__num industrial-num">2.</span>
      <span class="stat__label">Generation</span>
    </div>
  </div>
</section>

<section class="section services-teaser" data-reveal>
  <div class="container">
    <header class="section__head">
      <p class="eyebrow">Leistungen</p>
      <h2>Vier Disziplinen. Ein Anspruch.</h2>
    </header>
    <ul class="card-grid card-grid--4">
      <?php foreach ($services as $s): ?>
      <li class="card card--service">
        <span class="card__icon" aria-hidden="true">
          <svg width="32" height="32"><use href="#icon-<?= e($s['icon_key'] ?? 'truck') ?>"/></svg>
        </span>
        <h3><a href="/leistungen/<?= e($s['slug']) ?>"><?= e($s['title']) ?></a></h3>
        <p><?= e($s['summary'] ?? '') ?></p>
      </li>
      <?php endforeach; ?>
    </ul>
  </div>
</section>

<section class="section section--surface fleet-highlights" data-reveal>
  <div class="container">
    <header class="section__head">
      <p class="eyebrow">Fuhrpark</p>
      <h2>Klar gerechnet, fair geladen.</h2>
    </header>
    <ul class="card-grid card-grid--2">
      <?php $i = 0; foreach ($vehicles as $v): $i++; ?>
      <li class="card card--vehicle">
        <p class="card__index"><?= str_pad((string) $i, 2, '0', STR_PAD_LEFT) ?> / <?= e(strtoupper($v['name'])) ?></p>
        <a class="card__media" href="/fahrzeuge/<?= e($v['slug']) ?>">
          <img loading="lazy" decoding="async"
               src="<?= e($v['image_filename'] ? '/uploads/' . $v['image_filename'] : '/assets/images/placeholders/vehicle-' . preg_replace('#-.*$#', '', $v['slug']) . '.svg') ?>"
               alt="<?= e($v['image_alt'] ?? $v['name']) ?>">
        </a>
        <h3 class="card__title"><a href="/fahrzeuge/<?= e($v['slug']) ?>"><?= e($v['name']) ?></a></h3>
        <dl class="spec-list">
          <div><dt>Maße (L × B × H)</dt><dd><?= e(format_meters($v['length_m'] !== null ? (float) $v['length_m'] : null)) ?> × <?= e(format_meters($v['width_m'] !== null ? (float) $v['width_m'] : null)) ?> × <?= e(format_meters($v['height_m'] !== null ? (float) $v['height_m'] : null)) ?></dd></div>
          <div><dt>Nutzlast</dt><dd><?= e(format_kg($v['payload_kg'] !== null ? (int) $v['payload_kg'] : null)) ?></dd></div>
          <div><dt>Europaletten</dt><dd><?= e($v['euro_pallets'] !== null ? (string) $v['euro_pallets'] : '–') ?></dd></div>
        </dl>
        <a class="card__cta" href="/fahrzeuge/<?= e($v['slug']) ?>">Detailansicht <svg width="14" height="14" aria-hidden="true"><use href="#icon-arrow-right"/></svg></a>
      </li>
      <?php endforeach; ?>
    </ul>
    <p class="section__more"><a class="btn btn--ghost" href="/fahrzeuge">Gesamten Fuhrpark ansehen</a></p>
  </div>
</section>

<section class="section industries" data-reveal>
  <div class="container">
    <p class="eyebrow">Branchen, die wir kennen</p>
    <ul class="industries__list">
      <li>Maschinen- &amp; Anlagenbau</li>
      <li>Automotive</li>
      <li>Landmaschinentechnik</li>
      <li>Industriegüter &amp; palettierte Ware</li>
    </ul>
  </div>
</section>

<section class="section section--haze quote" data-reveal>
  <div class="container quote__inner">
    <blockquote>
      <p>„<?= e(setting('owner_quote', '')) ?>"</p>
      <footer>— <?= e(setting('owner_quote_attribution', '')) ?></footer>
    </blockquote>
    <div class="quote__media" aria-hidden="true">
      <img src="/assets/images/placeholders/quote-portrait.svg" alt="" loading="lazy">
    </div>
  </div>
</section>

<div class="cargo-strap" aria-hidden="true">
  <span></span><span></span><span></span>
</div>

<section class="section section--cta cta-final" data-reveal>
  <div class="container cta-final__inner">
    <p class="eyebrow">Direkt zur Disposition</p>
    <p class="cta-final__phone industrial-num industrial-num--outline">
      <a href="tel:<?= e($phoneE164) ?>"><?= e($phoneDisplay) ?></a>
    </p>
    <p class="cta-final__sub">Mo–Fr 07:00–17:00 · oder per E-Mail an <a href="mailto:<?= e($email) ?>"><?= e($email) ?></a></p>
  </div>
</section>

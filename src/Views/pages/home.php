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
    <?php
      // Bevorzuge echtes Hero-Foto aus Settings, falle auf SVG-Platzhalter zurueck.
      $heroId = (int) setting('hero_image_id', '0');
      $heroMedia = $heroId > 0 ? $GLOBALS['mediaRepo']->find($heroId) : null;
      if ($heroMedia) {
          echo media_picture($heroMedia, '', '100vw', [1600, 1200, 800], 'eager', '', 'high');
      } else {
          echo '<img src="/assets/images/placeholders/hero-home.svg" alt="" loading="eager" fetchpriority="high">';
      }
    ?>
  </div>
  <div class="hero__panel">
    <div class="container">
      <p class="eyebrow">Familienspedition &middot; seit <?= e(setting('founded_year', '1978')) ?></p>
      <h1 class="hero__headline"><?= e($page['hero_headline'] ?? 'Ihre Maschine in guten Händen.') ?></h1>
      <p class="hero__sub"><?= e($page['hero_sub'] ?? '') ?></p>
      <div class="hero__cta">
        <a class="btn btn--primary" href="mailto:<?= e($email) ?>?subject=<?= rawurlencode('Transportanfrage') ?>">
          Jetzt Anfrage senden
          <svg width="16" height="16" aria-hidden="true"><use href="#icon-arrow-right"/></svg>
        </a>
        <a class="btn btn--ghost" href="/fahrzeuge">Unsere Fahrzeuge</a>
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
      <span class="stat__label">Bis zu Nutzlast</span>
    </div>
    <div class="stat">
      <span class="stat__num industrial-num"><span data-counter="6">0</span></span>
      <span class="stat__label">Länder, in denen wir fahren</span>
    </div>
    <div class="stat">
      <span class="stat__num industrial-num">2.</span>
      <span class="stat__label">Generation in der Familie</span>
    </div>
  </div>
</section>

<section class="section services-teaser" data-reveal>
  <div class="container">
    <header class="section__head">
      <p class="eyebrow">Was wir für Sie tun</p>
      <h2>Alles, was Sie für Ihren Transport brauchen.</h2>
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
      <p class="eyebrow">Unser Fuhrpark</p>
      <h2>Für jede Maschine das richtige Fahrzeug.</h2>
    </header>
    <ul class="card-grid card-grid--2">
      <?php $i = 0; foreach ($vehicles as $v): $i++;
        $vMedia = !empty($v['image_filename']) ? [
            'filename' => $v['image_filename'],
            'alt_text' => $v['image_alt'] ?? $v['name'],
            'width'    => $v['image_width'] ?? null,
            'height'   => $v['image_height'] ?? null,
        ] : null;
      ?>
      <li class="card card--vehicle">
        <p class="card__index"><?= str_pad((string) $i, 2, '0', STR_PAD_LEFT) ?> / <?= e(strtoupper($v['name'])) ?></p>
        <a class="card__media" href="/fahrzeuge/<?= e($v['slug']) ?>">
          <?php if ($vMedia):
            echo media_picture($vMedia, $v['image_alt'] ?? $v['name'],
                '(min-width: 700px) 45vw, 100vw', [800, 400], 'lazy');
          else: ?>
          <img loading="lazy" decoding="async"
               src="/assets/images/placeholders/vehicle-<?= e(preg_replace('#-.*$#', '', $v['slug'])) ?>.svg"
               alt="<?= e($v['name']) ?>">
          <?php endif; ?>
        </a>
        <h3 class="card__title"><a href="/fahrzeuge/<?= e($v['slug']) ?>"><?= e($v['name']) ?></a></h3>
        <dl class="spec-list">
          <div><dt>Maße (L × B × H)</dt><dd><?= e(format_meters($v['length_m'] !== null ? (float) $v['length_m'] : null)) ?> × <?= e(format_meters($v['width_m'] !== null ? (float) $v['width_m'] : null)) ?> × <?= e(format_meters($v['height_m'] !== null ? (float) $v['height_m'] : null)) ?></dd></div>
          <div><dt>Nutzlast</dt><dd><?= e(format_kg($v['payload_kg'] !== null ? (int) $v['payload_kg'] : null)) ?></dd></div>
          <div><dt>Europaletten</dt><dd><?= e($v['euro_pallets'] !== null ? (string) $v['euro_pallets'] : '–') ?></dd></div>
        </dl>
        <a class="card__cta" href="/fahrzeuge/<?= e($v['slug']) ?>">Mehr zum Fahrzeug <svg width="14" height="14" aria-hidden="true"><use href="#icon-arrow-right"/></svg></a>
      </li>
      <?php endforeach; ?>
    </ul>
    <p class="section__more"><a class="btn btn--ghost" href="/fahrzeuge">Alle Fahrzeuge ansehen</a></p>
  </div>
</section>

<section class="section industries" data-reveal>
  <div class="container">
    <p class="eyebrow">Ihr kompetenter Partner</p>
    <ul class="industries__list">
      <li>Maschinenbau</li>
      <li>Automobilindustrie</li>
      <li>Landmaschinentechnik</li>
      <li>Industrie &amp; Anlagenbau</li>
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
      <?php
        $portraitId = (int) setting('quote_portrait_image_id', '0');
        $portraitMedia = $portraitId > 0 ? $GLOBALS['mediaRepo']->find($portraitId) : null;
        if ($portraitMedia) {
            echo media_picture($portraitMedia, '', '(min-width:700px) 400px, 100vw', [800, 400], 'lazy');
        } else {
            echo '<img src="/assets/images/placeholders/quote-portrait.svg" alt="" loading="lazy">';
        }
      ?>
    </div>
  </div>
</section>

<?= \App\Core\View::partial('faqs', ['faqs' => $faqs ?? []]) ?>

<div class="cargo-strap" aria-hidden="true">
  <span></span><span></span><span></span>
</div>

<section class="section section--cta cta-final" data-reveal>
  <div class="container cta-final__inner">
    <p class="eyebrow">Rufen Sie uns an</p>
    <p class="cta-final__phone industrial-num industrial-num--outline">
      <a href="tel:<?= e($phoneE164) ?>"><?= e($phoneDisplay) ?></a>
    </p>
    <p class="cta-final__sub">Wir sind Mo–Fr von 7 bis 18 Uhr für Sie da · oder schreiben Sie uns: <a href="mailto:<?= e($email) ?>"><?= e($email) ?></a></p>
  </div>
</section>

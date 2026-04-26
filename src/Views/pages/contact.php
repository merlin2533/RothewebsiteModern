<?php
/** @var array $page */
declare(strict_types=1);

$phoneE164 = setting('phone_e164', '+497127182310');
$phone = setting('phone', '07127 18231');
$email = setting('email', 'info@rothe-transporte.de');
$lat = setting('geo_lat', '48.5306');
$lng = setting('geo_lng', '9.1564');
$mapBox = sprintf('%.4f,%.4f,%.4f,%.4f',
    (float) $lng - 0.015, (float) $lat - 0.01,
    (float) $lng + 0.015, (float) $lat + 0.01
);
$mapUrl = "https://www.openstreetmap.org/export/embed.html?bbox={$mapBox}&layer=mapnik&marker={$lat},{$lng}";
?>
<section class="hero hero--inner" data-reveal>
  <div class="container">
    <p class="eyebrow">Kontakt</p>
    <h1><?= e($page['hero_headline'] ?? 'Direkt zur Disposition.') ?></h1>
    <p class="hero__sub"><?= e($page['hero_sub'] ?? '') ?></p>
  </div>
</section>

<section class="section contact-tiles" data-reveal>
  <div class="container">
    <ul class="card-grid card-grid--3">
      <li class="card card--contact">
        <span class="card__icon" aria-hidden="true"><svg width="32" height="32"><use href="#icon-phone"/></svg></span>
        <h2>Telefon</h2>
        <a class="card__phone industrial-num" href="tel:<?= e($phoneE164) ?>"><?= e($phone) ?></a>
        <p class="card__hours">Mo–Fr 07:00–17:00</p>
      </li>
      <li class="card card--contact">
        <span class="card__icon" aria-hidden="true"><svg width="32" height="32"><use href="#icon-mail"/></svg></span>
        <h2>E-Mail</h2>
        <a class="card__email" href="mailto:<?= e($email) ?>"><?= e($email) ?></a>
        <p class="card__hours">Antwort meist innerhalb eines Werktags.</p>
      </li>
      <li class="card card--contact">
        <span class="card__icon" aria-hidden="true"><svg width="32" height="32"><use href="#icon-pin"/></svg></span>
        <h2>Adresse</h2>
        <address>
          <?= e(setting('company_name', 'Rothe-Transporte und Speditions GbR')) ?><br>
          <?= e(setting('address_street', '')) ?><br>
          <?= e(setting('address_zip', '')) ?> <?= e(setting('address_city', '')) ?>
        </address>
      </li>
    </ul>
  </div>
</section>

<section class="section section--surface contact-map" data-reveal>
  <div class="container">
    <header class="section__head">
      <p class="eyebrow">Anfahrt</p>
      <h2>So finden Sie zu uns</h2>
    </header>
    <div class="map-wrap">
      <iframe
        src="<?= e($mapUrl) ?>"
        width="100%" height="450" frameborder="0"
        loading="lazy" referrerpolicy="no-referrer"
        title="Karte: Standort Rothe-Transporte"></iframe>
    </div>
    <p class="map-note">
      Karte: <a href="https://www.openstreetmap.org/?mlat=<?= e($lat) ?>&mlon=<?= e($lng) ?>#map=15/<?= e($lat) ?>/<?= e($lng) ?>" rel="noopener noreferrer">Größere Karte auf openstreetmap.org</a>.
    </p>
  </div>
</section>

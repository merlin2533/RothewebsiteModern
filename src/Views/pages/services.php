<?php
/** @var array $page */
/** @var array<int,array> $services */
declare(strict_types=1);

$phoneE164 = setting('phone_e164', '+497127182310');
$email = setting('email', 'info@rothe-transporte.de');
?>
<section class="hero hero--inner" data-reveal>
  <div class="container">
    <p class="eyebrow">Was wir für Sie tun</p>
    <h1><?= e($page['hero_headline'] ?? 'Alles aus einer Hand.') ?></h1>
    <p class="hero__sub"><?= e($page['hero_sub'] ?? '') ?></p>
  </div>
</section>

<section class="section services-grid" data-reveal>
  <div class="container">
    <ul class="card-grid card-grid--4">
      <?php foreach ($services as $s): ?>
      <li class="card card--service card--service-large">
        <span class="card__icon" aria-hidden="true">
          <svg width="40" height="40"><use href="#icon-<?= e($s['icon_key'] ?? 'truck') ?>"/></svg>
        </span>
        <h2><a href="/leistungen/<?= e($s['slug']) ?>"><?= e($s['title']) ?></a></h2>
        <p><?= e($s['summary'] ?? '') ?></p>
        <a class="card__cta" href="/leistungen/<?= e($s['slug']) ?>">Mehr dazu <svg width="14" height="14" aria-hidden="true"><use href="#icon-arrow-right"/></svg></a>
      </li>
      <?php endforeach; ?>
    </ul>
  </div>
</section>

<?php $i = 0; foreach ($services as $s): $i++; $alt = $i % 2 === 0; ?>
<section class="section section--surface vehicle-block <?= $alt ? 'vehicle-block--alt' : '' ?>" data-reveal>
  <div class="container vehicle-block__grid">
    <div class="vehicle-block__media" aria-hidden="true">
      <span class="card__icon card__icon--xl">
        <svg width="120" height="120"><use href="#icon-<?= e($s['icon_key'] ?? 'truck') ?>"/></svg>
      </span>
    </div>
    <div class="vehicle-block__body">
      <p class="eyebrow"><?= str_pad((string) $i, 2, '0', STR_PAD_LEFT) ?> / Was wir tun</p>
      <h2 id="<?= e($s['slug']) ?>"><?= e($s['title']) ?></h2>
      <div class="prose"><?= $s['body_html'] ?? '' ?></div>
    </div>
  </div>
</section>
<?php endforeach; ?>

<section class="section section--cta cta-final" data-reveal>
  <div class="container cta-final__inner">
    <p class="eyebrow">Klingt passend? Sprechen wir.</p>
    <p class="cta-final__phone industrial-num industrial-num--outline">
      <a href="tel:<?= e($phoneE164) ?>"><?= e(setting('phone', '07127 18231')) ?></a>
    </p>
    <p class="cta-final__sub">Lieber schreiben? Per E-Mail an <a href="mailto:<?= e($email) ?>"><?= e($email) ?></a></p>
  </div>
</section>

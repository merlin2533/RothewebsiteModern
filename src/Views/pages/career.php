<?php
/** @var array $page */
declare(strict_types=1);

$email = setting('email', 'info@rothe-transporte.de');
?>
<section class="hero hero--inner" data-reveal>
  <div class="container">
    <p class="eyebrow">Karriere</p>
    <h1><?= e($page['hero_headline'] ?? 'Stamm-Mannschaft.') ?></h1>
    <p class="hero__sub"><?= e($page['hero_sub'] ?? '') ?></p>
  </div>
</section>

<section class="section section--cta career-cta" data-reveal>
  <div class="container">
    <div class="career-cta__inner">
      <h2>Bewerben Sie sich direkt</h2>
      <p>Schicken Sie uns Ihre Unterlagen per E-Mail oder Post – wir melden uns kurzfristig zurück.</p>
      <p class="career-cta__actions">
        <a class="btn btn--primary" href="mailto:<?= e($email) ?>?subject=<?= rawurlencode('Bewerbung Berufskraftfahrer') ?>">Bewerbung per E-Mail</a>
        <a class="btn btn--ghost" href="/kontakt">Postanschrift &amp; Telefon</a>
      </p>
    </div>
  </div>
</section>

<section class="section" data-reveal>
  <div class="container container--narrow prose">
    <?= $page['content_html'] ?? '' ?>
  </div>
</section>

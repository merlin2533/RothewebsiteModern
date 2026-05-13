<?php
/** @var array $page */
declare(strict_types=1);

$email = setting('email', 'info@rothe-transporte.de');
?>
<section class="hero hero--inner" data-reveal>
  <div class="container">
    <p class="eyebrow">Karriere bei uns</p>
    <h1><?= e($page['hero_headline'] ?? 'Werden Sie Teil unseres Teams.') ?></h1>
    <p class="hero__sub"><?= e($page['hero_sub'] ?? '') ?></p>
  </div>
</section>

<section class="section section--cta career-cta" data-reveal>
  <div class="container">
    <div class="career-cta__inner">
      <h2>Klingt gut? Dann melden Sie sich.</h2>
      <p>Schicken Sie uns ein paar Zeilen – per E-Mail oder per Post. Wir melden uns kurzfristig bei Ihnen zurück.</p>
      <p class="career-cta__actions">
        <a class="btn btn--primary" href="mailto:<?= e($email) ?>?subject=<?= rawurlencode('Bewerbung Berufskraftfahrer') ?>">Jetzt per E-Mail bewerben</a>
        <a class="btn btn--ghost" href="/kontakt">Adresse &amp; Telefon</a>
      </p>
    </div>
  </div>
</section>

<section class="section" data-reveal>
  <div class="container container--narrow prose">
    <?= $page['content_html'] ?? '' ?>
  </div>
</section>

<?php
/** @var array $page */
declare(strict_types=1);

$page['noindex'] = true;
?>
<section class="hero hero--inner hero--404" data-reveal>
  <div class="container">
    <p class="eyebrow">404</p>
    <h1><?= e($page['hero_headline'] ?? 'Hier sind wir falsch abgebogen.') ?></h1>
    <p class="hero__sub"><?= e($page['hero_sub'] ?? '') ?></p>
  </div>
</section>

<section class="section" data-reveal>
  <div class="container container--narrow prose">
    <?= $page['content_html'] ?? '' ?>
    <p>
      <a class="btn btn--primary" href="/">Zur Startseite</a>
      <a class="btn btn--ghost" href="/fahrzeuge">Unsere Fahrzeuge</a>
    </p>
  </div>
</section>

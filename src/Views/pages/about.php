<?php
/** @var array $page */
/** @var array<int,array> $events */
declare(strict_types=1);
?>
<section class="hero hero--inner" data-reveal>
  <div class="container">
    <p class="eyebrow">Über uns</p>
    <h1><?= e($page['hero_headline'] ?? 'Familienbetrieb seit 1978.') ?></h1>
    <p class="hero__sub"><?= e($page['hero_sub'] ?? '') ?></p>
  </div>
</section>

<section class="section section--dark stats" data-reveal>
  <div class="container stats__grid">
    <div class="stat">
      <span class="stat__num industrial-num"><?= e(setting('founded_year', '1978')) ?></span>
      <span class="stat__label">Gegründet</span>
    </div>
    <div class="stat">
      <span class="stat__num industrial-num">2.</span>
      <span class="stat__label">Generation</span>
    </div>
    <div class="stat">
      <span class="stat__num industrial-num">6</span>
      <span class="stat__label">Länder befahren</span>
    </div>
    <div class="stat">
      <span class="stat__num industrial-num">2018</span>
      <span class="stat__label">Ausbildungsbetrieb</span>
    </div>
  </div>
</section>

<section class="section about-content" data-reveal>
  <div class="container container--narrow prose">
    <?= $page['content_html'] ?? '' ?>
  </div>
</section>

<section class="section section--surface timeline-section" data-reveal>
  <div class="container">
    <header class="section__head">
      <p class="eyebrow">Geschichte</p>
      <h2>Was uns gemacht hat, wer wir sind.</h2>
    </header>
    <ol class="timeline">
      <?php foreach ($events as $ev): ?>
      <li class="timeline__item">
        <span class="timeline__year industrial-num industrial-num--outline"><?= e((string) $ev['year']) ?></span>
        <div class="timeline__card">
          <h3><?= e($ev['title']) ?></h3>
          <?php if (!empty($ev['description'])): ?>
          <p><?= e($ev['description']) ?></p>
          <?php endif; ?>
        </div>
      </li>
      <?php endforeach; ?>
    </ol>
  </div>
</section>

<section class="section values" data-reveal>
  <div class="container">
    <header class="section__head">
      <p class="eyebrow">Werte</p>
      <h2>Drei Dinge, an denen wir uns messen lassen.</h2>
    </header>
    <ul class="card-grid card-grid--3">
      <li class="card">
        <h3>Termintreue</h3>
        <p>Was wir zusagen, fahren wir – auch wenn die Disposition kurzfristig umsteuern muss.</p>
      </li>
      <li class="card">
        <h3>Stamm-Mannschaft</h3>
        <p>Unsere Fahrer sind keine Leiharbeiter. Sie kennen ihre Fahrzeuge, ihre Strecken und unsere Kunden.</p>
      </li>
      <li class="card">
        <h3>Eigene Flotte</h3>
        <p>Vom Tieflader bis zum Motorwagen mit Ladekran – wir haben das passende Gerät selbst auf dem Hof.</p>
      </li>
    </ul>
  </div>
</section>

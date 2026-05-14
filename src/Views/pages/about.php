<?php
/** @var array $page */
/** @var array<int,array> $events */
declare(strict_types=1);
?>
<section class="hero hero--inner" data-reveal>
  <div class="container">
    <p class="eyebrow">Lernen Sie uns kennen</p>
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
      <span class="stat__label">Generation in der Familie</span>
    </div>
    <div class="stat">
      <span class="stat__num industrial-num">6</span>
      <span class="stat__label">Länder, in denen wir fahren</span>
    </div>
    <div class="stat">
      <span class="stat__num industrial-num">seit 2018</span>
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
      <p class="eyebrow">Unsere Geschichte</p>
      <h2>Wie aus einem Lkw eine Spedition wurde.</h2>
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
      <p class="eyebrow">Was uns wichtig ist</p>
      <h2>Drei Versprechen, die wir halten.</h2>
    </header>
    <ul class="card-grid card-grid--3">
      <li class="card">
        <h3>Wir halten Termine</h3>
        <p>Was wir Ihnen zusagen, das fahren wir auch – pünktlich. Wird es kurzfristig eng, melden wir uns bei Ihnen.</p>
      </li>
      <li class="card">
        <h3>Feste Fahrer</h3>
        <p>Bei uns gibt es kein Leiharbeiter-Karussell. Unsere Fahrer kennen ihre Lkw, ihre Strecken und unsere Kunden.</p>
      </li>
      <li class="card">
        <h3>Eigener Fuhrpark</h3>
        <p>Vom Tautliner bis zum Tiefbett-Sattelanhänger: Das passende Fahrzeug für Ihren Transport steht auf unserem Hof.</p>
      </li>
    </ul>
  </div>
</section>

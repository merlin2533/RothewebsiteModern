<?php
/** @var array $page */
declare(strict_types=1);
?>
<section class="hero hero--inner" data-reveal>
  <div class="container">
    <p class="eyebrow"><?= e($page['title'] ?? '') ?></p>
    <h1><?= e($page['hero_headline'] ?? $page['title'] ?? '') ?></h1>
    <?php if (!empty($page['hero_sub'])): ?>
    <p class="hero__sub"><?= e($page['hero_sub']) ?></p>
    <?php endif; ?>
  </div>
</section>

<section class="section" data-reveal>
  <div class="container container--narrow prose">
    <?= $page['content_html'] ?? '' ?>
  </div>
</section>

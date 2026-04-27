<?php
/** @var array $page */
/** @var array<int,array> $media */
declare(strict_types=1);
?>
<section class="hero hero--inner" data-reveal>
  <div class="container">
    <p class="eyebrow">Galerie</p>
    <h1 class="hero__headline"><?= e($page['hero_headline']) ?></h1>
    <p class="hero__sub"><?= e($page['hero_sub']) ?></p>
  </div>
</section>

<section class="section gallery-section" data-reveal>
  <div class="container">
    <?php if (empty($media)): ?>
      <p class="muted">Noch keine Bilder in der Galerie.</p>
    <?php else: ?>
      <div class="gallery-grid" id="gallery-grid">
        <?php foreach ($media as $i => $m):
          $thumb = \App\Core\MediaResolver::variantUrl($m, 800, 'jpg');
          $thumbWebp = \App\Core\MediaResolver::variantUrl($m, 800, 'webp');
          $full  = \App\Core\MediaResolver::variantUrl($m, 1600, 'jpg');
          $alt   = (string) ($m['alt_text'] ?? '');
          $w = (int) ($m['width'] ?? 0);
          $h = (int) ($m['height'] ?? 0);
        ?>
          <button type="button" class="gallery-tile"
                  data-lightbox-trigger
                  data-full="<?= e((string) $full) ?>"
                  data-alt="<?= e($alt) ?>"
                  data-index="<?= $i ?>"
                  aria-label="Bild oeffnen: <?= e($alt) ?>">
            <picture>
              <?php if ($thumbWebp): ?>
                <source type="image/webp" srcset="<?= e((string) $thumbWebp) ?>">
              <?php endif; ?>
              <img src="<?= e((string) $thumb) ?>" alt="<?= e($alt) ?>"
                   loading="lazy" decoding="async"
                   <?= $w && $h ? 'width="' . $w . '" height="' . $h . '"' : '' ?>>
            </picture>
          </button>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</section>

<!-- Lightbox: minimal, keyboard-navigierbar, lazy beim Open -->
<div class="lightbox" id="lightbox" hidden role="dialog" aria-modal="true" aria-label="Bild-Vollbildansicht">
  <button type="button" class="lightbox__btn lightbox__btn--close" data-lightbox-close aria-label="Schliessen">×</button>
  <button type="button" class="lightbox__btn lightbox__btn--prev"  data-lightbox-prev  aria-label="Voriges Bild">‹</button>
  <button type="button" class="lightbox__btn lightbox__btn--next"  data-lightbox-next  aria-label="Naechstes Bild">›</button>
  <figure class="lightbox__figure">
    <img class="lightbox__img" alt="" id="lightbox-img">
    <figcaption class="lightbox__caption" id="lightbox-caption"></figcaption>
  </figure>
  <div class="lightbox__counter" id="lightbox-counter"></div>
</div>

<script>
(function () {
  var triggers = document.querySelectorAll('[data-lightbox-trigger]');
  if (!triggers.length) return;

  var lb       = document.getElementById('lightbox');
  var img      = document.getElementById('lightbox-img');
  var caption  = document.getElementById('lightbox-caption');
  var counter  = document.getElementById('lightbox-counter');
  var current  = 0;

  function show(idx) {
    if (idx < 0) idx = triggers.length - 1;
    if (idx >= triggers.length) idx = 0;
    current = idx;
    var t = triggers[idx];
    img.src = t.dataset.full;
    img.alt = t.dataset.alt || '';
    caption.textContent = t.dataset.alt || '';
    counter.textContent = (idx + 1) + ' / ' + triggers.length;
    lb.hidden = false;
    document.body.style.overflow = 'hidden';
  }
  function close() {
    lb.hidden = true;
    img.src = '';
    document.body.style.overflow = '';
  }

  triggers.forEach(function (t, i) {
    t.addEventListener('click', function () { show(i); });
  });
  lb.addEventListener('click', function (e) {
    if (e.target === lb || e.target.closest('[data-lightbox-close]')) close();
    if (e.target.closest('[data-lightbox-prev]')) show(current - 1);
    if (e.target.closest('[data-lightbox-next]')) show(current + 1);
  });
  document.addEventListener('keydown', function (e) {
    if (lb.hidden) return;
    if (e.key === 'Escape')    close();
    if (e.key === 'ArrowLeft') show(current - 1);
    if (e.key === 'ArrowRight')show(current + 1);
  });
})();
</script>

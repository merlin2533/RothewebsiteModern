<?php
/** @var array $lp */
/** @var array<int,array> $relatedVehicles */
/** @var array<int,array> $relatedServices */
declare(strict_types=1);

$phoneE164  = trim((string) ($lp['primary_phone'] ?? '')) ?: setting('phone_e164', '+497127182310');
$phoneShown = trim((string) ($lp['primary_phone'] ?? '')) ?: setting('phone', '07127 18231');
$email      = trim((string) ($lp['primary_email'] ?? '')) ?: setting('email', 'info@rothe-transporte.de');
$ctaLabel   = (string) ($lp['cta_label'] ?? 'Anfrage senden');

// Subject for mailto with the LP context
$subject    = 'Anfrage: ' . $lp['title'];
?>
<section class="hero hero--inner" data-reveal>
    <div class="container">
        <p class="eyebrow"><?= e((string) ($lp['eyebrow'] ?? 'LANDINGPAGE')) ?></p>
        <h1 class="hero__headline"><?= e((string) ($lp['headline'] ?? $lp['title'])) ?></h1>
        <?php if (!empty($lp['sub'])): ?>
            <p class="hero__sub"><?= e((string) $lp['sub']) ?></p>
        <?php endif; ?>
        <div class="hero__cta">
            <a class="btn btn--primary" href="mailto:<?= e($email) ?>?subject=<?= rawurlencode($subject) ?>"
               data-source="lp:<?= e((string) $lp['slug']) ?>">
                <?= e($ctaLabel) ?>
                <svg width="16" height="16" aria-hidden="true"><use href="#icon-arrow-right"/></svg>
            </a>
            <a class="btn btn--ghost" href="tel:<?= e($phoneE164) ?>"
               data-source="lp:<?= e((string) $lp['slug']) ?>"><?= e($phoneShown) ?></a>
        </div>
    </div>
</section>

<?php if (!empty($lp['intro_html'])): ?>
    <section class="section section--surface" data-reveal>
        <div class="container container--narrow prose">
            <?= $lp['intro_html'] ?>
        </div>
    </section>
<?php endif; ?>

<?php if ($relatedServices): ?>
    <section class="section services-grid" data-reveal>
        <div class="container">
            <header class="section__head">
                <p class="eyebrow">Passende Leistungen</p>
                <h2>Was Sie konkret bekommen</h2>
            </header>
            <div class="card-grid card-grid--3">
                <?php foreach ($relatedServices as $s): ?>
                    <article class="card card--service">
                        <h3 class="card__title"><?= e($s['title']) ?></h3>
                        <p><?= e((string) ($s['summary'] ?? '')) ?></p>
                        <a class="card__cta" href="/leistungen/<?= e($s['slug']) ?>">
                            Details
                            <svg width="16" height="16" aria-hidden="true"><use href="#icon-arrow-right"/></svg>
                        </a>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif; ?>

<?php if ($relatedVehicles): ?>
    <section class="section section--surface fleet-highlights" data-reveal>
        <div class="container">
            <header class="section__head">
                <p class="eyebrow">Unser Fuhrpark</p>
                <h2>Diese Fahrzeuge setzen wir ein</h2>
            </header>
            <div class="card-grid card-grid--3">
                <?php foreach ($relatedVehicles as $v): ?>
                    <article class="card card--vehicle-mini">
                        <div class="card__media">
                            <img src="/assets/images/placeholders/vehicle-<?= e(preg_replace('/[^a-z]+/', '', (string) $v['slug']) ?: 'tieflader') ?>.svg"
                                 alt="<?= e((string) $v['name']) ?>" loading="lazy">
                        </div>
                        <h3 class="card__title"><?= e((string) $v['name']) ?></h3>
                        <dl class="spec-list">
                            <?php if (!empty($v['length_m'])): ?>
                                <div><dt>L × B × H</dt><dd><?= e(format_meters((float) $v['length_m'])) ?> × <?= e(format_meters((float) $v['width_m'])) ?> × <?= e(format_meters((float) $v['height_m'])) ?></dd></div>
                            <?php endif; ?>
                            <?php if (!empty($v['payload_kg'])): ?>
                                <div><dt>Nutzlast</dt><dd><?= e(format_kg((int) $v['payload_kg'])) ?></dd></div>
                            <?php endif; ?>
                            <?php if (!empty($v['euro_pallets'])): ?>
                                <div><dt>Euro-Paletten</dt><dd><?= (int) $v['euro_pallets'] ?></dd></div>
                            <?php endif; ?>
                        </dl>
                        <a class="card__cta" href="/fahrzeuge/<?= e((string) $v['slug']) ?>">
                            Details
                            <svg width="16" height="16" aria-hidden="true"><use href="#icon-arrow-right"/></svg>
                        </a>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif; ?>

<?php if (!empty($lp['body_html'])): ?>
    <section class="section about-content" data-reveal>
        <div class="container container--narrow prose">
            <?= $lp['body_html'] ?>
        </div>
    </section>
<?php endif; ?>

<section class="section section--cta cta-final" data-reveal>
    <div class="container cta-final__inner">
        <p class="eyebrow">Direkt zur Disposition</p>
        <p class="cta-final__phone industrial-num industrial-num--outline">
            <a href="tel:<?= e($phoneE164) ?>" data-source="lp:<?= e((string) $lp['slug']) ?>"><?= e($phoneShown) ?></a>
        </p>
        <p class="cta-final__sub">
            Mo–Fr 07:00–18:00 · oder per E-Mail an
            <a href="mailto:<?= e($email) ?>?subject=<?= rawurlencode($subject) ?>"
               data-source="lp:<?= e((string) $lp['slug']) ?>"><?= e($email) ?></a>
        </p>
    </div>
</section>

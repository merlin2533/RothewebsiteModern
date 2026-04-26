<?php
/** @var array $page */
/** @var array $service */
declare(strict_types=1);

$s = $service;
$phoneE164 = setting('phone_e164', '+497127182310');
$email = setting('email', 'info@rothe-transporte.de');

$structured_data = [[
    '@context' => 'https://schema.org',
    '@type' => 'Service',
    'name' => $s['title'],
    'description' => strip_tags((string) ($s['summary'] ?? '')),
    'provider' => [
        '@type' => 'MovingCompany',
        'name' => setting('company_name', 'Rothe-Transporte'),
        'url' => url('/'),
    ],
]];
?>
<section class="hero hero--inner" data-reveal>
  <div class="container">
    <nav aria-label="Breadcrumb" class="breadcrumb">
      <a href="/">Startseite</a> · <a href="/leistungen">Leistungen</a> · <span aria-current="page"><?= e($s['title']) ?></span>
    </nav>
    <p class="eyebrow">Leistung</p>
    <h1><?= e($s['title']) ?></h1>
    <p class="hero__sub"><?= e($s['summary'] ?? '') ?></p>
  </div>
</section>

<section class="section" data-reveal>
  <div class="container container--narrow prose">
    <?= $s['body_html'] ?? '' ?>
    <p>
      <a class="btn btn--primary" href="mailto:<?= e($email) ?>?subject=<?= rawurlencode('Anfrage: ' . $s['title']) ?>">Anfrage senden</a>
      <a class="btn btn--ghost" href="tel:<?= e($phoneE164) ?>">Anrufen</a>
    </p>
  </div>
</section>

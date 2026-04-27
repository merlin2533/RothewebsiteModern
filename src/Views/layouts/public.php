<?php
/** @var string $content */
/** @var array|null $page */
/** @var string|null $currentSlug */
declare(strict_types=1);

$cur = $currentSlug ?? '';
$phoneE164 = setting('phone_e164', '+497127182310');
$phoneDisplay = setting('phone', '07127 18231');
$email = setting('email', 'info@rothe-transporte.de');
$company = setting('company_name', 'Rothe-Transporte und Speditions GbR');
?>
<!doctype html>
<html lang="de">
<head>
<?php echo \App\Core\View::partial('meta', ['page' => $page ?? null, 'structured_data' => $structured_data ?? null]); ?>
<?php echo \App\Core\View::partial('tracking_head'); ?>
<script src="<?= asset('js/main.js') ?>" defer></script>
</head>
<body>
<?php echo \App\Core\View::partial('tracking_body'); ?>
<?php echo \App\Core\View::partial('icons'); ?>

<a class="skip-link" href="#main">Zum Inhalt springen</a>

<div class="marquee" aria-hidden="true">
  <div class="marquee__track">
    <span>Maschinentransporte &nbsp;·&nbsp; </span>
    <span>Schwerlast bis <strong>28 Tonnen</strong> &nbsp;·&nbsp; </span>
    <span>Sondertransporte &nbsp;·&nbsp; </span>
    <span>Eigene Lagerhallen &nbsp;·&nbsp; </span>
    <span>Direktfahrten &nbsp;·&nbsp; </span>
    <span>Kranarbeiten &amp; Montage &nbsp;·&nbsp; </span>
    <span>Touren: <strong>DE · AT · CH · FR · NL · BE</strong> &nbsp;·&nbsp; </span>
    <span>Seit <strong><?= e(setting('founded_year', '1978')) ?></strong> &nbsp;·&nbsp; </span>
    <span>2. Generation &nbsp;·&nbsp; </span>
    <span>Maschinentransporte &nbsp;·&nbsp; </span>
    <span>Schwerlast bis <strong>28 Tonnen</strong> &nbsp;·&nbsp; </span>
    <span>Sondertransporte &nbsp;·&nbsp; </span>
    <span>Eigene Lagerhallen &nbsp;·&nbsp; </span>
    <span>Direktfahrten &nbsp;·&nbsp; </span>
    <span>Kranarbeiten &amp; Montage &nbsp;·&nbsp; </span>
    <span>Touren: <strong>DE · AT · CH · FR · NL · BE</strong> &nbsp;·&nbsp; </span>
    <span>Seit <strong><?= e(setting('founded_year', '1978')) ?></strong> &nbsp;·&nbsp; </span>
    <span>2. Generation &nbsp;·&nbsp; </span>
  </div>
</div>

<div class="topbar">
  <div class="container topbar__inner">
    <span class="topbar__label">Mo – Fr &nbsp;07:00 – 17:00 Uhr &nbsp;·&nbsp; Notfallnummer 24/7</span>
    <div class="topbar__contact">
      <a class="topbar__link" href="tel:<?= e($phoneE164) ?>">
        <svg width="16" height="16" aria-hidden="true"><use href="#icon-phone"/></svg>
        <span><?= e($phoneDisplay) ?></span>
      </a>
      <a class="topbar__link" href="mailto:<?= e($email) ?>">
        <svg width="16" height="16" aria-hidden="true"><use href="#icon-mail"/></svg>
        <span><?= e($email) ?></span>
      </a>
    </div>
  </div>
</div>

<header class="site-header" data-header>
  <div class="container site-header__inner">
    <a class="brand" href="/" aria-label="Startseite – <?= e($company) ?>">
      <img src="/uploads/cropped-cropped-cropped-Logo_Vector.png" alt="Rothe Transporte · Spedition" class="brand__logo">
    </a>

    <button class="nav-toggle" type="button" data-nav-toggle aria-expanded="false" aria-controls="primary-nav" aria-label="Menü öffnen">
      <svg class="nav-toggle__icon nav-toggle__icon--menu" width="24" height="24" aria-hidden="true"><use href="#icon-menu"/></svg>
      <svg class="nav-toggle__icon nav-toggle__icon--close" width="24" height="24" aria-hidden="true"><use href="#icon-close"/></svg>
    </button>

    <nav id="primary-nav" class="primary-nav" aria-label="Hauptnavigation">
      <a href="/"<?= active_nav('home', $cur) ?>>Startseite</a>
      <a href="/ueber-uns"<?= active_nav('ueber-uns', $cur) ?>>Über uns</a>
      <a href="/leistungen"<?= active_nav('leistungen', $cur) ?>>Leistungen</a>
      <a href="/fahrzeuge"<?= active_nav('fahrzeuge', $cur) ?>>Fahrzeuge</a>
      <a href="/galerie"<?= active_nav('galerie', $cur) ?>>Galerie</a>
      <a href="/karriere"<?= active_nav('karriere', $cur) ?>>Karriere</a>
      <a href="/kontakt"<?= active_nav('kontakt', $cur) ?>>Kontakt</a>
      <a class="btn btn--primary primary-nav__cta" href="mailto:<?= e($email) ?>?subject=<?= rawurlencode('Transportanfrage') ?>">
        Anfrage senden
        <svg width="16" height="16" aria-hidden="true"><use href="#icon-arrow-right"/></svg>
      </a>
    </nav>
  </div>
</header>

<main id="main">
<?= $content ?>
</main>

<?php echo \App\Core\View::partial('consent_banner'); ?>

<footer class="site-footer">
  <div class="container site-footer__grid">
    <div>
      <p class="site-footer__brand">ROTHE</p>
      <p class="site-footer__sub">Transporte · Spedition</p>
      <p class="site-footer__addr">
        <?= e(setting('address_street', '')) ?><br>
        <?= e(setting('address_zip', '')) ?> <?= e(setting('address_city', '')) ?><br>
        <?= e(setting('address_country', 'Deutschland')) ?>
      </p>
    </div>
    <div>
      <h2 class="site-footer__h">Schnellzugriff</h2>
      <ul>
        <li><a href="/leistungen">Leistungen</a></li>
        <li><a href="/fahrzeuge">Fahrzeuge</a></li>
        <li><a href="/galerie">Galerie</a></li>
        <li><a href="/ueber-uns">Über uns</a></li>
        <li><a href="/karriere">Karriere</a></li>
      </ul>
    </div>
    <div>
      <h2 class="site-footer__h">Kontakt</h2>
      <ul>
        <li><a href="tel:<?= e($phoneE164) ?>"><?= e($phoneDisplay) ?></a></li>
        <li><a href="mailto:<?= e($email) ?>"><?= e($email) ?></a></li>
        <li>Mo–Fr 07:00–17:00</li>
      </ul>
    </div>
    <div>
      <h2 class="site-footer__h">Rechtliches</h2>
      <ul>
        <li><a href="/impressum">Impressum</a></li>
        <li><a href="/datenschutz">Datenschutz</a></li>
        <li><a href="/sitemap.xml">Sitemap</a></li>
      </ul>
    </div>
  </div>
  <div class="site-footer__bottom">
    <div class="container">
      <small>© <?= date('Y') ?> <?= e($company) ?> · Karl-Otto Rothe & Christopher Rothe</small>
    </div>
  </div>
</footer>
</body>
</html>

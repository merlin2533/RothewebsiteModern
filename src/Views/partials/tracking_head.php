<?php
declare(strict_types=1);
$gtm  = trim((string) setting('gtm_container_id', ''));
$plDomain = trim((string) setting('plausible_domain', ''));
$plScript = trim((string) setting('plausible_script_url', ''));
$matomoUrl    = trim((string) setting('matomo_url', ''));
$matomoSiteId = trim((string) setting('matomo_site_id', ''));

// Kategorisiertes Consent: rt_consent_v2 ist eine kommagetrennte Liste der
// erlaubten Kategorien (z.B. "necessary,statistics" oder "necessary,statistics,marketing").
// "necessary" ist immer impliziert; "statistics" und "marketing" sind opt-in.
$consentRaw = (string) ($_COOKIE['rt_consent_v2'] ?? '');
$consentCats = $consentRaw === '' ? [] : array_map('trim', explode(',', $consentRaw));
$hasStats     = in_array('statistics', $consentCats, true);
$hasMarketing = in_array('marketing', $consentCats, true);

// Plausible/Matomo zaehlen als Statistik. GTM kann beides laden → braucht Marketing.
$gtmActive    = $gtm !== '' && preg_match('/^GTM-[A-Z0-9]+$/', $gtm) && $hasMarketing;
$plActive     = $plDomain !== '' && $plScript !== '' && $hasStats;
$matomoActive = $matomoUrl !== '' && $matomoSiteId !== '' && $hasStats;
$anyActive    = $gtmActive || $plActive || $matomoActive;
?>
<?php if ($anyActive): ?>
<script>
  // vendor-neutral dataLayer (any tag manager / analytics may consume)
  window.dataLayer = window.dataLayer || [];
  window.dataLayer.push({ event: 'page_view' });
</script>
<?php endif; ?>

<?php if ($gtmActive): ?>
<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','<?= e($gtm) ?>');</script>
<!-- End Google Tag Manager -->
<?php endif; ?>

<?php if ($plActive): ?>
<script defer data-domain="<?= e($plDomain) ?>" src="<?= e($plScript) ?>"></script>
<?php endif; ?>

<?php if ($matomoActive): ?>
<script>
  var _paq = window._paq = window._paq || [];
  _paq.push(['trackPageView']);
  _paq.push(['enableLinkTracking']);
  (function() {
    var u = "<?= e(rtrim($matomoUrl, '/')) ?>/";
    _paq.push(['setTrackerUrl', u + 'matomo.php']);
    _paq.push(['setSiteId', '<?= e($matomoSiteId) ?>']);
    var d = document, g = d.createElement('script'), s = d.getElementsByTagName('script')[0];
    g.async = true; g.src = u + 'matomo.js'; s.parentNode.insertBefore(g, s);
  })();
</script>
<?php endif; ?>

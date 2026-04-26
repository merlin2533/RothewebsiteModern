<?php
declare(strict_types=1);
// Show banner only if any tracking is configured AND consent not yet decided.
$anyTracking = trim((string) setting('gtm_container_id', '')) !== ''
    || trim((string) setting('plausible_domain', '')) !== ''
    || trim((string) setting('matomo_url', '')) !== '';
$decided = isset($_COOKIE['rt_consent']);
if (!$anyTracking || $decided) {
    return;
}
?>
<aside class="consent-banner" id="consent-banner" role="dialog" aria-modal="false"
       aria-labelledby="consent-banner-title">
    <div class="consent-banner__inner">
        <div class="consent-banner__text">
            <p id="consent-banner-title" class="consent-banner__title">Wir respektieren Ihre Privatsphäre</p>
            <p class="consent-banner__sub">
                Wir verwenden optional anonyme Statistik- und Marketing-Tools, um unser Angebot zu verbessern.
                Sie können diese Auswahl jederzeit in der <a href="/datenschutz">Datenschutzerklärung</a> ändern.
            </p>
        </div>
        <div class="consent-banner__actions">
            <button type="button" class="btn btn--ghost" data-consent="denied">Ablehnen</button>
            <button type="button" class="btn btn--primary" data-consent="granted">Akzeptieren</button>
        </div>
    </div>
</aside>

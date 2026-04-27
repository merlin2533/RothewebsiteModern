<?php
declare(strict_types=1);
// Show banner only if any tracking is configured AND consent not yet decided.
$anyTracking = trim((string) setting('gtm_container_id', '')) !== ''
    || trim((string) setting('plausible_domain', '')) !== ''
    || trim((string) setting('matomo_url', '')) !== '';
$decided = isset($_COOKIE['rt_consent_v2']);
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
                Wir verwenden technisch notwendige Cookies, optional Statistik- und Marketing-Tools, um unser Angebot zu verbessern.
                Sie können die Auswahl in der <a href="/datenschutz">Datenschutzerklärung</a> jederzeit anpassen.
            </p>
        </div>

        <div class="consent-banner__categories" id="consent-cats">
            <label class="consent-cat consent-cat--locked" title="Immer aktiv – funktional notwendig">
                <input type="checkbox" checked disabled> Notwendig
            </label>
            <label class="consent-cat">
                <input type="checkbox" data-cat="statistics" checked> Statistik
            </label>
            <label class="consent-cat">
                <input type="checkbox" data-cat="marketing"> Marketing
            </label>
        </div>

        <div class="consent-banner__actions">
            <button type="button" class="btn btn--ghost"   data-consent="denied">Nur notwendig</button>
            <button type="button" class="btn btn--ghost"   data-consent="custom">Auswahl speichern</button>
            <button type="button" class="btn btn--primary" data-consent="all">Alle akzeptieren</button>
        </div>
    </div>
</aside>

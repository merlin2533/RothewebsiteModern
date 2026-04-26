<?php declare(strict_types=1); ?>
<?php
// Helper: get setting value
$s = static function (string $key) use ($settings): string {
    return (string) ($settings[$key] ?? '');
};
?>
<div class="page-header">
    <h1 class="page-header__title">Einstellungen</h1>
    <p class="page-header__sub">Zentrale Website-Daten – werden auf allen Seiten, im Footer und in Suchmaschinen-Snippets verwendet.</p>
</div>

<form method="post" action="/admin/settings">
    <?= \App\Core\Csrf::field() ?>

    <!-- Section: Firma -->
    <div class="form-section">
        <h2 class="form-section__title">Firma</h2>
        <div class="form-group">
            <label class="form-label" for="set-company">Firmenname</label>
            <input class="form-input" type="text" id="set-company" name="company_name" value="<?= e($s('company_name')) ?>">
        </div>
        <div class="form-group">
            <label class="form-label" for="set-owners">Inhaber (kommagetrennt)</label>
            <input class="form-input" type="text" id="set-owners" name="owners" value="<?= e($s('owners')) ?>">
        </div>
        <div class="form-group">
            <label class="form-label" for="set-founded">Gründungsjahr</label>
            <input class="form-input form-input--narrow" type="number" id="set-founded" name="founded_year" value="<?= e($s('founded_year')) ?>" min="1900" max="2100">
        </div>
    </div>

    <!-- Section: Adresse -->
    <div class="form-section">
        <h2 class="form-section__title">Adresse</h2>
        <div class="form-group">
            <label class="form-label" for="set-street">Straße &amp; Hausnummer</label>
            <input class="form-input" type="text" id="set-street" name="address_street" value="<?= e($s('address_street')) ?>">
        </div>
        <div class="input-row">
            <div class="form-group">
                <label class="form-label" for="set-zip">PLZ</label>
                <input class="form-input form-input--narrow" type="text" id="set-zip" name="address_zip" value="<?= e($s('address_zip')) ?>">
            </div>
            <div class="form-group">
                <label class="form-label" for="set-city">Ort</label>
                <input class="form-input" type="text" id="set-city" name="address_city" value="<?= e($s('address_city')) ?>">
            </div>
            <div class="form-group">
                <label class="form-label" for="set-country">Land</label>
                <input class="form-input form-input--narrow" type="text" id="set-country" name="address_country" value="<?= e($s('address_country')) ?>" placeholder="DE">
            </div>
        </div>
        <div class="input-row">
            <div class="form-group">
                <label class="form-label" for="set-lat">Breitengrad (geo_lat)</label>
                <input class="form-input" type="text" id="set-lat" name="geo_lat" value="<?= e($s('geo_lat')) ?>" placeholder="48.5512">
            </div>
            <div class="form-group">
                <label class="form-label" for="set-lng">Längengrad (geo_lng)</label>
                <input class="form-input" type="text" id="set-lng" name="geo_lng" value="<?= e($s('geo_lng')) ?>" placeholder="9.2033">
            </div>
        </div>
    </div>

    <!-- Section: Kontakt -->
    <div class="form-section">
        <h2 class="form-section__title">Kontakt</h2>
        <div class="input-row">
            <div class="form-group">
                <label class="form-label" for="set-phone">Telefon (Anzeige)</label>
                <input class="form-input" type="text" id="set-phone" name="phone" value="<?= e($s('phone')) ?>" placeholder="07127 18231">
            </div>
            <div class="form-group">
                <label class="form-label" for="set-phone-e164">Telefon (E.164 für tel:)</label>
                <input class="form-input" type="text" id="set-phone-e164" name="phone_e164" value="<?= e($s('phone_e164')) ?>" placeholder="+497127182310">
                <p class="form-hint">Format: +49... (ohne Leerzeichen)</p>
            </div>
        </div>
        <div class="form-group">
            <label class="form-label" for="set-email">E-Mail-Adresse</label>
            <input class="form-input" type="email" id="set-email" name="email" value="<?= e($s('email')) ?>">
        </div>
        <div class="form-group">
            <label class="form-label" for="set-hours">Öffnungszeiten (Anzeige)</label>
            <textarea class="form-input form-textarea" id="set-hours" name="opening_hours" rows="3"><?= e($s('opening_hours')) ?></textarea>
            <p class="form-hint">Freitext, z.B. „Mo–Fr 7:00–18:00 Uhr"</p>
        </div>
        <div class="form-group">
            <label class="form-label" for="set-hours-schema">Öffnungszeiten (Schema.org)</label>
            <input class="form-input" type="text" id="set-hours-schema" name="opening_hours_schema" value="<?= e($s('opening_hours_schema')) ?>" placeholder="Mo-Fr 07:00-18:00">
            <p class="form-hint">Für JSON-LD, Format: „Mo-Fr 07:00-18:00"</p>
        </div>
        <div class="form-group">
            <label class="form-label" for="set-areas">Bediente Länder (areas_served)</label>
            <input class="form-input" type="text" id="set-areas" name="areas_served" value="<?= e($s('areas_served')) ?>" placeholder="DE, AT, CH, FR, NL, BE">
            <p class="form-hint">Kommagetrennte ISO-Ländercodes für JSON-LD</p>
        </div>
    </div>

    <!-- Section: SEO Defaults -->
    <div class="form-section">
        <h2 class="form-section__title">SEO-Defaults</h2>
        <div class="form-group">
            <label class="form-label" for="set-meta-title">
                Standard-Meta-Titel
                <small class="counter-hint" data-counter-target="meta_default_title" data-min="50" data-max="60"></small>
            </label>
            <input
                class="form-input"
                type="text"
                id="set-meta-title"
                name="meta_default_title"
                value="<?= e($s('meta_default_title')) ?>"
                maxlength="120"
            >
            <p class="form-hint">Wird verwendet, wenn eine Seite keinen eigenen Meta-Titel hat. Ideal: 50–60 Zeichen.</p>
        </div>
        <div class="form-group">
            <label class="form-label" for="set-meta-desc">
                Standard-Meta-Beschreibung
                <small class="counter-hint" data-counter-target="meta_default_description" data-min="140" data-max="160"></small>
            </label>
            <textarea
                class="form-input form-textarea"
                id="set-meta-desc"
                name="meta_default_description"
                rows="3"
                maxlength="300"
            ><?= e($s('meta_default_description')) ?></textarea>
            <p class="form-hint">Ideal: 140–160 Zeichen.</p>
        </div>
        <div class="input-row">
            <div class="form-group">
                <label class="form-label" for="set-og-image">Standard-OG-Bild</label>
                <select class="form-input form-select" id="set-og-image" name="og_default_image_id">
                    <option value="">— kein Bild —</option>
                    <?php foreach ($media as $m): ?>
                        <option value="<?= (int) $m['id'] ?>" <?= $s('og_default_image_id') === (string) $m['id'] ? 'selected' : '' ?>>
                            <?= e($m['original_name'] ?? $m['filename']) ?><?= !empty($m['alt_text']) ? ' – ' . e($m['alt_text']) : '' ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <p class="form-hint">Fallback-Bild für Social-Media-Sharing (min. 1200×630 px).</p>
            </div>
            <div class="form-group">
                <label class="form-label" for="set-hero-image">Standard-Hero-Bild</label>
                <select class="form-input form-select" id="set-hero-image" name="hero_image_id">
                    <option value="">— kein Bild —</option>
                    <?php foreach ($media as $m): ?>
                        <option value="<?= (int) $m['id'] ?>" <?= $s('hero_image_id') === (string) $m['id'] ? 'selected' : '' ?>>
                            <?= e($m['original_name'] ?? $m['filename']) ?><?= !empty($m['alt_text']) ? ' – ' . e($m['alt_text']) : '' ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </div>

    <!-- Section: Inhaber-Zitat -->
    <div class="form-section">
        <h2 class="form-section__title">Inhaber-Zitat</h2>
        <div class="form-group">
            <label class="form-label" for="set-quote">Zitat</label>
            <textarea class="form-input form-textarea" id="set-quote" name="owner_quote" rows="4"><?= e($s('owner_quote')) ?></textarea>
        </div>
        <div class="form-group">
            <label class="form-label" for="set-quote-attr">Zuschreibung (Name, Funktion)</label>
            <input class="form-input" type="text" id="set-quote-attr" name="owner_quote_attribution" value="<?= e($s('owner_quote_attribution')) ?>" placeholder="Karl-Otto Rothe, Inhaber">
        </div>
    </div>

    <!-- Section: Suche & Brand-SERP -->
    <div class="form-section">
        <h2 class="form-section__title">Brand-Suche</h2>
        <p class="form-section__hint">URL-Template für die WebSite-SearchAction (wird in Brand-Sitelinks verwendet, optional). Beispiel: <code>https://www.google.com/search?q=site%3Arothe-transporte.de+{search_term_string}</code></p>
        <div class="form-group">
            <label class="form-label" for="set-search">Search-Action Target</label>
            <input class="form-input" type="text" id="set-search" name="search_action_target" value="<?= e($s('search_action_target')) ?>">
        </div>
        <div class="form-group">
            <label class="form-label" for="set-twitter">X / Twitter Handle (mit @)</label>
            <input class="form-input" type="text" id="set-twitter" name="twitter_handle" value="<?= e($s('twitter_handle')) ?>" placeholder="@firma">
        </div>
        <div class="form-group">
            <label class="form-label" for="set-vat">USt-IdNr. (Impressum)</label>
            <input class="form-input" type="text" id="set-vat" name="vat_id" value="<?= e($s('vat_id')) ?>" placeholder="DE000000000">
        </div>
        <div class="form-group">
            <label class="form-label" for="set-ceo">Geschäftsführer (für JSON-LD/Impressum)</label>
            <input class="form-input" type="text" id="set-ceo" name="ceo_name" value="<?= e($s('ceo_name')) ?>">
        </div>
    </div>

    <!-- Section: Conversion-Tracking -->
    <div class="form-section">
        <h2 class="form-section__title">Conversion-Tracking</h2>
        <p class="form-section__hint">Alle Felder leer lassen = kein Tracking. Vor dem Aktivieren in der Datenschutzerklärung dokumentieren und ggf. Cookie-/Consent-Banner ergänzen.</p>

        <div class="form-group">
            <label class="form-label" for="set-gtm">Google Tag Manager Container-ID</label>
            <input class="form-input" type="text" id="set-gtm" name="gtm_container_id" value="<?= e($s('gtm_container_id')) ?>" placeholder="GTM-XXXXXX">
        </div>

        <div class="input-row">
            <div class="form-group">
                <label class="form-label" for="set-pl-domain">Plausible Domain</label>
                <input class="form-input" type="text" id="set-pl-domain" name="plausible_domain" value="<?= e($s('plausible_domain')) ?>" placeholder="rothe-transporte.de">
            </div>
            <div class="form-group">
                <label class="form-label" for="set-pl-url">Plausible Script-URL</label>
                <input class="form-input" type="text" id="set-pl-url" name="plausible_script_url" value="<?= e($s('plausible_script_url')) ?>" placeholder="https://plausible.io/js/script.js">
            </div>
        </div>

        <div class="input-row">
            <div class="form-group">
                <label class="form-label" for="set-matomo-url">Matomo URL</label>
                <input class="form-input" type="text" id="set-matomo-url" name="matomo_url" value="<?= e($s('matomo_url')) ?>" placeholder="https://stats.example.com/">
            </div>
            <div class="form-group">
                <label class="form-label" for="set-matomo-id">Matomo Site-ID</label>
                <input class="form-input form-input--narrow" type="text" id="set-matomo-id" name="matomo_site_id" value="<?= e($s('matomo_site_id')) ?>" placeholder="1">
            </div>
        </div>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn-primary">Einstellungen speichern</button>
    </div>
</form>

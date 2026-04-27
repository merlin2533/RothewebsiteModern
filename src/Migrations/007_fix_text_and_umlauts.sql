-- =============================================================================
-- 007_fix_text_and_umlauts.sql
-- Fixes broken umlaut encoding (ae/oe/ue written as ASCII) across FAQs,
-- timeline, settings and pages. Also improves a few texts to match the
-- original Rothe-Transporte tone more closely.
-- =============================================================================

-- ── Settings ──────────────────────────────────────────────────────────────────

UPDATE settings SET
  value = 'Wir bewegen Maschinen seit 1978. Mit eigenem Fuhrpark, festen Fahrern und einem Anruf zur Disposition. Daran haben wir nichts geändert.'
WHERE key = 'owner_quote';

UPDATE settings SET
  value = 'Familienspedition seit 1978 in Walddorfhäslach. Maschinentransporte, Tieflader bis 28 t, Tautliner, Lagerung. Touren in DE, AT, CH, FR, NL, BE. Telefon: 07127 18231.'
WHERE key = 'meta_default_description';

-- ── Pages ─────────────────────────────────────────────────────────────────────

-- "47 Jahre" becomes stale and was wrong (1978→2026 = 48 Jahre). Use a
-- timeless description matching the old site's tone instead.
UPDATE pages SET
  hero_headline = 'Familienspedition seit 1978.',
  hero_sub      = '1978 als Ein-Mann-Betrieb in Münsingen gegründet. Heute: eigener Fuhrpark, feste Fahrer, zwei Generationen Erfahrung.',
  content_html  = '<h2>Unsere Geschichte</h2><p>Karl-Otto Rothe gründete das Unternehmen 1978 in Münsingen. 1985 zog der Betrieb nach Walddorfhäslach um. Was als Ein-Mann-Betrieb begann, ist heute eine spezialisierte Spedition für Maschinen- und Spezialtransporte. Seit 2010 führt Christopher Rothe das Unternehmen in zweiter Generation mit.</p><h2>Was uns auszeichnet</h2><p>Wir liefern, was wir zusagen – punktgenau, sorgfältig verzurrt, mit denselben Fahrern, die unsere Kunden seit Jahren kennen. Seit 2018 sind wir anerkannter Ausbildungsbetrieb für Berufskraftfahrer.</p>'
WHERE slug = 'ueber-uns';

UPDATE pages SET
  hero_headline = 'Maschinentransporte. Seit 1978.',
  hero_sub      = 'Tieflader, Tautliner, Motorwagen mit Ladekran – alles aus eigener Hand, mit eigenem Fuhrpark und festen Fahrern.',
  content_html  = '<p>Wir transportieren Maschinen, Anlagen und Industriegüter seit 1978 – mit eigenem Fuhrpark, festen Fahrern und direkter Disposition. Kein Callcenter, kein Vermittler.</p><h2>Unser Anspruch</h2><p>Termin halten. Maschine sicher abliefern. Preis am Telefon nennen. Das ist alles, worauf es ankommt.</p>'
WHERE slug = 'home';

UPDATE pages SET
  hero_headline = 'Vier Leistungen. Ein Ansprechpartner.',
  content_html  = '<p>Maschinentransport, Spezialtransport, Lagerung, Stapler- und Entladedienst – alles aus einer Hand, mit eigenem Personal und eigener Technik. Kein Subunternehmer-Geflecht.</p>'
WHERE slug = 'leistungen';

UPDATE pages SET
  hero_headline = 'Fahrer gesucht. Mensch gemeint.',
  hero_sub      = 'Wir suchen Berufskraftfahrer (Klasse CE) für Maschinen- und Spezialtransporte in DE, AT, CH, FR, NL und BE.',
  content_html  = '<h2>Was wir bieten</h2><ul><li>Eigener, moderner Fuhrpark – jeder Fahrer hat seinen festen Lkw.</li><li>Faire, pünktliche Bezahlung.</li><li>Familienbetrieb mit kurzen Entscheidungswegen.</li><li>Sicherer Arbeitsplatz mit Perspektive.</li><li>Wertschätzung – wir setzen auf Stamm-Mannschaft, kein Karussell.</li></ul><h2>Bewerbung</h2><p>Per E-Mail an <a href="mailto:info@rothe-transporte.de">info@rothe-transporte.de</a> oder per Post an: Rothe-Transporte und Speditions GbR, Aeckerlesweg 3, 72141 Walddorfhäslach.</p>'
WHERE slug = 'karriere';

-- ── FAQs – fix broken umlauts ─────────────────────────────────────────────────

UPDATE faqs SET
  answer_html = '<p>Vier Stück. <strong>Tieflader-Sattelauflieger</strong> (28 t Nutzlast, 13,60 × 2,48 × 3,70 m, 33 Europaletten). <strong>Tautliner-Sattelzug</strong> (25 t, 13,60 × 2,48 × 2,70 m, 33 Europaletten). <strong>Anhänger</strong> mit integrierter Auffahrrampe und Lenkachse. <strong>Motorwagen mit Ladekran</strong> und Lenkachse für enge Baustellen.</p>'
WHERE question LIKE 'Welche Fahrzeuge%';

UPDATE faqs SET
  question    = 'In welche Länder fahren Sie regelmäßig?',
  answer_html = '<p>Deutschland, Österreich, die Schweiz, Frankreich, die Niederlande, Belgien. Tagesfahrten innerhalb Süddeutschlands sind Standard.</p>'
WHERE question LIKE 'In welche L%';

UPDATE faqs SET
  answer_html = '<p>Das hängt von Strecke, Gewicht, Maßen und Fahrzeug ab. Rufen Sie uns mit Abhol- und Zielort, Maßen und Gewicht der Maschine unter <a href="tel:+497127182310">07127 18231</a> an – Sie bekommen einen verbindlichen Festpreis am Telefon.</p>'
WHERE question LIKE 'Was kostet%';

UPDATE faqs SET
  answer_html = '<p>Ja. Großteile, Regalware und palettierte Industriegüter in eigenen Hallen. Be- und Entladung übernehmen wir mit eigenen Staplern bis 3,5 t.</p>'
WHERE question LIKE 'Bieten Sie auch Lagerung%';

UPDATE faqs SET
  answer_html = '<p>Ja. Karl-Otto Rothe gründete 1978 in Münsingen. Seit 2010 führt Christopher Rothe das Unternehmen in zweiter Generation mit. Sitz seit 1985: Walddorfhäslach im Landkreis Reutlingen.</p>'
WHERE question LIKE 'Sind Sie ein Familienunternehmen%';

UPDATE faqs SET
  answer_html = '<p>Telefon ist am schnellsten: <a href="tel:+497127182310">07127 18231</a>. Wir brauchen: Abhol- und Zielort, Wunschtermin, Maße und Gewicht der Sendung. Alternativ E-Mail an <a href="mailto:info@rothe-transporte.de">info@rothe-transporte.de</a>.</p>'
WHERE question LIKE 'Wie buche ich%';

-- ── Timeline – fix broken umlauts ────────────────────────────────────────────

UPDATE timeline_events SET
  description = 'Karl-Otto Rothe gründet das Transportunternehmen als Ein-Mann-Betrieb in Münsingen.'
WHERE year = 1978;

UPDATE timeline_events SET
  title       = 'Umzug nach Walddorfhäslach',
  description = 'Verlagerung des Standorts nach Walddorfhäslach, Landkreis Reutlingen.'
WHERE year = 1985;

UPDATE timeline_events SET
  description = 'Anschaffung weiterer Fahrzeuge zur Bewältigung steigender Nachfrage.'
WHERE year = 1991;

UPDATE timeline_events SET
  description = 'Ausrichtung auf Maschinen- und Anlagentransporte als Kerngeschäft.'
WHERE year = 2005;

UPDATE timeline_events SET
  description = 'Christopher Rothe steigt ein – die zweite Generation übernimmt Verantwortung.'
WHERE year = 2010;

UPDATE timeline_events SET
  description = 'Erweiterung der Flotte um Tieflader-Sattelauflieger und Spezialanhänger.'
WHERE year = 2015;

UPDATE timeline_events SET
  description = 'Anerkennung als Ausbildungsbetrieb für Berufskraftfahrer.'
WHERE year = 2018;

-- =============================================================================
-- 006_modern_copy_and_gallery.sql
-- Modernisierte, knappere Texte fuer Pages, Vehicles, Services, FAQs +
-- Settings-Key fuer "Galerie aktiv".
-- Stilrichtung: konkret > abstrakt, Zahl > Adjektiv, Verb > Nomen,
-- weniger Floskeln, mehr Substanz.
-- =============================================================================

-- ── PAGES ──────────────────────────────────────────────────────────────────

UPDATE pages SET
  meta_title       = '28 Tonnen, 13,60 m. Maschinen-Spedition Walddorfhäslach',
  meta_description = 'Familienspedition seit 1978. Tieflader, Tautliner, Motorwagen mit Ladekran. Festpreis am Telefon: 07127 18231.',
  hero_headline    = 'Schweres bewegen. Pünktlich.',
  hero_sub         = '13,60 Meter Tieflader. 28 Tonnen Nutzlast. Tagestour in sechs Länder. Disposition direkt aus Walddorfhäslach.',
  content_html     = '<p>Wir transportieren Maschinen seit 1978. Mit eigenem Fuhrpark, festen Fahrern und einem Anruf zur Disposition – kein Callcenter, kein Vermittler.</p><h2>Unser Anspruch</h2><p>Termin halten. Maschine sicher abliefern. Festpreis am Telefon nennen. Das ist alles, worauf es ankommt.</p>'
WHERE slug = 'home';

UPDATE pages SET
  meta_title       = 'Über uns – seit 1978 in Familienhand | Rothe-Transporte',
  meta_description = 'Karl-Otto und Christopher Rothe: zwei Generationen Maschinen-Spedition aus Walddorfhäslach. Eigener Fuhrpark, Stamm-Fahrer, Ausbildungsbetrieb.',
  hero_headline    = '47 Jahre. Zwei Generationen.',
  hero_sub         = '1978 als Ein-Mann-Betrieb gegründet. Heute eine Spezial-Spedition mit eigenem Fuhrpark und festen Fahrern.',
  content_html     = '<h2>Unsere Geschichte</h2><p>Karl-Otto Rothe gründete die Firma 1978 in Münsingen. 1985 zog der Betrieb nach Walddorfhäslach um. Was als Ein-Mann-Betrieb begann, ist heute eine spezialisierte Spedition für Maschinen- und Spezialtransporte. Seit 2010 führt Christopher Rothe das Unternehmen in zweiter Generation mit.</p><h2>Was uns leitet</h2><p>Wir liefern, was wir zusagen. Punktgenau, sorgfältig verzurrt, mit denselben Fahrern, die unsere Kunden seit Jahren kennen. Seit 2018 sind wir anerkannter Ausbildungsbetrieb.</p>'
WHERE slug = 'ueber-uns';

UPDATE pages SET
  meta_title       = 'Leistungen – Maschinen-, Spezialtransport, Lagerung | Rothe',
  meta_description = 'Maschinen- und Spezialtransporte, Tieflader bis 28 t, Tautliner, eigene Lagerhallen, Stapler bis 3,5 t. Sechs Länder, ein Ansprechpartner.',
  hero_headline    = 'Vier Disziplinen, ein Anruf.',
  hero_sub         = 'Maschinentransport, Spezialtransport, Lagerung, Stapler-Dienst – alles aus eigener Hand.',
  content_html     = '<p>Wir machen vier Dinge. Sehr konzentriert. Mit eigenen Fahrzeugen, eigenen Fahrern und eigener Lagerhalle. Kein Subunternehmer-Geflecht.</p>'
WHERE slug = 'leistungen';

UPDATE pages SET
  meta_title       = 'Fuhrpark – Tieflader 28 t, Tautliner 25 t, Motorwagen mit Kran',
  meta_description = 'Vier Fahrzeuge, klar gerechnet. Tieflader 28 t (13,60 × 2,48 × 3,70 m), Tautliner 25 t, Anhänger mit Auffahrrampe, Motorwagen mit Ladekran.',
  hero_headline    = 'Vier Fahrzeuge. Klar gerechnet.',
  hero_sub         = 'Maße, Nutzlast, Paletten – alles, was Disponenten wissen müssen.',
  content_html     = '<p>Unser Fuhrpark ist konsequent auf Maschinen- und Spezialtransporte ausgelegt. Jedes Fahrzeug hat seinen klaren Einsatzzweck – und seine festen Fahrer.</p>'
WHERE slug = 'fahrzeuge';

UPDATE pages SET
  meta_title       = 'Karriere – Berufskraftfahrer (CE) gesucht | Rothe-Transporte',
  meta_description = 'Familienbetrieb sucht Berufskraftfahrer für Maschinentransporte in DE, AT, CH, FR, NL, BE. Moderner Fuhrpark, faire Bezahlung, Stamm-Mannschaft.',
  hero_headline    = 'Stamm-Mannschaft. Kein Karussell.',
  hero_sub         = 'Wir suchen Berufskraftfahrer (CE) für unsere Touren in sechs Länder. Faire Bezahlung, fester Lkw, kurze Wege.',
  content_html     = '<h2>Was wir bieten</h2><ul><li>Moderner, eigener Fuhrpark – jeder Fahrer hat seinen Lkw.</li><li>Faire, pünktliche Bezahlung.</li><li>Familienbetrieb, kurze Entscheidungswege.</li><li>Sicherer Arbeitsplatz mit Perspektive.</li><li>Wertschätzung – kein Leiharbeiter-Karussell.</li></ul><h2>Bewerbung</h2><p>Per E-Mail an <a href="mailto:info@rothe-transporte.de">info@rothe-transporte.de</a> oder per Post: Rothe-Transporte und Speditions GbR, Aeckerlesweg 3, 72141 Walddorfhäslach.</p>'
WHERE slug = 'karriere';

UPDATE pages SET
  meta_title       = 'Kontakt – 07127 18231 | Rothe-Transporte Walddorfhäslach',
  meta_description = 'Direkt zur Disposition: Telefon 07127 18231, E-Mail info@rothe-transporte.de, Adresse Aeckerlesweg 3, 72141 Walddorfhäslach. Mo–Fr 07:00–17:00.',
  hero_headline    = 'Direkt zur Disposition.',
  hero_sub         = 'Anrufen, schreiben, vorbeikommen – ohne Umwege, ohne Callcenter.',
  content_html     = '<p>Wir nehmen Anfragen am liebsten direkt am Telefon entgegen. Maße, Termin und Strecke klären wir in einem Gespräch – Sie bekommen einen verbindlichen Festpreis.</p>'
WHERE slug = 'kontakt';

-- ── VEHICLES (technische Daten unangetastet, nur marketing_text) ──────────

UPDATE vehicles SET
  marketing_text = 'Unser Spezialist für sperrige und voluminöse Maschinen. Niedrige Ladehöhe macht ihn flexibel – auch dort, wo andere an der Höhe scheitern. 28 Tonnen Nutzlast, 33 Europaletten.'
WHERE slug = 'tieflader-sattelauflieger';

UPDATE vehicles SET
  marketing_text = 'Standardisierte, palettierte Ware – schnell und wettergeschützt. Beidseitig öffnende Schiebeplane macht Be- und Entladen schnell, auch unter Zeitdruck. 25 Tonnen, 33 Europaletten.'
WHERE slug = 'tautliner-sattelzug';

UPDATE vehicles SET
  marketing_text = 'Klein, präzise, robust. Für kompakte und palettierte Güter. Integrierte Auffahrrampe für ebenerdiges Laden, Lenkachse für enge Höfe und Innenstadt-Anlieferung.'
WHERE slug = 'anhaenger-alltagsheld';

UPDATE vehicles SET
  marketing_text = 'Wenn der Stapler vor Ort fehlt: unser Motorwagen mit Ladekran und Lenkachse setzt Maschinen punktgenau ab – auch auf engen Baustellen und in Stuttgarter Innenstadtstraßen.'
WHERE slug = 'motorwagen-mit-ladekran';

-- ── SERVICES ──────────────────────────────────────────────────────────────

UPDATE services SET
  summary   = 'Werkzeugmaschinen, Anlagen, industrielle Großteile – mit dem passenden Fahrzeug, geübten Fahrern und einem Anruf zur Disposition.',
  body_html = '<p>Maschinentransporte sind unser Kerngeschäft seit 2005. Wir transportieren Werkzeugmaschinen, Anlagenkomponenten und industrielle Großteile mit dem passenden Fahrzeug, korrekter Ladungssicherung nach VDI 2700 und festen Fahrern, die wissen, worauf es ankommt.</p>'
WHERE slug = 'maschinentransporte';

UPDATE services SET
  summary   = 'Sperrige, voluminöse oder hohe Frachten – wo Standardmaße nicht reichen, kommt unser Tieflader.',
  body_html = '<p>Niedrige Ladehöhe, 28 Tonnen Nutzlast, flexibles Ladevolumen – die Lösung für Ware, die anderswo nicht passt. Mit Festpreis am Telefon, ohne ueberraschungen.</p>'
WHERE slug = 'spezialtransporte';

UPDATE services SET
  summary   = 'Eigene Lagerhallen für Großteile, Regal- und Palettenware. Kurze Wege zwischen Lager und Fuhrpark.',
  body_html = '<p>Wir lagern Großteile, Regalware und palettierte Industrieguter in eigenen Hallen. Kurze Wege zwischen Lager und Fuhrpark sparen Zeit – Ihre Sendung steht zur Abholung bereit, wenn der Lkw vorfährt.</p>'
WHERE slug = 'lagerung';

UPDATE services SET
  summary   = 'Be- und Entladung bis 3,5 Tonnen direkt vor Ort, mit eigenem Gerät und eingespieltem Personal.',
  body_html = '<p>Wenn am Lade- oder Entladeort kein Stapler verfügbar ist, übernehmen wir das selbst – mit eigenem Gerät bis 3,5 Tonnen. Ein Anruf genügt, der Rest ist eingespielt.</p>'
WHERE slug = 'stapler-entladedienst';

-- ── FAQs (etwas direkter, weniger Floskeln) ───────────────────────────────

UPDATE faqs SET
  answer_html = '<p>Vier Stueck. <strong>Tieflader-Sattelauflieger</strong> (28 t Nutzlast, 13,60 × 2,48 × 3,70 m, 33 Europaletten). <strong>Tautliner-Sattelzug</strong> (25 t, 13,60 × 2,48 × 2,70 m, 33 Europaletten). <strong>Anhaenger</strong> mit integrierter Auffahrrampe und Lenkachse. <strong>Motorwagen mit Ladekran</strong> und Lenkachse fuer enge Baustellen.</p>'
WHERE question LIKE 'Welche Fahrzeuge%';

UPDATE faqs SET
  answer_html = '<p>Deutschland, Oesterreich, die Schweiz, Frankreich, die Niederlande, Belgien. Tagesfahrten innerhalb Sueddeutschlands sind Standard.</p>'
WHERE question LIKE 'In welche Laender%';

UPDATE faqs SET
  answer_html = '<p>Das haengt von Strecke, Gewicht, Maßen und Fahrzeug ab. Rufen Sie uns mit Abhol- und Zielort, Maßen und Gewicht der Maschine unter <a href="tel:+497127182310">07127 18231</a> an – Sie bekommen einen verbindlichen Festpreis am Telefon.</p>'
WHERE question LIKE 'Was kostet ein Maschinentransport%';

UPDATE faqs SET
  answer_html = '<p>Ja. Großteile, Regalware und palettierte Industrieguter in eigenen Hallen. Be- und Entladung uebernehmen wir mit eigenen Staplern bis 3,5 t.</p>'
WHERE question LIKE 'Bieten Sie auch Lagerung%';

UPDATE faqs SET
  answer_html = '<p>Ja. Karl-Otto Rothe gruendete 1978 in Muensingen. Seit 2010 fuehrt Christopher Rothe das Unternehmen in zweiter Generation mit. Sitz seit 1985: Walddorfhaeslach im Landkreis Reutlingen.</p>'
WHERE question LIKE 'Sind Sie ein Familienunternehmen%';

UPDATE faqs SET
  answer_html = '<p>Telefon ist am schnellsten: <a href="tel:+497127182310">07127 18231</a>. Brauchen wir: Abhol- und Zielort, Wunschtermin, Maße und Gewicht der Sendung. Alternativ E-Mail an <a href="mailto:info@rothe-transporte.de">info@rothe-transporte.de</a>.</p>'
WHERE question LIKE 'Wie buche ich%';

-- ── SETTINGS: neuer Schluessel fuer Galerie + frischer Owner-Zitat ───────

INSERT OR IGNORE INTO settings (key, value) VALUES ('gallery_enabled', '1');

UPDATE settings SET
  value = 'Wir bewegen Maschinen seit 1978. Mit eigenem Fuhrpark, festen Fahrern und einem Anruf zur Disposition. Daran haben wir nichts geaendert.'
WHERE key = 'owner_quote';

UPDATE settings SET
  value = 'Karl-Otto & Christopher Rothe'
WHERE key = 'owner_quote_attribution';

-- ── TIMELINE: kuerzere Beschreibungen ─────────────────────────────────────

UPDATE timeline_events SET description = 'Karl-Otto Rothe gruendet das Transportunternehmen als Ein-Mann-Betrieb in Muensingen.'
WHERE year = 1978;
UPDATE timeline_events SET description = 'Verlagerung des Standorts ins Walddorfhaeslach, Landkreis Reutlingen.'
WHERE year = 1985;
UPDATE timeline_events SET description = 'Anschaffung weiterer Fahrzeuge zur Bewaeltigung steigender Nachfrage.'
WHERE year = 1991;
UPDATE timeline_events SET description = 'Ausrichtung auf Maschinen- und Anlagentransporte als Kerngeschaeft.'
WHERE year = 2005;
UPDATE timeline_events SET description = 'Christopher Rothe steigt ein – die zweite Generation uebernimmt Verantwortung.'
WHERE year = 2010;
UPDATE timeline_events SET description = 'Erweiterung der Flotte um Tieflader-Sattelauflieger und Spezialanhaenger.'
WHERE year = 2015;
UPDATE timeline_events SET description = 'Anerkennung als Ausbildungsbetrieb fuer Berufskraftfahrer.'
WHERE year = 2018;

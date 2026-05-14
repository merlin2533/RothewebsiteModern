-- =============================================================================
-- 009_align_with_original_site.sql
-- Bringt alle Texte strikt in Einklang mit der Live-Original-Webseite
-- rothe-transporte.de (Stand 2026). Korrigiert inhaltliche Abweichungen
-- aus 006–008 (Telegrammstil, erfundene Formulierungen) und uebernimmt
-- die Original-Tonalitaet, leicht optimiert.
-- Technische Fahrzeugdaten bleiben unangetastet.
-- =============================================================================

-- ── Settings: Fakten korrigieren ──────────────────────────────────────────────

-- Original-Bürozeiten: Mo-Fr 7:00-18:00 (nicht 17:00).
UPDATE settings SET value = 'Mo-Fr 07:00-18:00' WHERE key = 'opening_hours';
UPDATE settings SET value = 'Mo-Fr 07:00-18:00' WHERE key = 'opening_hours_schema';

-- Original-Adresse: Äckerlesweg 3 (mit Umlaut).
UPDATE settings SET value = 'Äckerlesweg 3' WHERE key = 'address_street';

-- Gesellschafter / Geschäftsführung – Schreibweise wie im Original.
UPDATE settings SET value = 'Karl Otto Rothe & Christopher Rothe' WHERE key = 'owners';
UPDATE settings SET value = 'Karl Otto Rothe' WHERE key = 'ceo_name';

UPDATE settings SET
  value = 'In zweiter Generation liefern wir Waren und Güter termingerecht nach Kundenwunsch ans Ziel. Denn gemeinsam bewegen wir Großes!'
WHERE key = 'owner_quote';

UPDATE settings SET
  value = 'Karl Otto & Christopher Rothe'
WHERE key = 'owner_quote_attribution';

UPDATE settings SET
  value = 'Rothe-Transporte – Ihr Experte für Maschinen- & Spezialtransporte | Walddorfhäslach'
WHERE key = 'meta_default_title';

UPDATE settings SET
  value = 'Familiengeführte Spedition seit 1978 in Walddorfhäslach. Maschinen- und Spezialtransporte, Lagerhaltung – alles aus einer Hand. Touren in DE, AT, CH, FR, NL, BE. Telefon: 07127 18231.'
WHERE key = 'meta_default_description';

-- ── Pages ─────────────────────────────────────────────────────────────────────

-- Startseite
UPDATE pages SET
  meta_title       = 'Rothe-Transporte – Ihr Experte für Maschinen- & Spezialtransporte',
  meta_description = 'Familiengeführte Spedition seit 1978 in Walddorfhäslach. Maschinen- und Spezialtransporte, Lagerhaltung, alles aus einer Hand. Touren in DE, AT, CH, FR, NL, BE.',
  hero_headline    = 'Ihr Experte für Maschinen- & Spezialtransporte',
  hero_sub         = 'Seit 1978 bringen wir Ihre Güter termingerecht und zuverlässig ans Ziel – quer durch Deutschland und das europäische Ausland.',
  content_html     = '<p>Wir sind Rothe-Transporte – ein familiengeführtes Transport- und Speditionsunternehmen aus Walddorfhäslach. Seit über 40 Jahren sind wir für Sie quer durch Deutschland sowie im europäischen Ausland unterwegs und sorgen dafür, dass Ihre Güter termingerecht und zuverlässig am Ziel ankommen.</p><h2>Alles aus einer Hand</h2><p>Von der Beratung über die Einholung von Genehmigungen bis hin zur Anlieferung an den Bestimmungsort – bei uns bekommen Sie den kompletten Service. In zweiter Generation liefern wir Waren und Güter termingerecht nach Kundenwunsch ans Ziel. Denn gemeinsam bewegen wir Großes!</p>'
WHERE slug = 'home';

-- Über uns
UPDATE pages SET
  meta_title       = 'Über uns – Familienspedition seit 1978 | Rothe-Transporte',
  meta_description = 'Rothe-Transporte und Speditions GbR: 1978 von Karl Otto Rothe gegründet, heute in zweiter Generation. Kompetenter Partner für Maschinenbau, Automobilindustrie und Landmaschinentechnik.',
  hero_headline    = 'Über uns',
  hero_sub         = 'In zweiter Generation liefern wir Waren und Güter termingerecht nach Kundenwunsch ans Ziel.',
  content_html     = '<h2>Unsere Geschichte</h2><p>Karl Otto Rothe gründete das Unternehmen 1978 in Münsingen. 1985 zog der Betrieb nach Walddorfhäslach um. Aus einem kleinen Transportunternehmen ist über die Jahrzehnte ein kompetenter Partner für Maschinen- und Spezialtransporte geworden. Seit 2010 führt Christopher Rothe das Unternehmen in zweiter Generation mit.</p><h2>Ihr kompetenter Partner</h2><p>Wir sind in vielen Industriebereichen zu Hause – vom Maschinenbau über die Automobilindustrie bis zur Landmaschinentechnik. In zweiter Generation liefern wir Waren und Güter termingerecht nach Kundenwunsch ans Ziel. Seit 2018 sind wir zudem anerkannter IHK-Ausbildungsbetrieb.</p>'
WHERE slug = 'ueber-uns';

-- Leistungen
UPDATE pages SET
  meta_title       = 'Leistungen – Güter-, Maschinen- & Spezialtransporte | Rothe-Transporte',
  meta_description = 'Gütertransporte, Maschinentransporte und Spezialtransporte bis 50 Tonnen. Beratung, Genehmigungen, Begleitfahrzeuge, Ladungssicherung und Lagerung – alles aus einer Hand.',
  hero_headline    = 'Unsere Leistungen',
  hero_sub         = 'Für alles die passende Ladefläche – vom Gütertransport bis zum Schwertransport.',
  content_html     = '<p>Ob Komplett- oder Teilladung, großvolumige Maschine oder Schwertransport – bei uns finden Sie für jede Aufgabe die passende Ladefläche. Wir beraten Sie gerne persönlich.</p><h2>Alles aus einer Hand</h2><p>Transportberatung, Planung und Betreuung, Verzollung, Einholung von Genehmigungen, Organisation von Begleitfahrzeugen, Liefertermin-Verfolgung, Ladungssicherung und Lagermöglichkeiten inklusive Staplerservice – alles aus einer Hand.</p>'
WHERE slug = 'leistungen';

-- Fahrzeuge / Fuhrpark
UPDATE pages SET
  meta_title       = 'Fuhrpark – Tautliner, Tieflader & Spezialtrailer | Rothe-Transporte',
  meta_description = 'Unser Fuhrpark für Maschinen- und Spezialtransporte: Tautliner, Tieflader und Spezialanhänger. Alle Fahrzeuge in technisch einwandfreiem Zustand nach EURO-6-Norm.',
  hero_headline    = 'Unser Fuhrpark',
  hero_sub         = 'Moderne Fahrzeuge nach EURO-6-Norm – für jede Ladung das richtige.',
  content_html     = '<p>Unser Fuhrpark ist konsequent auf Maschinen- und Spezialtransporte ausgelegt. Alle Fahrzeuge sind in technisch einwandfreiem Zustand, entsprechen der EURO-6-Norm und sind mit den passenden Mitteln zur Ladungssicherung ausgestattet.</p><p>Sie sind unsicher, welches Fahrzeug zu Ihrer Ladung passt? Rufen Sie uns an – wir beraten Sie gerne.</p>'
WHERE slug = 'fahrzeuge';

-- Karriere
UPDATE pages SET
  meta_title       = 'Karriere – Berufskraftfahrer für den Fernverkehr | Rothe-Transporte',
  meta_description = 'Familiengeführter Betrieb mit kurzen Entscheidungswegen sucht Berufskraftfahrer für den Fernverkehr. Angemessene Entlohnung, sicherer Arbeitsplatz, moderner Fuhrpark.',
  hero_headline    = 'Sie möchten Großes bewegen?',
  hero_sub         = 'Werden Sie Teil unseres familiengeführten Teams – mit kurzen Entscheidungswegen und großer Wertschätzung.',
  content_html     = '<p>Unsere engagierten und qualifizierten Mitarbeiter sind der Schlüssel zu unserem Erfolg. Als familiengeführter Betrieb setzen wir auf kurze Entscheidungswege und große Wertschätzung.</p><h2>Was wir bieten</h2><ul><li>Familiengeführter Betrieb mit kurzen Entscheidungswegen und großer Wertschätzung</li><li>Angemessene und pünktliche Entlohnung</li><li>Sicherer Arbeitsplatz mit interessanten Benefits</li><li>Moderner Fuhrpark</li></ul><h2>Aktuelle Stelle</h2><p>Berufskraftfahrer für den Fernverkehr (m/w/d)</p><h2>So bewerben Sie sich</h2><p>Schicken Sie uns Ihre Bewerbung per E-Mail an <a href="mailto:info@rothe-transporte.de">info@rothe-transporte.de</a> oder per Post an: Rothe-Transporte und Speditions GbR, Äckerlesweg 3, 72141 Walddorfhäslach. Für Rückfragen erreichen Sie uns unter <a href="tel:+497127182310">07127 18231</a>.</p>'
WHERE slug = 'karriere';

-- Kontakt
UPDATE pages SET
  meta_title       = 'Kontakt – Rothe-Transporte Walddorfhäslach | 07127 18231',
  meta_description = 'So erreichen Sie uns: Telefon 07127 18231, E-Mail info@rothe-transporte.de, Äckerlesweg 3, 72141 Walddorfhäslach. Bürozeiten Mo–Fr 7:00–18:00 Uhr.',
  hero_headline    = 'Sprechen wir miteinander',
  hero_sub         = 'Direkter Kontakt, kurze Wege – so erreichen Sie uns.',
  content_html     = '<p>Sie erreichen uns am schnellsten telefonisch unter <a href="tel:+497127182310">07127 18231</a>, Mo–Fr von 7 bis 18 Uhr. Gerne können Sie uns auch eine E-Mail an <a href="mailto:info@rothe-transporte.de">info@rothe-transporte.de</a> schicken.</p><h2>Ihre Ansprechpartner</h2><p>Karl Otto Rothe – Angebote<br>Christopher Rothe – Disposition (<a href="mailto:dispo@rothe-transporte.de">dispo@rothe-transporte.de</a>)<br>Anette Rothe – Abrechnung (<a href="mailto:abrechnung@rothe-transporte.de">abrechnung@rothe-transporte.de</a>)</p>'
WHERE slug = 'kontakt';

-- Impressum / Datenschutz: Adresse und Schreibweisen korrigieren.
UPDATE pages SET
  content_html = '<h2>Anbieter</h2><p>Rothe-Transporte und Speditions GbR<br>Äckerlesweg 3<br>72141 Walddorfhäslach<br>Deutschland</p><h2>Vertretungsberechtigte Gesellschafter</h2><p>Karl Otto Rothe<br>Christopher Rothe</p><h2>Kontakt</h2><p>Telefon: <a href="tel:+497127182310">07127 18231</a><br>E-Mail: <a href="mailto:info@rothe-transporte.de">info@rothe-transporte.de</a></p><h2>Haftung für Inhalte</h2><p>Als Diensteanbieter sind wir gemäß § 7 Abs.1 TMG für eigene Inhalte auf diesen Seiten nach den allgemeinen Gesetzen verantwortlich. Nach §§ 8 bis 10 TMG sind wir als Diensteanbieter jedoch nicht verpflichtet, übermittelte oder gespeicherte fremde Informationen zu überwachen oder nach Umständen zu forschen, die auf eine rechtswidrige Tätigkeit hinweisen.</p><h2>Haftung für Links</h2><p>Unser Angebot enthält ggf. Links zu externen Websites Dritter, auf deren Inhalte wir keinen Einfluss haben. Deshalb können wir für diese fremden Inhalte auch keine Gewähr übernehmen.</p><h2>Urheberrecht</h2><p>Die durch die Seitenbetreiber erstellten Inhalte und Werke auf diesen Seiten unterliegen dem deutschen Urheberrecht.</p>'
WHERE slug = 'impressum';

UPDATE pages SET
  content_html = '<h2>Verantwortlicher</h2><p>Rothe-Transporte und Speditions GbR<br>Äckerlesweg 3<br>72141 Walddorfhäslach<br>Telefon: 07127 18231<br>E-Mail: info@rothe-transporte.de</p><h2>Erhebung allgemeiner Informationen</h2><p>Wenn Sie diese Website aufrufen, werden automatisch Informationen allgemeiner Natur durch unseren Webserver erfasst (Browsertyp, Betriebssystem, Domainname, IP-Adresse, Datum, Zugriffszeit). Diese Informationen werden anonymisiert ausgewertet und ausschließlich zur Verbesserung des Angebots verwendet.</p><h2>Cookies und Tracking</h2><p>Diese Website verwendet keine Cookies, kein Tracking, kein Analyse-Tool und keine externen Skripte. Es findet keine Datenübermittlung an Dritte statt.</p><h2>Kontaktaufnahme</h2><p>Wenn Sie uns per E-Mail kontaktieren, werden Ihre Angaben zur Bearbeitung Ihrer Anfrage gespeichert. Diese Daten geben wir nicht ohne Ihre Einwilligung weiter.</p><h2>Ihre Rechte</h2><p>Sie haben das Recht auf Auskunft, Berichtigung, Löschung und Einschränkung der Verarbeitung Ihrer personenbezogenen Daten. Wenden Sie sich an die oben genannte Adresse.</p>'
WHERE slug = 'datenschutz';

-- ── Vehicles (technische Daten unangetastet, nur marketing_text) ──────────────

UPDATE vehicles SET
  marketing_text = 'Unser Tautliner ist unser Allrounder! Ideal für normalmaßige, verpackte und nicht temperaturempfindliche Ware. Die beidseitig öffnende Plane ermöglicht ein schnelles Be- und Entladen, das Edscha-Verdeck auch die Kranbeladung von oben.'
WHERE slug = 'tautliner-sattelzug';

UPDATE vehicles SET
  marketing_text = 'Unser Tiefbett-Sattelanhänger ist der Spezialist für Ladung, die über das Standardmaß hinausgeht. Mit niedriger Ladehöhe und bis zu 28 Tonnen Nutzlast bringen wir auch sperrige und schwere Güter sicher ans Ziel.'
WHERE slug = 'tieflader-sattelauflieger';

UPDATE vehicles SET
  marketing_text = 'Alltagsheld – klein, aber fein! Ideal für kompakte, leichte und palettierte Güter, gerade im urbanen Bereich. Mit Auffahrrampe und Lenkachse kommen wir auch in enge Höfe und schwierige Anlieferungen.'
WHERE slug = 'anhaenger-alltagsheld';

UPDATE vehicles SET
  marketing_text = 'Wenn am Zielort kein Stapler bereitsteht: Unser Motorwagen mit Ladekran und Lenkachse setzt Ihre Maschine punktgenau ab – auch auf engen Baustellen und in der Innenstadt.'
WHERE slug = 'motorwagen-mit-ladekran';

-- ── Services ──────────────────────────────────────────────────────────────────

UPDATE services SET
  summary   = 'Großvolumige Ladungen bis 3 Meter Breite – Industrieanlagen, Aggregate, Apparate und vieles mehr.',
  body_html = '<p>Großvolumige Ladungen bis 3 Meter Breite wie Industrieanlagen, Aggregate oder Apparate sind unser Spezialgebiet. Mit unserem speziellen Fuhrpark und unserem Fachwissen meistern wir Ihren Transport. Die nötige Genehmigung innerhalb Deutschlands ist vorhanden.</p>'
WHERE slug = 'maschinentransporte';

UPDATE services SET
  summary   = 'Großraumtransporte unter Plane und Schwertransporte bis 50 Tonnen.',
  body_html = '<p>Großraumtransporte unter Plane bis 5 m Breite, 3,5 m Höhe und 15 m Länge sowie Schwertransporte bis 50 Tonnen wickeln wir zuverlässig für Sie ab. Vorlaufzeit und eine individuelle Genehmigung sind erforderlich – wir beraten Sie gerne.</p>'
WHERE slug = 'spezialtransporte';

UPDATE services SET
  summary   = 'Lagermöglichkeiten für Großteile, Regal- und Palettenware in unseren eigenen Hallen.',
  body_html = '<p>In unseren eigenen Hallen bieten wir Ihnen Lagermöglichkeiten für Großteile, Regal- oder Palettenware an. Be- und Entladung übernehmen wir mit unserem eigenen Staplerservice bis 3,5 Tonnen.</p>'
WHERE slug = 'lagerung';

UPDATE services SET
  summary   = 'Be- und Entladung vor Ort mit eigenem Staplerservice bis 3,5 Tonnen.',
  body_html = '<p>Wenn am Lade- oder Entladeort kein Stapler verfügbar ist, übernehmen wir das mit eigenem Gerät bis 3,5 Tonnen. Unser Staplerservice gehört fest zu unserem Komplettpaket – alles aus einer Hand.</p>'
WHERE slug = 'stapler-entladedienst';

-- ── FAQs – Fakten an Original angleichen (Bürozeiten 7–18 Uhr) ────────────────

UPDATE faqs SET
  answer_html = '<p>Am einfachsten geht es per Telefon: <a href="tel:+497127182310">07127 18231</a>. Wir sind Mo–Fr von 7 bis 18 Uhr für Sie da. Sagen Sie uns, wo wir abholen, wohin es geht und wann es soweit sein soll – Maße und Gewicht der Sendung helfen uns, das passende Fahrzeug zu wählen. Alternativ per E-Mail an <a href="mailto:info@rothe-transporte.de">info@rothe-transporte.de</a>.</p>'
WHERE question LIKE 'Wie buche ich%';

-- ── Timeline – Wortlaut an Original angleichen + 2020 & 2022 ergänzen ─────────

UPDATE timeline_events SET
  title = 'Gründung in Münsingen',
  description = 'Karl Otto Rothe gründet das Transportunternehmen in Münsingen.'
WHERE year = 1978;

UPDATE timeline_events SET
  title = 'Umzug nach Walddorfhäslach',
  description = 'Der Betrieb zieht nach Walddorfhäslach im Landkreis Reutlingen um.'
WHERE year = 1985;

UPDATE timeline_events SET
  title = 'Fahrzeuge & Lager',
  description = 'Fahrzeugzugang und Erweiterung der Lagerflächen.'
WHERE year = 1991;

UPDATE timeline_events SET
  title = 'Spezialisierung Maschinentransport',
  description = 'Das Unternehmen spezialisiert sich auf Maschinentransporte.'
WHERE year = 2005;

UPDATE timeline_events SET
  title = 'Zweite Generation',
  description = 'Christopher Rothe tritt in das Unternehmen ein.'
WHERE year = 2010;

UPDATE timeline_events SET
  title = 'Spezialtrailer',
  description = 'Der Fuhrpark wird um Spezialtrailer erweitert.'
WHERE year = 2015;

UPDATE timeline_events SET
  title = 'IHK-Ausbildungsbetrieb',
  description = 'Rothe-Transporte wird als IHK-Ausbildungsbetrieb zugelassen.'
WHERE year = 2018;

INSERT INTO timeline_events (year, title, description, sort_order)
SELECT 2020, 'Neue Regalsysteme', 'Installation neuer Regalsysteme in den Lagerhallen.', 8
WHERE NOT EXISTS (SELECT 1 FROM timeline_events WHERE year = 2020);

INSERT INTO timeline_events (year, title, description, sort_order)
SELECT 2022, 'Erste Auszubildende', 'Die erste Auszubildende zur Berufskraftfahrerin beginnt bei uns.', 9
WHERE NOT EXISTS (SELECT 1 FROM timeline_events WHERE year = 2022);

-- =============================================================================
-- 005_landing_pages_and_redirects.sql
-- Branchen-/Stadt-Landingpages und 301-Redirect-Map.
-- =============================================================================

-- ── Landing pages (Industry / City / Campaign) ──────────────────────────────
CREATE TABLE IF NOT EXISTS landing_pages (
    id               INTEGER PRIMARY KEY AUTOINCREMENT,
    slug             TEXT    NOT NULL UNIQUE,         -- z.B. 'maschinentransport-stuttgart'
    kind             TEXT    NOT NULL DEFAULT 'industry', -- industry | city | campaign
    title            TEXT    NOT NULL,
    eyebrow          TEXT,                            -- z.B. 'INDUSTRIE-LANDINGPAGE'
    headline         TEXT,
    sub              TEXT,
    intro_html       TEXT,
    body_html        TEXT,
    meta_title       TEXT,
    meta_description TEXT,
    cta_label        TEXT    NOT NULL DEFAULT 'Jetzt anfragen',
    primary_phone    TEXT,                            -- optional Override (Call-Tracking)
    primary_email    TEXT,                            -- optional Override
    related_vehicles TEXT,                            -- CSV von vehicle slugs
    related_services TEXT,                            -- CSV von service slugs
    region_name      TEXT,                            -- z.B. 'Stuttgart' (fuer LocalBusiness areaServed)
    is_published     INTEGER NOT NULL DEFAULT 1,
    sort_order       INTEGER NOT NULL DEFAULT 0,
    created_at       TEXT    NOT NULL DEFAULT (strftime('%Y-%m-%d %H:%M:%S', 'now')),
    updated_at       TEXT    NOT NULL DEFAULT (strftime('%Y-%m-%d %H:%M:%S', 'now'))
);
CREATE INDEX IF NOT EXISTS idx_lp_slug      ON landing_pages(slug);
CREATE INDEX IF NOT EXISTS idx_lp_kind      ON landing_pages(kind);
CREATE INDEX IF NOT EXISTS idx_lp_published ON landing_pages(is_published);

-- Seeds: 3 Branchen + 3 Staedte. Texte ueberarbeitet, Specs unveraendert.
INSERT INTO landing_pages
  (slug, kind, title, eyebrow, headline, sub, intro_html, body_html,
   meta_title, meta_description, cta_label, related_vehicles, related_services, region_name, sort_order)
VALUES
('maschinentransport',
 'industry',
 'Maschinentransport',
 'INDUSTRIE-LANDINGPAGE',
 'Maschinentransport mit eigenem Fuhrpark – seit 1978.',
 'Werkzeugmaschinen, Anlagenkomponenten, industrielle Großteile. Disposition aus Walddorfhäslach, Touren in sechs Länder.',
 '<p>Wir transportieren Maschinen, seit 1978. Mit eigenem Tieflader-Sattelauflieger (28 t Nutzlast, 13,60 m × 2,48 m × 3,70 m), Tautliner-Sattelzug (25 t) und Motorwagen mit Ladekran. Festen Fahrern. Klarer Disposition.</p>',
 '<h2>Wofür Sie uns rufen</h2><ul><li>Werkzeugmaschinen aus dem Maschinenbau – sperrig, schwer, empfindlich.</li><li>Anlagenkomponenten für Inbetriebnahmen, Verlagerungen, Modernisierungen.</li><li>Großteile aus dem Anlagen- und Sondermaschinenbau.</li></ul><h2>Was Sie bekommen</h2><ul><li>Verbindlicher Festpreis am Telefon.</li><li>Stamm-Fahrer mit dem passenden Fahrzeug.</li><li>Eigene Lagerhalle für Zwischenlager und Teile-Konsolidierung.</li></ul>',
 'Maschinentransport – Festpreis am Telefon | Rothe-Transporte',
 'Maschinentransport mit eigenem Fuhrpark seit 1978. Tieflader 28 t, Tautliner 25 t, Motorwagen mit Ladekran. Festpreis am Telefon: 07127 18231.',
 'Festpreis anfragen',
 'tieflader-sattelauflieger,tautliner-sattelzug,motorwagen-mit-ladekran',
 'maschinentransporte,spezialtransporte,lagerung',
 NULL, 1),
('anlagentransport',
 'industry',
 'Anlagentransport',
 'INDUSTRIE-LANDINGPAGE',
 'Anlagen verlagern – ohne dass die Produktion länger steht als nötig.',
 'Demontagebegleitung, Transport, Aufstellung. Ein Ansprechpartner, ein Fuhrpark, eine Verantwortung.',
 '<p>Anlagentransporte sind kein Standard-Frachtgeschäft. Es geht um Termin, Reihenfolge, Schwerpunkt – und darum, dass am anderen Ende alles unbeschädigt ankommt. Genau dafür kombinieren wir Tieflader, Tautliner und Motorwagen mit Ladekran.</p>',
 '<h2>Was wir bewegen</h2><ul><li>Pressen, Bearbeitungszentren, CNC-Maschinen.</li><li>Förder- und Lagerautomation.</li><li>Komplette Produktionslinien in mehreren Sendungen, koordiniert.</li></ul><h2>Wie wir arbeiten</h2><ol><li>Vorab: Maße, Gewichte, Schwerpunkte, Hub- und Stellpläne klären.</li><li>Disposition: passendes Fahrzeug + Stamm-Fahrer + Rampen-/Krantermin.</li><li>Transport: Ladungssicherung nach VDI 2700; bei Bedarf Begleitfahrzeug.</li></ol>',
 'Anlagentransport – Maschinen verlagern ohne lange Standzeit | Rothe-Transporte',
 'Anlagentransporte mit Tieflader, Tautliner und Motorwagen mit Ladekran. Festpreis und fester Ansprechpartner. Anrufen: 07127 18231.',
 'Verlagerung anfragen',
 'tieflader-sattelauflieger,motorwagen-mit-ladekran,tautliner-sattelzug',
 'spezialtransporte,maschinentransporte,stapler-entladedienst',
 NULL, 2),
('landmaschinen-spedition',
 'industry',
 'Landmaschinen-Spedition',
 'INDUSTRIE-LANDINGPAGE',
 'Landmaschinen-Transport mit Tieflader und Auffahrrampe.',
 'Mähdrescher, Traktoren, Forsttechnik – sicher verzurrt, pünktlich am Hof.',
 '<p>Landmaschinen sind sperrig, hoch, oft asymmetrisch. Unsere Tieflader-Sattelauflieger mit niedriger Ladehöhe und unser Anhänger mit integrierter Auffahrrampe sind dafür gebaut.</p>',
 '<h2>Was wir transportieren</h2><ul><li>Traktoren, Mähdrescher, Feldhäcksler.</li><li>Forst- und Erntetechnik mit Sondermaßen.</li><li>Anbaugeräte, Zubehör, Ersatzteile zur Maschine.</li></ul><h2>Was Sie bekommen</h2><ul><li>Niedrige Ladehöhe (Tieflader 3,70 m Gesamthöhe inkl. Maschine).</li><li>Lenkachse für enge Höfe und Feldwege.</li><li>Festpreis und Wunschtermin am Telefon.</li></ul>',
 'Landmaschinen-Spedition – Mähdrescher, Traktor, Forsttechnik | Rothe-Transporte',
 'Landmaschinen-Spedition mit Tieflader (28 t, niedrige Ladehöhe) und Anhänger mit Auffahrrampe und Lenkachse. Festpreis: 07127 18231.',
 'Landmaschinen-Transport anfragen',
 'tieflader-sattelauflieger,anhaenger-alltagsheld',
 'maschinentransporte,spezialtransporte',
 NULL, 3),
('maschinentransport-reutlingen',
 'city',
 'Maschinentransport Reutlingen',
 'STADT-LANDINGPAGE',
 'Ihre Maschinenspedition aus dem Landkreis Reutlingen.',
 'Sitz Walddorfhäslach – Tagesfahrten in Stuttgart, Tübingen, Ulm und nach Süden bis zur Schweiz.',
 '<p>Wir sind Reutlinger. Unser Standort liegt direkt im Landkreis. Das bedeutet: kurze Anfahrt zu Ihnen, schnelle Disposition, Stamm-Fahrer, die jede Industriestraße kennen.</p>',
 '<h2>Reutlingen + Umkreis 80 km</h2><ul><li>Tübingen, Stuttgart, Ulm, Esslingen, Pforzheim – Tagestouren.</li><li>Eigene Lagerhalle in Walddorfhäslach für Konsolidierung und Zwischenlager.</li><li>Stapler bis 3,5 t für Be- und Entladung vor Ort.</li></ul><h2>Was Sie bekommen</h2><ul><li>Festpreis am Telefon.</li><li>Tieflader (28 t), Tautliner (25 t), Motorwagen mit Ladekran.</li><li>Anruf direkt zur Disposition – kein Callcenter.</li></ul>',
 'Maschinentransport Reutlingen – Spedition Walddorfhäslach | Rothe-Transporte',
 'Maschinentransport-Spedition aus Walddorfhäslach (Landkreis Reutlingen). Tieflader, Tautliner, Ladekran. Festpreis: 07127 18231.',
 'Anfrage Reutlingen',
 'tieflader-sattelauflieger,tautliner-sattelzug,motorwagen-mit-ladekran',
 'maschinentransporte,lagerung',
 'Reutlingen', 4),
('maschinentransport-stuttgart',
 'city',
 'Maschinentransport Stuttgart',
 'STADT-LANDINGPAGE',
 'Maschinentransport Stuttgart – Tagesfahrten von Walddorfhäslach.',
 'Vom Neckartal bis zum Stuttgarter Norden – mit Tieflader, Ladekran und Stadt-Erfahrung.',
 '<p>Stuttgart ist eng. Engagierte Innenstadtstraßen, Hanglagen, schwierige Anlieferzeiten. Unser Motorwagen mit Ladekran und Lenkachse setzt Maschinen punktgenau ab, auch dort, wo der Sattelzug nicht reinkommt.</p>',
 '<h2>Stuttgart und Umland</h2><ul><li>Tagesfahrten zwischen Walddorfhäslach und allen Stuttgarter Stadtteilen.</li><li>Lieferzeitfenster für Innenstadt-Anlieferungen.</li><li>Begleitfahrzeug auf Wunsch (für Sondertransporte).</li></ul><h2>Welches Fahrzeug für Stuttgart?</h2><ul><li><strong>Innenstadt</strong>: Motorwagen mit Ladekran (Lenkachse, kurzer Wendekreis).</li><li><strong>Industriegebiete</strong>: Tieflader-Sattelauflieger oder Tautliner.</li></ul>',
 'Maschinentransport Stuttgart – Festpreis am Telefon | Rothe-Transporte',
 'Maschinentransport Stuttgart aus Walddorfhäslach. Tieflader, Tautliner, Motorwagen mit Ladekran für die Innenstadt. 07127 18231.',
 'Anfrage Stuttgart',
 'motorwagen-mit-ladekran,tieflader-sattelauflieger,tautliner-sattelzug',
 'maschinentransporte,spezialtransporte',
 'Stuttgart', 5),
('maschinentransport-tuebingen',
 'city',
 'Maschinentransport Tübingen',
 'STADT-LANDINGPAGE',
 'Maschinentransport Tübingen – Ihr Spediteur direkt nebenan.',
 'Wir sitzen 12 km von Tübingen entfernt. Schneller geht es nicht.',
 '<p>Tübingen liegt direkt vor unserer Haustür. Wenn Sie aus dem Tübinger Industriegebiet, der Universität oder einem Maschinenbauer im Steinlachtal anrufen, ist unser Tieflader oft am gleichen Tag verfügbar.</p>',
 '<h2>Was wir für Tübingen tun</h2><ul><li>Tagesfahrten in alle Tübinger Stadtteile und das Umland.</li><li>Eigene Lagerhalle in Walddorfhäslach für Konsolidierungen.</li><li>Maschinentransport mit Festpreis – kein Stundensatz.</li></ul>',
 'Maschinentransport Tübingen – 12 km vor der Haustür | Rothe-Transporte',
 'Maschinentransport Tübingen mit eigenem Fuhrpark aus Walddorfhäslach (12 km Entfernung). Festpreis am Telefon: 07127 18231.',
 'Anfrage Tübingen',
 'tieflader-sattelauflieger,tautliner-sattelzug,motorwagen-mit-ladekran',
 'maschinentransporte,lagerung,stapler-entladedienst',
 'Tübingen', 6);

-- ── Redirect map (alte URLs auf neue – pflegbar im Admin) ───────────────────
CREATE TABLE IF NOT EXISTS redirects (
    id          INTEGER PRIMARY KEY AUTOINCREMENT,
    from_path   TEXT    NOT NULL UNIQUE,           -- z.B. '/elementor-1287/'
    to_path     TEXT    NOT NULL,                  -- z.B. '/ueber-uns'
    status_code INTEGER NOT NULL DEFAULT 301,
    is_active   INTEGER NOT NULL DEFAULT 1,
    hits        INTEGER NOT NULL DEFAULT 0,
    last_hit_at TEXT,
    created_at  TEXT    NOT NULL DEFAULT (strftime('%Y-%m-%d %H:%M:%S', 'now')),
    updated_at  TEXT    NOT NULL DEFAULT (strftime('%Y-%m-%d %H:%M:%S', 'now'))
);
CREATE INDEX IF NOT EXISTS idx_redirects_from   ON redirects(from_path);
CREATE INDEX IF NOT EXISTS idx_redirects_active ON redirects(is_active);

INSERT INTO redirects (from_path, to_path) VALUES
('/elementor-1287',    '/ueber-uns'),
('/elementor-1287/',   '/ueber-uns'),
('/elementor-1248',    '/fahrzeuge'),
('/elementor-1248/',   '/fahrzeuge'),
('/elementor-1854',    '/karriere'),
('/elementor-1854/',   '/karriere');

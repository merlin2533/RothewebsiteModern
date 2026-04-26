-- =============================================================================
-- 003_seo_faqs_tracking.sql
-- FAQs (FAQPage schema + sichtbare Liste) und SEO/Tracking-Settings.
-- =============================================================================

CREATE TABLE IF NOT EXISTS faqs (
    id          INTEGER PRIMARY KEY AUTOINCREMENT,
    question    TEXT    NOT NULL,
    answer_html TEXT    NOT NULL,
    page_slug   TEXT    NOT NULL DEFAULT 'home',
    is_active   INTEGER NOT NULL DEFAULT 1,
    sort_order  INTEGER NOT NULL DEFAULT 0,
    created_at  TEXT    NOT NULL DEFAULT (strftime('%Y-%m-%d %H:%M:%S', 'now')),
    updated_at  TEXT    NOT NULL DEFAULT (strftime('%Y-%m-%d %H:%M:%S', 'now'))
);

CREATE INDEX IF NOT EXISTS idx_faqs_page_slug ON faqs(page_slug);
CREATE INDEX IF NOT EXISTS idx_faqs_active    ON faqs(is_active);

-- Seed: realistische, faktenbasierte Q&A; LLM-zitierbar.
INSERT INTO faqs (question, answer_html, page_slug, sort_order) VALUES
('Welche Fahrzeuge umfasst Ihr Fuhrpark?',
 '<p>Wir betreiben einen Tieflader-Sattelauflieger (28 t Nutzlast, 13,60 m × 2,48 m × 3,70 m, 33 Europaletten), einen Tautliner-Sattelzug (25 t, 13,60 m × 2,48 m × 2,70 m, 33 Europaletten), einen kompakten Anhaenger mit Auffahrrampe und Lenkachse sowie einen Motorwagen mit Ladekran und Lenkachse fuer enge Baustellen.</p>',
 'home', 1),
('In welche Laender fahren Sie regelmaessig?',
 '<p>Wir bedienen regelmaessig Deutschland, Oesterreich, die Schweiz, Frankreich, die Niederlande und Belgien. Tagesfahrten innerhalb des deutschsprachigen Raums sind Standard.</p>',
 'home', 2),
('Was kostet ein Maschinentransport?',
 '<p>Der Preis haengt von Strecke, Gewicht, Maßen und benoetigtem Fahrzeug ab. Rufen Sie uns mit Abhol- und Zielort, Maßen und Gewicht der Maschine unter <a href="tel:+497127182310">07127 18231</a> an, dann nennen wir Ihnen einen verbindlichen Festpreis.</p>',
 'home', 3),
('Bieten Sie auch Lagerung an?',
 '<p>Ja. Wir lagern Großteile, Regalware und palettierte Industrieguter in eigenen Hallen. Be- und Entladung uebernehmen wir mit eigenen Staplern bis 3,5 t.</p>',
 'home', 4),
('Sind Sie ein Familienunternehmen?',
 '<p>Ja. Karl-Otto Rothe gruendete die Spedition 1978 in Muensingen. Seit 2010 fuehrt Christopher Rothe das Unternehmen in zweiter Generation mit. Sitz ist seit 1985 Walddorfhaeslach im Landkreis Reutlingen.</p>',
 'home', 5),
('Wie buche ich einen Transport?',
 '<p>Am schnellsten per Anruf unter <a href="tel:+497127182310">07127 18231</a>. Alternativ per E-Mail an <a href="mailto:info@rothe-transporte.de">info@rothe-transporte.de</a> mit Abhol- und Zielort, gewuenschtem Termin sowie Maßen und Gewicht der Sendung.</p>',
 'home', 6);

-- ── Neue Settings-Keys ──────────────────────────────────────────────────────
-- Tracking (alle leer = deaktiviert; vendor-neutral, alles opt-in)
INSERT OR IGNORE INTO settings (key, value) VALUES
  ('gtm_container_id',     ''),
  ('plausible_domain',     ''),
  ('plausible_script_url', 'https://plausible.io/js/script.js'),
  ('matomo_url',           ''),
  ('matomo_site_id',       ''),
  ('search_action_target', ''),  -- z.B. https://www.google.com/search?q=site%3Arothe-transporte.de+{search_term_string}
  ('og_default_image_id',  ''),
  ('vat_id',               ''),  -- Optional fuer Impressum (USt-IdNr.)
  ('ceo_name',             'Karl-Otto Rothe'),
  ('twitter_handle',       '');

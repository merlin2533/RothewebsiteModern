-- =============================================================================
-- 010_fleet_from_original.sql
-- Bringt den Fuhrpark exakt auf den Stand der Original-Webseite
-- rothe-transporte.de. Christopher Rothe hat bestätigt: "Motorwagen mit
-- Ladekran – haben wir nicht." Stattdessen hat das Original 5 Fahrzeuge:
-- Tautliner-Sattelzug, Semitrailer, Megatrailer, Tiefbett-Sattelanhänger,
-- Citytrailer "Alltagsheld".
--
-- Fahrzeugbilder: echte Renderings von rothe-transporte.de, abgelegt unter
-- uploads/. MediaResolver erzeugt die Web-Varianten on-the-fly.
-- =============================================================================

-- ── Media-Einträge für die 5 echten Fahrzeugbilder ────────────────────────────

INSERT INTO media (filename, original_name, mime, width, height, alt_text) VALUES
  ('from-original/tautliner-sattelzug-1600.jpg', 'tautliner-sattelzug.png', 'image/jpeg', 1138, 373,
   'Tautliner-Sattelzug von Rothe-Transporte'),
  ('from-original/semitrailer-1600.jpg', 'semitrailer.png', 'image/jpeg', 937, 337,
   'Semitrailer von Rothe-Transporte für Spezialtransporte'),
  ('from-original/megatrailer-1600.jpg', 'megatrailer.png', 'image/jpeg', 1147, 439,
   'Megatrailer von Rothe-Transporte für voluminöse Güter'),
  ('from-original/tiefbett-sattelanhaenger-1600.jpg', 'tiefbett-sattelanhaenger.png', 'image/jpeg', 1030, 357,
   'Tiefbett-Sattelanhänger von Rothe-Transporte'),
  ('from-original/citytrailer-alltagsheld-1600.jpg', 'citytrailer-alltagsheld.png', 'image/jpeg', 928, 403,
   'Citytrailer „Alltagsheld" von Rothe-Transporte');

-- ── "Motorwagen mit Ladekran" entfernen – existiert nicht ────────────────────

DELETE FROM vehicles WHERE slug = 'motorwagen-mit-ladekran';

-- ── 1. Tautliner-Sattelzug ────────────────────────────────────────────────────

UPDATE vehicles SET
  name             = 'Tautliner-Sattelzug',
  marketing_text   = 'Unser Tautliner ist unser Allrounder! Ideal für normalmaßige, verpackte und nicht temperaturempfindliche Ware. Die beidseitig öffnende Plane ermöglicht ein schnelles Be- und Entladen, das Edscha-Verdeck auch die Kranbeladung von oben.',
  length_m         = 13.60,
  width_m          = 2.48,
  height_m         = 2.70,
  payload_kg       = 25000,
  euro_pallets     = 33,
  axles            = 3,
  special_features = 'Edscha-Verdeck (Kranbeladung von oben möglich); beidseitig öffnende Schiebeplane',
  image_id         = (SELECT id FROM media WHERE filename = 'from-original/tautliner-sattelzug-1600.jpg'),
  sort_order       = 1,
  updated_at       = (strftime('%Y-%m-%d %H:%M:%S', 'now'))
WHERE slug = 'tautliner-sattelzug';

-- ── 2. Semitrailer (neu) ──────────────────────────────────────────────────────

INSERT INTO vehicles
  (slug, name, marketing_text, length_m, width_m, height_m, payload_kg, euro_pallets, axles, special_features, image_id, is_active, sort_order)
VALUES
  ('semitrailer', 'Semitrailer',
   'Unser Semitrailer ist der Spezialist für Ihren Spezialtransport! Gemacht für außergewöhnlich schwere, breite und große Ware wie Maschinen und Betonteile. Mit Radmulden eignet er sich auch für Fahrzeugtransporte.',
   13.60, 2.48, 3.25, 21000, 33, 3,
   'Hubdach; Radmulden für Fahrzeugtransporte; mit Genehmigung bis 3,50 m Höhe und 5,00 m Breite, optional bis 30 t Nutzlast',
   (SELECT id FROM media WHERE filename = 'from-original/semitrailer-1600.jpg'),
   1, 2);

-- ── 3. Megatrailer (neu) ──────────────────────────────────────────────────────

INSERT INTO vehicles
  (slug, name, marketing_text, length_m, width_m, height_m, payload_kg, euro_pallets, axles, special_features, image_id, is_active, sort_order)
VALUES
  ('megatrailer', 'Megatrailer',
   'Unser Megatrailer ist das Großraumwunder für sperrige und voluminöse Güter. Dank flexiblem Ladevolumen und Hubdach bringen wir auch große Sendungen sicher unter.',
   13.60, 2.48, 2.85, 25000, NULL, 3,
   'Hubdach mit Kranbeladung; mit Genehmigung bis 3,05 m Höhe und 4,00 m Breite',
   (SELECT id FROM media WHERE filename = 'from-original/megatrailer-1600.jpg'),
   1, 3);

-- ── 4. Tiefbett-Sattelanhänger (war "Tieflader-Sattelauflieger") ─────────────
-- Slug bleibt für URL-Stabilität erhalten, nur der Anzeigename wird angepasst.

UPDATE vehicles SET
  name             = 'Tiefbett-Sattelanhänger',
  marketing_text   = 'Unser Tiefbett-Sattelanhänger ist die Lösung für Ladung, die über das Standardmaß hinausgeht. Mit niedriger Ladehöhe und bis zu 28 Tonnen Nutzlast bringen wir auch sperrige und schwere Güter sicher ans Ziel.',
  length_m         = 13.60,
  width_m          = 2.48,
  height_m         = 3.70,
  payload_kg       = 28000,
  euro_pallets     = 33,
  axles            = 3,
  special_features = 'Niedrige Ladehöhe; Hubdach; mit Genehmigung bis 5,00 m Breite',
  image_id         = (SELECT id FROM media WHERE filename = 'from-original/tiefbett-sattelanhaenger-1600.jpg'),
  sort_order       = 4,
  updated_at       = (strftime('%Y-%m-%d %H:%M:%S', 'now'))
WHERE slug = 'tieflader-sattelauflieger';

-- ── 5. Citytrailer „Alltagsheld" (war "Anhänger Alltagsheld") ────────────────

UPDATE vehicles SET
  name             = 'Citytrailer „Alltagsheld"',
  marketing_text   = 'Unser Citytrailer ist der Alltagsheld – klein, aber fein! Ideal für kompakte, leichte und palettierte Güter, gerade im urbanen Bereich. Die integrierte Ladebordwand macht das Be- und Entladen auch ohne Rampe einfach.',
  length_m         = 9.00,
  width_m          = 2.48,
  height_m         = 2.70,
  payload_kg       = 15000,
  euro_pallets     = 22,
  axles            = 2,
  special_features = 'Hubdach; integrierte Ladebordwand (2,60 × 2,48 m, 3 t Tragkraft); Lenkachse für enge Anlieferungen',
  image_id         = (SELECT id FROM media WHERE filename = 'from-original/citytrailer-alltagsheld-1600.jpg'),
  sort_order       = 5,
  updated_at       = (strftime('%Y-%m-%d %H:%M:%S', 'now'))
WHERE slug = 'anhaenger-alltagsheld';

-- ── 301-Redirect für die alte (entfernte) Motorwagen-Detailseite ─────────────

INSERT INTO redirects (from_path, to_path, status_code) VALUES
  ('/fahrzeuge/motorwagen-mit-ladekran', '/fahrzeuge', 301);

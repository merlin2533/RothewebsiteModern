-- =============================================================================
-- 008_simpler_customer_texts.sql
-- Texte näher am Stil der originalen Rothe-Transporte-Webseite:
-- warm, partnerschaftlich, in einfacher Kundensprache. Ersetzt den knappen
-- "Industrial"-Telegrammstil aus 006/007 durch ganze Sätze und direkte
-- Ansprache. Technische Daten werden nicht angetastet.
-- =============================================================================

-- ── Settings ──────────────────────────────────────────────────────────────────

-- Adresse: ASCII-Platzhalter aus 002 auf richtige Umlaute heben.
UPDATE settings SET value = 'Walddorfhäslach' WHERE key = 'address_city';

UPDATE settings SET
  value = 'Wir bewegen Maschinen seit 1978 – mit eigenem Fuhrpark, festen Fahrern und einem persönlichen Ansprechpartner. Daran haben wir nichts geändert, und daran wird sich auch nichts ändern.'
WHERE key = 'owner_quote';

UPDATE settings SET
  value = 'Karl-Otto & Christopher Rothe'
WHERE key = 'owner_quote_attribution';

UPDATE settings SET
  value = 'Rothe-Transporte – Ihre Familienspedition für Maschinen- und Spezialtransporte aus Walddorfhäslach. Seit 1978 mit eigenem Fuhrpark. Rufen Sie uns an: 07127 18231.'
WHERE key = 'meta_default_description';

UPDATE settings SET
  value = 'Rothe-Transporte – Ihre Spedition für Maschinentransporte | Walddorfhäslach'
WHERE key = 'meta_default_title';

-- ── Pages ─────────────────────────────────────────────────────────────────────

-- Startseite: warm, einladend, einfach.
UPDATE pages SET
  meta_title       = 'Rothe-Transporte – Maschinen- und Spezialtransporte aus Walddorfhäslach',
  meta_description = 'Ihre Familienspedition seit 1978. Maschinentransporte, Tieflader bis 28 Tonnen, Lagerung in eigenen Hallen. Tagestouren in DE, AT, CH, FR, NL, BE. Wir melden uns persönlich: 07127 18231.',
  hero_headline    = 'Ihre Maschine in guten Händen.',
  hero_sub         = 'Seit 1978 transportieren wir Maschinen sicher und pünktlich quer durch Europa – als Familienbetrieb mit eigenem Fuhrpark und Fahrern, die ihr Handwerk verstehen.',
  content_html     = '<p>Willkommen bei Rothe-Transporte. Wir sind eine Familienspedition aus Walddorfhäslach und bewegen seit 1978 Maschinen, Anlagen und schwere Güter. Bei uns rufen Sie nicht in einem Callcenter an – Sie sprechen direkt mit jemandem, der Ihre Tour disponiert.</p><h2>Worauf Sie sich verlassen können</h2><p>Wir liefern, was wir zusagen. Ihre Maschine wird sorgfältig verladen, sicher gezurrt und pünktlich am Zielort übergeben. Den Preis nennen wir Ihnen direkt am Telefon – ohne Überraschungen auf der Rechnung.</p><p>Probieren Sie es einfach aus: Ein kurzer Anruf genügt, und Sie wissen, was Ihr Transport kostet und wann er fahren kann.</p>'
WHERE slug = 'home';

-- Über uns: persönlich, Geschichte erzählen.
UPDATE pages SET
  meta_title       = 'Über uns – Familienspedition seit 1978 | Rothe-Transporte',
  meta_description = 'Lernen Sie uns kennen: Karl-Otto und Christopher Rothe führen die Spedition in zweiter Generation. Aus dem Ein-Mann-Betrieb von 1978 wurde ein eingespieltes Team mit eigenem Fuhrpark.',
  hero_headline    = 'Familienbetrieb seit 1978.',
  hero_sub         = 'Aus einem Ein-Mann-Betrieb in Münsingen ist über zwei Generationen ein eingespieltes Team geworden – mit eigenem Fuhrpark und Fahrern, die seit Jahren bei uns sind.',
  content_html     = '<h2>Wie alles angefangen hat</h2><p>1978 hat Karl-Otto Rothe das Unternehmen in Münsingen gegründet – mit einem Lkw und viel Anpacken. 1985 sind wir nach Walddorfhäslach umgezogen, wo wir bis heute zu Hause sind. Was als Ein-Mann-Betrieb begann, ist heute eine spezialisierte Spedition für Maschinentransporte. Seit 2010 führt Christopher Rothe das Unternehmen mit – die zweite Generation.</p><h2>Was uns wichtig ist</h2><p>Wir machen Logistik aus Überzeugung, nicht im Akkord. Unsere Kunden begleiten uns oft über viele Jahre – und unsere Fahrer auch. Wir setzen auf eine feste Stamm-Mannschaft, weil Vertrauen Zeit braucht. Seit 2018 bilden wir auch selbst Berufskraftfahrer aus, weil wir an die nächste Generation glauben.</p>'
WHERE slug = 'ueber-uns';

-- Leistungen: klare, kundenfreundliche Übersicht.
UPDATE pages SET
  meta_title       = 'Leistungen – Maschinentransport, Tieflader, Lagerung | Rothe-Transporte',
  meta_description = 'Maschinentransporte, Spezialtransporte, Lagerung in eigenen Hallen und Stapler-Service vor Ort – alles aus einer Hand. Wir koordinieren Ihren Auftrag persönlich, ohne Subunternehmer-Kette.',
  hero_headline    = 'Alles aus einer Hand.',
  hero_sub         = 'Maschinentransport, Spezialtransport, Lagerung und Stapler-Service – Sie haben einen festen Ansprechpartner für alles.',
  content_html     = '<p>Wir machen vier Dinge, und die machen wir gut. Mit eigenen Fahrzeugen, eigenen Fahrern und einer eigenen Lagerhalle direkt am Standort. So bleibt die Verantwortung für Ihren Auftrag bei uns – vom ersten Anruf bis zur Übergabe beim Empfänger.</p><p>Sie haben eine Frage zu Ihrer Sendung? Rufen Sie an. Wir wissen, wo Ihr Lkw gerade ist.</p>'
WHERE slug = 'leistungen';

-- Fahrzeuge: einfach erklärt, ohne Disponenten-Jargon.
UPDATE pages SET
  meta_title       = 'Unser Fuhrpark – Tieflader, Tautliner, Anhänger, Ladekran | Rothe-Transporte',
  meta_description = 'Tieflader bis 28 Tonnen, Tautliner-Sattelzug für palettierte Ware, kompakter Anhänger mit Auffahrrampe und Motorwagen mit Ladekran. Für jede Maschine das passende Fahrzeug.',
  hero_headline    = 'Für jede Aufgabe das passende Fahrzeug.',
  hero_sub         = 'Vier Fahrzeuge – jedes mit seinem eigenen Einsatzbereich. Vom 28-Tonnen-Tieflader bis zum Motorwagen mit Ladekran für enge Baustellen.',
  content_html     = '<p>Unser Fuhrpark ist konsequent auf Maschinen- und Spezialtransporte ausgelegt. Jedes Fahrzeug hat seinen Zweck, und jeder Fahrer hat seinen festen Lkw – das hat sich über die Jahre einfach bewährt.</p><p>Sie sind unsicher, welches Fahrzeug für Ihre Maschine passt? Schildern Sie uns kurz Maße und Gewicht – wir sagen Ihnen verbindlich, was geht.</p>'
WHERE slug = 'fahrzeuge';

-- Karriere: warm, einladend, ehrlich.
UPDATE pages SET
  meta_title       = 'Karriere – Berufskraftfahrer (m/w/d) gesucht | Rothe-Transporte',
  meta_description = 'Wir suchen Berufskraftfahrer für Maschinentransporte in DE, AT, CH, FR, NL und BE. Familienbetrieb mit modernem Fuhrpark, fairer Bezahlung und festem Lkw für jeden Fahrer.',
  hero_headline    = 'Werden Sie Teil unseres Teams.',
  hero_sub         = 'Wir suchen Berufskraftfahrer (Klasse CE), die ihren Beruf ernst nehmen und gerne unterwegs sind. Bei uns sind Sie kein Personal-Posten, sondern ein Kollege.',
  content_html     = '<h2>Was Sie bei uns erwartet</h2><ul><li>Ein moderner, eigener Fuhrpark – jeder Fahrer hat seinen festen Lkw.</li><li>Faire und pünktliche Bezahlung – ohne Wenn und Aber.</li><li>Kurze Wege: Wir entscheiden im Familienbetrieb, nicht in der Konzernzentrale.</li><li>Ein sicherer Arbeitsplatz mit Perspektive – seit 1978.</li><li>Eine feste Stamm-Mannschaft. Kein Leiharbeiter-Karussell.</li></ul><h2>So bewerben Sie sich</h2><p>Schicken Sie uns ein paar Zeilen per E-Mail an <a href="mailto:info@rothe-transporte.de">info@rothe-transporte.de</a> oder einen Brief an: Rothe-Transporte und Speditions GbR, Aeckerlesweg 3, 72141 Walddorfhäslach. Wir melden uns kurzfristig bei Ihnen.</p>'
WHERE slug = 'karriere';

-- Kontakt: kurz, freundlich, ohne Floskel.
UPDATE pages SET
  meta_title       = 'Kontakt – Rothe-Transporte Walddorfhäslach | 07127 18231',
  meta_description = 'So erreichen Sie uns: Telefon 07127 18231, E-Mail info@rothe-transporte.de oder direkt am Aeckerlesweg 3 in Walddorfhäslach. Mo–Fr 07:00–17:00 Uhr.',
  hero_headline    = 'Sprechen wir miteinander.',
  hero_sub         = 'Am liebsten klären wir Ihren Transport direkt am Telefon – das geht am schnellsten und ist am persönlichsten.',
  content_html     = '<p>Sie erreichen uns Mo–Fr von 7 bis 17 Uhr unter <a href="tel:+497127182310">07127 18231</a>. Eine kurze Schilderung Ihrer Sendung (Maße, Gewicht, Start und Ziel) genügt – Sie bekommen einen verbindlichen Festpreis direkt am Telefon.</p><p>Lieber schreiben? Schicken Sie uns eine E-Mail an <a href="mailto:info@rothe-transporte.de">info@rothe-transporte.de</a>. Wir antworten in der Regel am selben oder am nächsten Werktag.</p>'
WHERE slug = 'kontakt';

-- 404: freundlich, mit klarem Ausweg.
UPDATE pages SET
  hero_headline = 'Hier sind wir falsch abgebogen.',
  hero_sub      = 'Die Seite, die Sie suchen, gibt es nicht (mehr). Aber unsere Disposition findet sicher den richtigen Weg.',
  content_html  = '<p>Vielleicht hilft Ihnen die Navigation oben weiter. Oder Sie rufen uns einfach an: <a href="tel:+497127182310">07127 18231</a>. Wir helfen gerne.</p>'
WHERE slug = '404';

-- ── Vehicles (technische Daten unangetastet, nur marketing_text) ──────────

UPDATE vehicles SET
  marketing_text = 'Unser Tieflader ist der Spezialist für alles, was hoch, sperrig oder besonders schwer ist. Durch die niedrige Ladehöhe passen auch Maschinen darauf, die auf andere Lkw nicht passen. Bis zu 28 Tonnen Nutzlast und Platz für 33 Europaletten.'
WHERE slug = 'tieflader-sattelauflieger';

UPDATE vehicles SET
  marketing_text = 'Der Tautliner ist unser Allrounder für palettierte und verpackte Ware. Die Schiebeplane lässt sich von beiden Seiten öffnen – das spart Zeit beim Be- und Entladen. 25 Tonnen Nutzlast, 33 Europaletten, wettergeschützt.'
WHERE slug = 'tautliner-sattelzug';

UPDATE vehicles SET
  marketing_text = 'Unser Anhänger ist klein und wendig – und damit ideal für kompakte Sendungen und enge Höfe. Dank Auffahrrampe können wir Ihre Ware ebenerdig verladen, dank Lenkachse kommen wir auch in schwierige Anlieferungen.'
WHERE slug = 'anhaenger-alltagsheld';

UPDATE vehicles SET
  marketing_text = 'Wenn am Zielort kein Stapler vor Ort ist: Unser Motorwagen hat seinen eigenen Ladekran an Bord und setzt Ihre Maschine punktgenau ab – auch auf engen Baustellen oder mitten in der Innenstadt. Die Lenkachse macht ihn dabei besonders wendig.'
WHERE slug = 'motorwagen-mit-ladekran';

-- ── Services ──────────────────────────────────────────────────────────────

UPDATE services SET
  summary   = 'Werkzeugmaschinen, Anlagen, schwere Industrieteile – wir transportieren sie sicher zum Ziel, mit dem passenden Fahrzeug und Fahrern, die wissen, worauf es ankommt.',
  body_html = '<p>Maschinentransporte sind unser Kerngeschäft – seit Jahrzehnten. Wir holen Ihre Werkzeugmaschine, Ihre Anlage oder Ihr Industrieteil ab, sichern es nach den Vorschriften der VDI 2700 und bringen es pünktlich an den Zielort. Unsere Fahrer haben das schon hundertfach gemacht.</p><p>Erzählen Sie uns einfach am Telefon, worum es geht – wir kümmern uns um den Rest.</p>'
WHERE slug = 'maschinentransporte';

UPDATE services SET
  summary   = 'Sperrig, voluminös oder einfach zu hoch für einen normalen Lkw? Mit unserem Tieflader bekommen wir auch das transportiert.',
  body_html = '<p>Manchmal passt eine Maschine einfach nicht auf einen Standard-Lkw. Dann kommt unser Tieflader-Sattelauflieger zum Einsatz: niedrige Ladehöhe, 28 Tonnen Nutzlast, flexibles Ladevolumen. Sie nennen uns Maße und Gewicht, wir sagen Ihnen verbindlich, ob es geht und was es kostet – direkt am Telefon.</p>'
WHERE slug = 'spezialtransporte';

UPDATE services SET
  summary   = 'Wir lagern Ihre Maschinen, Großteile oder Paletten in unseren eigenen Hallen – mit kurzen Wegen zum Fuhrpark.',
  body_html = '<p>Brauchen Sie Platz für Ihre Ware? Wir lagern Großteile, Regalware und palettierte Industriegüter in unseren eigenen Hallen direkt am Firmensitz. Be- und Entladung übernehmen wir mit unseren eigenen Staplern bis 3,5 Tonnen. Wenn Sie abrufen, steht Ihre Sendung bereit.</p>'
WHERE slug = 'lagerung';

UPDATE services SET
  summary   = 'Kein Stapler am Lade- oder Entladeort? Kein Problem – wir bringen unseren eigenen mit.',
  body_html = '<p>Wenn beim Be- oder Entladen kein Stapler vor Ort ist, kommen wir mit eigenem Gerät bis 3,5 Tonnen. Das spart Ihnen Stress, Zeit und Koordination mit Subunternehmern. Sagen Sie kurz Bescheid, wir richten es ein.</p>'
WHERE slug = 'stapler-entladedienst';

-- ── FAQs – Antworten in einfacher Kundensprache ───────────────────────────────

UPDATE faqs SET
  answer_html = '<p>Vier Fahrzeuge für unterschiedliche Aufgaben:</p><ul><li><strong>Tieflader-Sattelauflieger</strong> – 28 t Nutzlast, 13,60 × 2,48 × 3,70 m, 33 Europaletten. Für hohe und sperrige Maschinen.</li><li><strong>Tautliner-Sattelzug</strong> – 25 t Nutzlast, 13,60 × 2,48 × 2,70 m, 33 Europaletten. Für palettierte und verpackte Ware.</li><li><strong>Anhänger</strong> mit integrierter Auffahrrampe und Lenkachse – für kompakte Sendungen und enge Höfe.</li><li><strong>Motorwagen mit Ladekran</strong> und Lenkachse – für Baustellen und Innenstadt-Anlieferungen ohne Stapler.</li></ul>'
WHERE question LIKE 'Welche Fahrzeuge%';

UPDATE faqs SET
  question    = 'In welche Länder fahren Sie regelmäßig?',
  answer_html = '<p>Wir fahren regelmäßig nach Deutschland, Österreich, in die Schweiz, nach Frankreich, in die Niederlande und nach Belgien. Tagesfahrten innerhalb von Süddeutschland sind bei uns Alltag – auch kurzfristig.</p>'
WHERE question LIKE 'In welche L%';

UPDATE faqs SET
  answer_html = '<p>Das hängt davon ab, wie lang die Strecke ist, was die Maschine wiegt, welche Maße sie hat und welches Fahrzeug wir brauchen. Rufen Sie uns einfach an: <a href="tel:+497127182310">07127 18231</a>. Mit Abhol- und Zielort, Maßen und Gewicht können wir Ihnen direkt am Telefon einen verbindlichen Festpreis nennen – ohne Überraschungen auf der Rechnung.</p>'
WHERE question LIKE 'Was kostet%';

UPDATE faqs SET
  answer_html = '<p>Ja, gerne. Wir lagern Großteile, Regalware und palettierte Industriegüter in unseren eigenen Hallen direkt am Firmensitz. Be- und Entladung übernehmen wir mit eigenen Staplern bis 3,5 Tonnen – Sie müssen sich um nichts kümmern.</p>'
WHERE question LIKE 'Bieten Sie auch Lagerung%';

UPDATE faqs SET
  answer_html = '<p>Ja, wir sind ein Familienbetrieb. Karl-Otto Rothe hat das Unternehmen 1978 in Münsingen gegründet, seit 2010 führt sein Sohn Christopher Rothe die Spedition mit. Unser Sitz ist seit 1985 Walddorfhäslach im Landkreis Reutlingen – da finden Sie uns auch heute noch.</p>'
WHERE question LIKE 'Sind Sie ein Familienunternehmen%';

UPDATE faqs SET
  answer_html = '<p>Am einfachsten geht es per Telefon: <a href="tel:+497127182310">07127 18231</a>. Wir brauchen drei Dinge von Ihnen: wo wir abholen, wohin es geht, und wann es soweit sein soll. Maße und Gewicht der Sendung helfen uns, das passende Fahrzeug zu wählen. Lieber schreiben? E-Mail an <a href="mailto:info@rothe-transporte.de">info@rothe-transporte.de</a> – wir melden uns spätestens am nächsten Werktag.</p>'
WHERE question LIKE 'Wie buche ich%';

-- ── Timeline – ganz leichte Sprache ──────────────────────────────────────────

UPDATE timeline_events SET
  title       = 'Gründung in Münsingen',
  description = 'Karl-Otto Rothe gründet das Unternehmen als Ein-Mann-Betrieb – mit einem Lkw und viel Anpacken.'
WHERE year = 1978;

UPDATE timeline_events SET
  title       = 'Umzug nach Walddorfhäslach',
  description = 'Wir ziehen in den Landkreis Reutlingen um – an den Standort, an dem wir bis heute zu Hause sind.'
WHERE year = 1985;

UPDATE timeline_events SET
  title       = 'Mehr Fahrzeuge',
  description = 'Mit wachsender Nachfrage wächst auch unser Fuhrpark – die ersten zusätzlichen Lkw kommen dazu.'
WHERE year = 1991;

UPDATE timeline_events SET
  title       = 'Spezialisierung Maschinentransport',
  description = 'Wir konzentrieren uns auf das, was wir am besten können: Maschinen- und Anlagentransporte.'
WHERE year = 2005;

UPDATE timeline_events SET
  title       = 'Zweite Generation',
  description = 'Christopher Rothe steigt ein – die nächste Generation übernimmt Verantwortung mit.'
WHERE year = 2010;

UPDATE timeline_events SET
  title       = 'Neue Spezialfahrzeuge',
  description = 'Tieflader-Sattelauflieger und Spezialanhänger erweitern unsere Möglichkeiten für sperrige Sendungen.'
WHERE year = 2015;

UPDATE timeline_events SET
  title       = 'Ausbildungsbetrieb',
  description = 'Wir bilden jetzt selbst Berufskraftfahrer aus – weil wir an die nächste Generation glauben.'
WHERE year = 2018;

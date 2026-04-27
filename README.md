# Rothe-Transporte – Website

Moderne PHP-Webseite fuer **Rothe-Transporte und Speditions GbR** (Walddorfhaeslach) – Familienbetrieb fuer Maschinen- und Spezialtransporte seit 1978.

Stack: PHP 8.2+ · SQLite · selbst gehostete Fonts · kein Build-Schritt · kein Tracker.

## Schnellstart

### Auf einen Webserver deployen

➡️ **Komplette Deployment-Anleitung: [DEPLOY.md](DEPLOY.md)** – Repo hochladen, DocumentRoot auf `public/` zeigen, dann **`https://deine-domain.de/install.php`** aufrufen. Der Web-Installer prüft alles, schreibt `.env`, baut die DB auf, generiert Favicons und sperrt sich danach selbst.

### Lokal entwickeln

Voraussetzungen: PHP 8.2+ mit `pdo_sqlite`, `fileinfo`, `mbstring`, `session`, `gd`.

```bash
git clone <repo>
cd RothewebsiteModern
cp .env.example .env                  # ggf. anpassen
php scripts/migrate.php               # legt DB an + fuellt Inhalte
php -S 127.0.0.1:8000 -t public/      # Dev-Server
```

Browser: <http://127.0.0.1:8000>

Admin: <http://127.0.0.1:8000/admin/login>
- Default-User: `admin`
- Default-Passwort: `ChangeMe!2026` ← **nach erstem Login unter `/admin/account` aendern!** Mindestens 12 Zeichen, Gross-/Kleinbuchstaben, Ziffer.

## Verzeichnisse

```
public/      Webroot (DocumentRoot des Hosters hierauf zeigen)
src/         PHP-Code (Controllers, Repositories, Views, Migrations, Core)
data/        SQLite-DB, Sessions, Logs (nicht oeffentlich, .htaccess sperrt)
uploads/     Vom Admin hochgeladene Bilder (Symlink public/uploads -> ../uploads)
scripts/     CLI-Tools (migrate, seed, mirror_assets, generate_placeholders)
```

## Inhalte pflegen

Alle Inhalte (Texte, Fahrzeuge, Leistungen, Zeitstrahl, Bilder, SEO-Meta, Kontaktdaten) sind im Admin-Bereich `/admin` editierbar.

> **Achtung:** Die technischen Daten der Fahrzeuge (Maße, Nutzlast, Paletten, Achsen) stammen aus den Originaldaten und sollten nur in Ruecksprache mit dem Geschaeftsfuehrer geaendert werden.

## Originalbilder spiegeln (optional)

```bash
bash scripts/mirror_assets.sh
```

Versucht, Bilder von rothe-transporte.de zu spiegeln. Falls die Site nicht erreichbar ist, werden SVG-Platzhalter aus `public/assets/images/placeholders/` verwendet. Echte Fotos koennen jederzeit ueber den Admin hochgeladen und Bildern zugeordnet werden.

## Deployment

Hoster muss bieten: PHP 8.2+, `mod_rewrite` (Apache) bzw. Rewrite-Regeln (Nginx), Schreibrechte auf `data/` und `uploads/`.

1. DocumentRoot auf `public/` zeigen lassen.
2. Repo deployen, `cp .env.example .env` und Werte setzen (`SITE_URL`, `APP_ENV=production`).
3. `php scripts/migrate.php` einmalig ausfuehren.
4. In `public/.htaccess` den `Strict-Transport-Security`- und HTTPS-Redirect-Block aktivieren.
5. Admin-Passwort aendern.
6. Sitemap bei Google Search Console einreichen: `https://<domain>/sitemap.xml`.

### Nginx-Aequivalent (statt .htaccess)

```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
location ~ /\. { deny all; }
location /uploads/ {
    location ~ \.(php|phtml|phar)$ { deny all; }
}
```

## Sicherheit

- Passwoerter mit `password_hash(PASSWORD_DEFAULT)`.
- CSRF-Token auf allen POST-Formularen.
- Brute-Force-Schutz: 5 Fehlversuche / 15 min / IP.
- Sessions HttpOnly + SameSite=Strict + (auto) Secure bei HTTPS.
- Upload-Whitelist (jpeg/png/webp/svg/gif), Mime-Sniffing, max 8 MB, randomisierter Dateiname, PHP-Ausfuehrung in `uploads/` deaktiviert.
- HTML-Editor sanitiert via Tag-Whitelist.
- CSP, X-Content-Type-Options, Referrer-Policy in `.htaccess`.

## Backup

`scripts/backup.sh` erzeugt einen konsistenten SQLite-Snapshot (via `.backup`)
plus alle Uploads in einem datierten `tar.gz` unter `backups/`:

```bash
bash scripts/backup.sh
# → backups/rothe-YYYYMMDDTHHMMSSZ.tar.gz
```

Cron-Beispiel (taeglich 03:15, 14 Tage Aufbewahrung):

```cron
15 3 * * * cd /var/www/rothe && bash scripts/backup.sh && \
           find backups -name '*.tar.gz' -mtime +14 -delete
```

## SEO / Pruefung

- Sitemap dynamisch: `/sitemap.xml`
- Robots: `/robots.txt`
- Strukturierte Daten testen: <https://search.google.com/test/rich-results>
- Lighthouse: Performance ≥ 95, A11y ≥ 95, SEO 100, BP 100 (Ziel)
- Meta-Title 50-60 Zeichen, Meta-Description 140-160 Zeichen pro Seite (im Admin pflegbar)
- NAP-Konsistenz (Name/Address/Phone) ist durch zentrale `settings`-Tabelle automatisch gegeben

## Lizenz

Inhalte (Texte, Bilder): © Rothe-Transporte und Speditions GbR. Source-Code: privat.

# Deployment-Anleitung: Rothe-Transporte

Diese Anleitung führt dich vom leeren Hosting-Account bis zur fertigen, indexierten Website. Geplante Dauer: **15–25 Minuten**.

## Voraussetzungen beim Hoster

| Anforderung | Was prüfen? |
|---|---|
| **PHP 8.2+** | im Hosting-Panel auf 8.2 / 8.3 / 8.4 stellen |
| **Erweiterungen** | `pdo_sqlite`, `mbstring`, `fileinfo`, `gd`, `session`, `openssl` (alle bei Standardpaketen vorhanden) |
| **Apache mit `mod_rewrite`** *oder* **Nginx mit Rewrite-Regeln** | für Saubere URLs |
| **DocumentRoot** auf das Verzeichnis **`public/`** zeigen lassen | wichtig für Sicherheit – `data/`, `src/`, `uploads/` dürfen NICHT öffentlich erreichbar sein |
| **HTTPS-Zertifikat** | Let's Encrypt, im Panel mit einem Klick |

> **Falls dein Hoster den DocumentRoot nicht ändern lässt**, bleibt das Repo wie es ist – Apache fängt sensible Pfade über `.htaccess` zusätzlich ab. Nginx-User: siehe Abschnitt unten.

## Schritt 1 — Dateien hochladen

Lade den **kompletten Repo-Inhalt** auf den Server. Per Zip:

```bash
git archive --format=zip --output rothe.zip claude/rebuild-transport-website-php-83JnU
```

Per FTP/SFTP/Plesk/cPanel hochladen, Zielverzeichnis z. B. `/var/www/rothe/`. Strukturansicht nach Upload:

```
/var/www/rothe/
├── public/         ← DocumentRoot zeigt HIERHER
├── src/
├── scripts/
├── uploads/
├── data/
├── README.md
└── DEPLOY.md
```

## Schritt 2 — DocumentRoot setzen

### Apache (Plesk/cPanel/eigener Server)

Die Domain im Panel auf `/var/www/rothe/public` zeigen lassen. Apache liest dann automatisch die mitgelieferte `public/.htaccess`.

### Nginx (eigener Server)

Server-Block:

```nginx
server {
    listen 443 ssl http2;
    server_name rothe-transporte.de www.rothe-transporte.de;
    root /var/www/rothe/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include fastcgi_params;
        fastcgi_pass unix:/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
    }

    location /uploads/ {
        location ~ \.(php|phtml|phar)$ { deny all; }
    }

    location ~ /\. { deny all; }
    location ~ /(data|src|scripts|backups)/ { deny all; }
}
```

## Schritt 3 — Schreibrechte setzen

Per SSH:

```bash
cd /var/www/rothe
chmod -R 750 data uploads
chown -R www-data:www-data data uploads        # Debian/Ubuntu/Apache
# oder: chown -R apache:apache data uploads    # CentOS/RHEL
```

Ohne SSH (FTP-Panel): bei `data/` und `uploads/` jeweils Schreibrechte für PHP setzen – häufig CHMOD `755` reicht beim Shared-Hosting.

## Schritt 4 — Installer aufrufen

Im Browser:

```
https://deine-domain.de/install.php
```

Du bekommst zuerst eine **Token-Aufforderung**. Hole dir das Token aus der Datei `data/install_token.txt` (per SSH `cat data/install_token.txt` oder per FTP herunterladen) und rufe dann auf:

```
https://deine-domain.de/install.php?t=<token>
```

Der Installer führt dich durch:

1. **System-Check** (PHP-Version, Erweiterungen, Schreibrechte)
2. **Konfiguration** (Site-URL, Admin-Login, Kontaktdaten)
3. **Installation** – legt SQLite-DB an, führt Migrationen aus, generiert Favicons + Platzhalter, setzt das Admin-Passwort, erstellt Lock-Datei

Der Installer **sperrt sich danach automatisch** über `data/installed.lock`. Lösche `public/install.php` anschliessend vom Server, falls du auf Nummer sicher gehen willst.

## Schritt 5 — Im Admin nachpflegen

```
https://deine-domain.de/admin/login
```

Login mit den Daten aus dem Installer. Pflichtaufgaben:

1. **Fahrzeug-Specs ergänzen** – `/admin/vehicles/3/edit` (Anhänger Alltagsheld) und `/admin/vehicles/4/edit` (Motorwagen mit Ladekran) brauchen noch Maße + Nutzlast.
2. **Echte Bilder hochladen** – `/admin/media`, dann unter `/admin/vehicles/<id>/edit` als `image_id` zuweisen, Hero-Bild in `/admin/settings → SEO Defaults`.
3. **Settings prüfen** – `/admin/settings`, vor allem die Adresse, Telefon, E-Mail, USt-IdNr.

## Schritt 6 — HTTPS und Security härten

In `public/.htaccess` die beiden auskommentierten Blöcke aktivieren:

```apache
# HTTPS-Redirect
RewriteCond %{HTTPS} !=on
RewriteRule ^ https://%{HTTP_HOST}%{REQUEST_URI} [R=301,L]

# Strict-Transport-Security (erst nach erfolgreichem HTTPS-Test setzen!)
Header set Strict-Transport-Security "max-age=31536000; includeSubDomains; preload"
```

## Schritt 7 — Suchmaschinen anmelden

| Tool | URL einreichen |
|---|---|
| **Google Search Console** | https://search.google.com/search-console |
| **Bing Webmaster Tools** | https://www.bing.com/webmasters |

Für beide:
- Property hinzufügen (Domain-Verifizierung per DNS-TXT-Record)
- Sitemap einreichen: `https://deine-domain.de/sitemap.xml`
- Bilder-Sitemap einreichen: `https://deine-domain.de/sitemap-images.xml`

## Schritt 8 (optional) — Tracking aktivieren

Erst nach Anpassung der **Datenschutzerklärung** ins Admin gehen: `/admin/settings → Conversion-Tracking`.

| Tool | Setting |
|---|---|
| **Plausible** (cookie-los, DSGVO-freundlich, empfohlen) | `plausible_domain` = `rothe-transporte.de` |
| **Google Tag Manager** | `gtm_container_id` = `GTM-XXXXXX` |
| **Matomo (Self-Hosted)** | `matomo_url` + `matomo_site_id` |
| **Server-Side GA4** | `ga4_measurement_id` + `ga4_api_secret` |
| **Meta CAPI** | `meta_pixel_id` + `meta_capi_token` |

Sobald _ein_ Tracking-Setting befüllt ist, erscheint beim ersten Besucher der Cookie-Banner mit drei Kategorien (Notwendig / Statistik / Marketing). Tracking läuft erst nach Zustimmung.

## Schritt 9 — Backup einrichten

```bash
crontab -e
```

```cron
# Tägliches Backup um 03:15 (SQLite + uploads in tar.gz, 14 Tage Aufbewahrung)
15 3 * * * cd /var/www/rothe && bash scripts/backup.sh && find backups -name '*.tar.gz' -mtime +14 -delete
```

## Troubleshooting

| Symptom | Ursache | Fix |
|---|---|---|
| Installer zeigt _„Falsches Token"_ | Token wurde regeneriert oder ist >1 h alt | `cat data/install_token.txt` und Link erneut aufrufen |
| 403 / 500 nach Aufruf der Startseite | Schreibrechte `data/` oder `uploads/` fehlen | siehe Schritt 3 |
| Bilder werden nicht angezeigt | Symlink `public/uploads → ../uploads` fehlt | per SSH `cd public && ln -s ../uploads uploads` |
| Installer kann nicht erneut laufen | Lock-Datei `data/installed.lock` schützt | manuell löschen, dann neu starten |
| FAQs / Landingpages 404 | Migration nicht ausgeführt | `php scripts/migrate.php` (SSH) oder Installer erneut |
| GTM/Plausible/Matomo lädt nicht | Consent-Banner nicht akzeptiert oder Setting leer | im Browser-DevTools: `document.cookie` muss `rt_consent_v2=` enthalten |

## Was der Installer NICHT macht

1. **HTTPS-Zertifikat** – musst du im Hosting-Panel einrichten
2. **DNS-Records** – musst du beim Domain-Registrar setzen
3. **Cookie-Banner-Anpassung an deine konkreten Tracker** – Standardfall reicht für Plausible cookieless; für GTM mit Meta-Pixel sollte ein DSGVO-Anwalt drüberschauen
4. **Echte Bilder + die zwei fehlenden Fahrzeug-Specs** – nur du selbst

Fertig. Bei Problemen: siehe README für die Architektur, Audit-Log unter `/admin/audit` für jede Admin-Aktion.

<?php
/**
 * install.php – Web-Installer fuer Rothe-Transporte
 * ============================================================================
 *
 * Schritte:
 *   1.  System-Check (PHP-Version, Erweiterungen, Schreibrechte)
 *   2.  Konfiguration (SITE_URL, Admin-Passwort, optional Tracking)
 *   3.  Schreibt .env, baut DB auf, fuehrt Migrationen aus
 *   4.  Erzeugt Favicons + SVG-Platzhalter
 *   5.  Legt /uploads-Symlink unter /public/uploads an
 *   6.  Setzt das Admin-Passwort + sperrt den Installer per Lock-Datei
 *
 * Sicherheit:
 *   - WICHTIG: Diese Datei MUSS nach erfolgreicher Installation vom
 *     Server geloescht werden – ein erneuter Aufruf zeigt zwar nur einen
 *     Hinweis, aber sicherer ist die Loeschung.
 *   - Wenn data/installed.lock existiert, ist der Installer gesperrt.
 */

declare(strict_types=1);

ini_set('display_errors', '1');
error_reporting(E_ALL);

$baseDir   = dirname(__DIR__);
$dataDir   = $baseDir . '/data';
$lockFile  = $dataDir . '/installed.lock';

@mkdir($dataDir, 0750, true);

// ── 0) Lock-Datei? Installer gesperrt ──────────────────────────────────────
if (is_file($lockFile)) {
    http_response_code(410);
    render_layout('Installer gesperrt', '
        <div class="alert alert--ok">
          <h2>Bereits installiert</h2>
          <p>Diese Website wurde am <code>' . htmlspecialchars((string) file_get_contents($lockFile)) . '</code> initialisiert.</p>
          <p>Loeschen Sie <code>data/installed.lock</code> auf dem Server, falls Sie den Installer erneut nutzen wollen.<br>
          Empfohlen: <code>public/install.php</code> jetzt vom Server entfernen.</p>
          <p><a href="/">→ Zur Webseite</a> · <a href="/admin/login">→ Admin-Login</a></p>
        </div>
    ');
    exit;
}

// ── 2) Helpers ─────────────────────────────────────────────────────────────
function check(string $label, bool $ok, string $hint = ''): array
{
    return ['label' => $label, 'ok' => $ok, 'hint' => $hint];
}

function render_layout(string $title, string $bodyHtml): void
{
    echo '<!doctype html><html lang="de"><head><meta charset="utf-8">';
    echo '<meta name="viewport" content="width=device-width,initial-scale=1">';
    echo '<title>' . htmlspecialchars($title) . ' – Rothe-Transporte Installer</title>';
    echo '<meta name="robots" content="noindex,nofollow">';
    echo '<style>
        :root{--c-primary:#0B2545;--c-deep:#050F22;--c-accent:#C2410C;--c-line:#E3E8EF;--c-bg:#F4F2EE;--c-ok:#16a34a;--c-err:#b91c1c}
        *{box-sizing:border-box}body{margin:0;background:var(--c-bg);color:#0F1419;font-family:system-ui,-apple-system,Segoe UI,sans-serif;line-height:1.6}
        .wrap{max-width:780px;margin:3rem auto;padding:0 1.5rem}
        .card{background:#fff;border:1px solid var(--c-line);border-radius:12px;padding:2rem;margin-bottom:2rem;box-shadow:0 16px 36px -24px rgba(11,37,69,.2)}
        h1{font-size:2rem;color:var(--c-primary);margin-top:0}
        h2{color:var(--c-primary);margin-top:1.5rem}
        a{color:var(--c-accent)}
        code,pre{font-family:ui-monospace,SFMono-Regular,Menlo,monospace;font-size:.92em}
        pre{background:var(--c-deep);color:#fff;padding:1rem;border-radius:8px;overflow-x:auto}
        label{display:block;font-weight:600;margin-bottom:.4rem;margin-top:1rem;color:#2B3340}
        input[type=text],input[type=password],input[type=email]{width:100%;padding:.7rem .9rem;border:1px solid var(--c-line);border-radius:6px;font:inherit}
        button{background:var(--c-accent);color:#fff;border:0;padding:.85rem 1.6rem;border-radius:6px;cursor:pointer;font-weight:600;font-size:1rem}
        button:hover{filter:brightness(1.08)}
        ul.checks{list-style:none;padding:0;margin:0}
        ul.checks li{padding:.5rem .8rem;border-radius:6px;margin-bottom:.4rem;display:flex;align-items:start;gap:.7rem}
        ul.checks li.ok{background:#e8f7ee;color:#155e2c}
        ul.checks li.err{background:#fde8e8;color:#7c1818}
        ul.checks li::before{font-weight:700}
        ul.checks li.ok::before{content:"\2714";color:var(--c-ok)}
        ul.checks li.err::before{content:"\2716";color:var(--c-err)}
        .alert{padding:1rem 1.2rem;border-radius:8px}
        .alert--ok{background:#e8f7ee;color:#155e2c;border-left:4px solid var(--c-ok)}
        .alert--err{background:#fde8e8;color:#7c1818;border-left:4px solid var(--c-err)}
        .small{font-size:.9rem;color:#6B7785}
        .step{font-size:.78rem;letter-spacing:.18em;text-transform:uppercase;color:var(--c-accent);font-weight:700;margin-bottom:.5rem}
    </style></head><body><div class="wrap"><div class="card">';
    echo $bodyHtml;
    echo '</div></div></body></html>';
}

// ── 3) System-Check ─────────────────────────────────────────────────────────
$checks = [
    check('PHP ≥ 8.2',                 PHP_VERSION_ID >= 80200, 'Aktuell: ' . PHP_VERSION),
    check('Extension: pdo_sqlite',     extension_loaded('pdo_sqlite')),
    check('Extension: mbstring',       extension_loaded('mbstring')),
    check('Extension: fileinfo',       extension_loaded('fileinfo')),
    check('Extension: gd',             extension_loaded('gd')),
    check('Extension: session',        extension_loaded('session')),
    check('Extension: openssl',        extension_loaded('openssl')),
    check('Verzeichnis data/ schreibbar',    is_writable($dataDir)),
    check('Verzeichnis uploads/ schreibbar', is_writable($baseDir . '/uploads')),
    check('public/index.php vorhanden',      is_file($baseDir . '/public/index.php')),
    check('src/Migrations/ enthält *.sql',   count(glob($baseDir . '/src/Migrations/*.sql') ?: []) > 0),
];
$systemOk = !in_array(false, array_column($checks, 'ok'), true);

// ── 4) Schritt-Steuerung ───────────────────────────────────────────────────
$step  = (string) ($_GET['step'] ?? 'check');
$post  = $_SERVER['REQUEST_METHOD'] === 'POST';

if ($step === 'check' && !$systemOk) {
    $li = '';
    foreach ($checks as $c) {
        $cls  = $c['ok'] ? 'ok' : 'err';
        $hint = $c['hint'] !== '' ? ' <small class="small">' . htmlspecialchars($c['hint']) . '</small>' : '';
        $li .= "<li class=\"$cls\">" . htmlspecialchars($c['label']) . $hint . "</li>";
    }
    render_layout('System-Check', '
        <p class="step">Schritt 1 von 3</p>
        <h1>System-Check fehlgeschlagen</h1>
        <ul class="checks">' . $li . '</ul>
        <p class="small">Sobald alle Punkte gruen sind, laden Sie diese Seite neu.</p>
        <p><a href="/install.php">↻ Erneut pruefen</a></p>
    ');
    exit;
}

if ($step === 'check' && $systemOk) {
    $li = '';
    foreach ($checks as $c) {
        $li .= '<li class="ok">' . htmlspecialchars($c['label']) . '</li>';
    }
    $defaultUrl = (($_SERVER['HTTPS'] ?? '') === 'on' ? 'https' : 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost');
    render_layout('Konfiguration', '
        <p class="step">Schritt 2 von 3</p>
        <h1>System bereit</h1>
        <ul class="checks">' . $li . '</ul>
        <div class="alert alert--err" style="margin-top:1.5rem">
            <strong>Sicherheitshinweis:</strong> Loeschen Sie <code>public/install.php</code> nach Abschluss vom Server.
        </div>
        <h2>Konfiguration</h2>
        <form method="post" action="/install.php?step=run">
            <label for="site_url">Site-URL (ohne Slash am Ende)</label>
            <input type="text" id="site_url" name="site_url" required value="' . htmlspecialchars($defaultUrl) . '">

            <label for="admin_user">Admin-Benutzername</label>
            <input type="text" id="admin_user" name="admin_user" required value="admin">

            <label for="admin_pw">Admin-Passwort (min. 12 Zeichen, mit Gross-, Kleinbuchstabe, Ziffer)</label>
            <input type="password" id="admin_pw" name="admin_pw" required minlength="12" autocomplete="new-password">

            <label for="admin_pw2">Admin-Passwort wiederholen</label>
            <input type="password" id="admin_pw2" name="admin_pw2" required minlength="12" autocomplete="new-password">

            <label for="email">Kontakt-E-Mail (kommt ins Impressum + mailto-Links)</label>
            <input type="email" id="email" name="email" required value="info@rothe-transporte.de">

            <label for="phone">Telefon (Display-Format)</label>
            <input type="text" id="phone" name="phone" required value="07127 18231">

            <label for="phone_e164">Telefon (E.164 fuer tel:-Links)</label>
            <input type="text" id="phone_e164" name="phone_e164" required value="+497127182310">

            <p style="margin-top:2rem"><button type="submit">Installation starten</button></p>
        </form>
    ');
    exit;
}

// ── 5) Installation ausfuehren ─────────────────────────────────────────────
if ($step === 'run' && $post) {
    $errors = [];
    $siteUrl = trim((string) ($_POST['site_url'] ?? ''));
    $adminUser = trim((string) ($_POST['admin_user'] ?? 'admin'));
    $adminPw   = (string) ($_POST['admin_pw'] ?? '');
    $adminPw2  = (string) ($_POST['admin_pw2'] ?? '');
    $email     = trim((string) ($_POST['email'] ?? ''));
    $phone     = trim((string) ($_POST['phone'] ?? ''));
    $phoneE164 = trim((string) ($_POST['phone_e164'] ?? ''));

    if (!preg_match('#^https?://[^/\s]+$#i', $siteUrl)) $errors[] = 'Site-URL ungueltig.';
    if ($adminUser === '')                              $errors[] = 'Admin-Benutzername fehlt.';
    if ($adminPw !== $adminPw2)                         $errors[] = 'Passwoerter stimmen nicht ueberein.';
    if (strlen($adminPw) < 12)                          $errors[] = 'Passwort zu kurz (min. 12 Zeichen).';
    if (!preg_match('/[A-Z]/', $adminPw) || !preg_match('/[a-z]/', $adminPw) || !preg_match('/\d/', $adminPw)) {
        $errors[] = 'Passwort braucht Gross-, Kleinbuchstaben und eine Ziffer.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL))     $errors[] = 'E-Mail ungueltig.';

    if ($errors) {
        $errHtml = '<div class="alert alert--err"><strong>Bitte korrigieren:</strong><ul>';
        foreach ($errors as $e) $errHtml .= '<li>' . htmlspecialchars($e) . '</li>';
        $errHtml .= '</ul></div><p><a href="/install.php">← Zurueck</a></p>';
        render_layout('Validierungsfehler', $errHtml);
        exit;
    }

    $log = [];

    // 5a. .env schreiben
    $envContent = "# Rothe-Transporte – generiert vom Installer am " . date('c') . "\n";
    $envContent .= "SITE_URL={$siteUrl}\n";
    $envContent .= "DB_PATH=data/database.sqlite\n";
    $envContent .= "UPLOADS_PATH=uploads\n";
    $envContent .= "ADMIN_DEFAULT_USER={$adminUser}\n";
    $envContent .= "ADMIN_DEFAULT_PASSWORD=__set_via_installer__\n";
    $envContent .= "SESSION_LIFETIME=3600\n";
    $envContent .= "APP_ENV=production\n";
    $envContent .= 'IP_HASH_SALT=' . bin2hex(random_bytes(16)) . "\n";
    file_put_contents($baseDir . '/.env', $envContent);
    chmod($baseDir . '/.env', 0640);
    $log[] = '.env geschrieben';

    // 5b. Bootstrap laden + Migrationen ausfuehren
    require $baseDir . '/src/bootstrap.php';
    if (!class_exists('App\\Core\\Database')) {
        render_layout('Bootstrap-Fehler', '<div class="alert alert--err">Database-Klasse nicht gefunden – src/Database.php fehlt?</div>');
        exit;
    }
    \App\Core\Database::runMigrations();
    $log[] = 'Migrationen ausgefuehrt';

    // 5c. Admin-Passwort setzen
    $pdo = \App\Core\Database::getInstance();
    $hash = password_hash($adminPw, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare('SELECT id FROM users WHERE username = ? LIMIT 1');
    $stmt->execute([$adminUser]);
    if ($row = $stmt->fetch()) {
        $pdo->prepare('UPDATE users SET password_hash = ? WHERE id = ?')
            ->execute([$hash, (int) $row['id']]);
    } else {
        $pdo->prepare('INSERT INTO users (username, password_hash, role) VALUES (?, ?, ?)')
            ->execute([$adminUser, $hash, 'admin']);
    }
    $log[] = 'Admin-Konto gesetzt: ' . htmlspecialchars($adminUser);

    // 5d. Settings setzen
    $settings = [
        'phone'      => $phone,
        'phone_e164' => $phoneE164,
        'email'      => $email,
    ];
    $repo = $GLOBALS['settingRepo'] ?? null;
    if ($repo) {
        $repo->setMany($settings);
        $log[] = 'Kontakt-Settings aktualisiert (Telefon, E-Mail)';
    }

    // 5e. Favicons + SVG-Platzhalter erzeugen
    $genFav = $baseDir . '/scripts/generate_favicons.php';
    $genPlc = $baseDir . '/scripts/generate_placeholders.php';
    if (is_file($genFav))  { ob_start(); include $genFav; ob_end_clean(); $log[] = 'Favicons erzeugt'; }
    if (is_file($genPlc))  { ob_start(); include $genPlc; ob_end_clean(); $log[] = 'SVG-Platzhalter erzeugt'; }

    // 5e.b. uploads/ enthaelt Originalbilder? -> resizen + Media-Library seeden
    $uploadsDir = $baseDir . '/uploads';
    $origCount  = 0;
    if (is_dir($uploadsDir)) {
        foreach (scandir($uploadsDir) ?: [] as $f) {
            if ($f === '.' || $f === '..' || str_starts_with($f, '.')) continue;
            if (is_file($uploadsDir . '/' . $f) && preg_match('/\.(jpe?g|png|webp|gif)$/i', $f)) {
                $origCount++;
            }
        }
    }
    if ($origCount > 0) {
        $resizer = $baseDir . '/scripts/resize_uploads.php';
        $seeder  = $baseDir . '/scripts/seed_media_from_originals.php';
        if (is_file($resizer)) {
            ob_start(); include $resizer; ob_end_clean();
            $log[] = "Resize: {$origCount} Originalbilder verarbeitet (JPG + WebP, 1600/1200/800 px)";
        }
        if (is_file($seeder)) {
            ob_start(); include $seeder; ob_end_clean();
            $log[] = 'Media-Library mit ' . $origCount . ' Eintraegen geseedet (Logo + Fahrzeug-Bilder zugeordnet)';
        }
    }

    // 5f. Symlink public/uploads anlegen, falls noch nicht da
    $sym = $baseDir . '/public/uploads';
    if (!is_link($sym) && !is_dir($sym)) {
        @symlink('../uploads', $sym);
    }
    $log[] = 'Uploads-Symlink: ' . (is_link($sym) ? 'OK' : 'manuell anlegen!');

    // 5g. Lock + Cleanup
    file_put_contents($lockFile, date('c'));
    chmod($lockFile, 0640);
    // remove any leftover token from previous installer versions
    @unlink($baseDir . '/data/install_token.txt');
    $log[] = 'Installer gesperrt (data/installed.lock)';

    $li = '';
    foreach ($log as $l) $li .= '<li class="ok">' . $l . '</li>';
    render_layout('Fertig', '
        <p class="step">Schritt 3 von 3</p>
        <h1>Installation abgeschlossen</h1>
        <div class="alert alert--ok">
            <strong>Erfolg.</strong> Sie koennen jetzt loslegen.
        </div>
        <h2>Was passiert ist</h2>
        <ul class="checks">' . $li . '</ul>
        <h2>Naechste Schritte</h2>
        <ol>
            <li>Im Hosting-Panel <strong>HTTPS-Erzwingung</strong> einschalten und in <code>public/.htaccess</code> den HSTS-Block einkommentieren.</li>
            <li>Im Admin (<a href="/admin/login">/admin/login</a>) die fehlenden technischen Daten von <em>Anhaenger Alltagsheld</em> und <em>Motorwagen mit Ladekran</em> eintragen.</li>
            <li>Echte Fotos unter <a href="/admin/media">/admin/media</a> hochladen und als Hero-/Fahrzeug-Bilder zuweisen.</li>
            <li>Sitemap bei der Google Search Console einreichen: <code>' . htmlspecialchars(rtrim($siteUrl, '/')) . '/sitemap.xml</code></li>
            <li>Diese Datei (<code>install.php</code>) optional vom Server loeschen, sie ist jetzt gesperrt.</li>
        </ol>
        <p><a href="/admin/login">→ Zum Admin-Login</a> · <a href="/">→ Zur Webseite</a></p>
    ');
    exit;
}

http_response_code(400);
render_layout('Unbekannter Schritt', '<div class="alert alert--err">Unbekannter Schritt.</div>');

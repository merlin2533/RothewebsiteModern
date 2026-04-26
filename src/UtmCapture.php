<?php

declare(strict_types=1);

namespace App\Core;

/**
 * UTM / Click-ID capture.
 * - Persists a long-lived "rt_attr" cookie + DB row at first touch.
 * - Adds visit count and updates last_seen on subsequent visits.
 * - First-touch attribution wins (utm_* / *_clid only set on first visit).
 *
 * GET-only, no impact on POST/admin requests, never blocks.
 */
final class UtmCapture
{
    private const COOKIE_NAME = 'rt_attr';
    private const COOKIE_TTL  = 31536000; // 1 year

    public static function run(): void
    {
        try {
            $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
            $uri    = (string) ($_SERVER['REQUEST_URI'] ?? '/');

            if ($method !== 'GET' || str_starts_with($uri, '/admin')
                || str_starts_with($uri, '/assets') || str_starts_with($uri, '/uploads')) {
                return;
            }

            $pdo = Database::getInstance();
            $key = $_COOKIE[self::COOKIE_NAME] ?? '';
            $isNew = false;

            if ($key === '' || !preg_match('/^[a-f0-9]{32}$/', $key)) {
                $key = bin2hex(random_bytes(16));
                $isNew = true;
                self::setCookie($key);
            }

            $params = $_GET;
            $hasUtm = !empty($params['utm_source']) || !empty($params['gclid'])
                || !empty($params['fbclid'])    || !empty($params['msclkid']);

            // No UTMs and not a new session → cheap update only every ~5 mins
            if ($isNew) {
                self::insert($pdo, $key, $params);
            } elseif ($hasUtm) {
                self::updateOrUpgrade($pdo, $key, $params);
            } else {
                self::touch($pdo, $key);
            }
        } catch (\Throwable) {
            // attribution must never break the page
        }
    }

    private static function insert(\PDO $pdo, string $key, array $params): void
    {
        $stmt = $pdo->prepare('
            INSERT INTO lead_attributions
              (session_key, landing_url, referrer, utm_source, utm_medium, utm_campaign,
               utm_term, utm_content, gclid, fbclid, msclkid, user_agent, ip_hash)
            VALUES
              (:k, :url, :ref, :src, :med, :camp, :term, :content, :gclid, :fbclid, :msclkid, :ua, :iph)
        ');
        $stmt->execute([
            ':k'       => $key,
            ':url'     => substr((string) ($_SERVER['REQUEST_URI'] ?? ''), 0, 512),
            ':ref'     => substr((string) ($_SERVER['HTTP_REFERER'] ?? ''), 0, 512),
            ':src'     => self::clip($params['utm_source']   ?? null),
            ':med'     => self::clip($params['utm_medium']   ?? null),
            ':camp'    => self::clip($params['utm_campaign'] ?? null),
            ':term'    => self::clip($params['utm_term']     ?? null),
            ':content' => self::clip($params['utm_content']  ?? null),
            ':gclid'   => self::clip($params['gclid']        ?? null),
            ':fbclid'  => self::clip($params['fbclid']       ?? null),
            ':msclkid' => self::clip($params['msclkid']      ?? null),
            ':ua'      => substr((string) ($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 255),
            ':iph'     => self::ipHash(),
        ]);
    }

    private static function updateOrUpgrade(\PDO $pdo, string $key, array $params): void
    {
        // Upgrade attribution only if previous fields were empty (first-touch wins).
        $row = $pdo->prepare('SELECT * FROM lead_attributions WHERE session_key = ?');
        $row->execute([$key]);
        $existing = $row->fetch();
        if (!$existing) {
            self::insert($pdo, $key, $params);
            return;
        }

        $upd = [
            'utm_source'   => $existing['utm_source']   ?: self::clip($params['utm_source']   ?? null),
            'utm_medium'   => $existing['utm_medium']   ?: self::clip($params['utm_medium']   ?? null),
            'utm_campaign' => $existing['utm_campaign'] ?: self::clip($params['utm_campaign'] ?? null),
            'utm_term'     => $existing['utm_term']     ?: self::clip($params['utm_term']     ?? null),
            'utm_content'  => $existing['utm_content']  ?: self::clip($params['utm_content']  ?? null),
            'gclid'        => $existing['gclid']        ?: self::clip($params['gclid']        ?? null),
            'fbclid'       => $existing['fbclid']       ?: self::clip($params['fbclid']       ?? null),
            'msclkid'      => $existing['msclkid']      ?: self::clip($params['msclkid']      ?? null),
        ];

        $pdo->prepare('
            UPDATE lead_attributions SET
              utm_source = :s, utm_medium = :m, utm_campaign = :c,
              utm_term = :t, utm_content = :ct,
              gclid = :gc, fbclid = :fb, msclkid = :ms,
              visits = visits + 1,
              last_seen = strftime("%Y-%m-%d %H:%M:%S", "now")
            WHERE session_key = :k
        ')->execute([
            ':s' => $upd['utm_source'], ':m' => $upd['utm_medium'], ':c' => $upd['utm_campaign'],
            ':t' => $upd['utm_term'],   ':ct'=> $upd['utm_content'],
            ':gc'=> $upd['gclid'],      ':fb'=> $upd['fbclid'],     ':ms'=> $upd['msclkid'],
            ':k' => $key,
        ]);
    }

    private static function touch(\PDO $pdo, string $key): void
    {
        $pdo->prepare('
            UPDATE lead_attributions
            SET visits = visits + 1,
                last_seen = strftime("%Y-%m-%d %H:%M:%S", "now")
            WHERE session_key = ?
        ')->execute([$key]);
    }

    private static function setCookie(string $key): void
    {
        if (headers_sent()) {
            return;
        }
        $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
        setcookie(self::COOKIE_NAME, $key, [
            'expires'  => time() + self::COOKIE_TTL,
            'path'     => '/',
            'secure'   => $secure,
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
        $_COOKIE[self::COOKIE_NAME] = $key;
    }

    private static function clip($v): ?string
    {
        if ($v === null) return null;
        $v = trim((string) $v);
        return $v === '' ? null : substr($v, 0, 200);
    }

    private static function ipHash(): ?string
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '';
        if ($ip === '') return null;
        $salt = (string) (getenv('IP_HASH_SALT') ?: ($GLOBALS['config']['admin_default_password'] ?? 'rothe-salt'));
        return substr(sha1($ip . $salt), 0, 40);
    }
}

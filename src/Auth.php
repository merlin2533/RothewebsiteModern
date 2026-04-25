<?php

declare(strict_types=1);

namespace App\Core;

final class Auth
{
    private const SESSION_KEY = '_auth_user';
    private const LAST_ACTIVITY = '_auth_last_activity';
    private const MAX_ATTEMPTS = 5;
    private const ATTEMPT_WINDOW = 900; // 15 minutes

    public static function attempt(string $username, string $password, string $ip): bool
    {
        if (self::isLocked($ip)) {
            return false;
        }

        $pdo = Database::getInstance();
        $stmt = $pdo->prepare('SELECT id, username, password_hash, role FROM users WHERE username = ? LIMIT 1');
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($password, $user['password_hash'])) {
            self::recordFailure($ip);
            return false;
        }

        // Update last_login_at
        $pdo->prepare('UPDATE users SET last_login_at = ? WHERE id = ?')
            ->execute([date('Y-m-d H:i:s'), $user['id']]);

        // Set session
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        session_regenerate_id(true);
        $_SESSION[self::SESSION_KEY] = [
            'id'       => (int) $user['id'],
            'username' => $user['username'],
            'role'     => $user['role'],
        ];
        $_SESSION[self::LAST_ACTIVITY] = time();

        self::clearFailures($ip);
        return true;
    }

    public static function check(): bool
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        if (empty($_SESSION[self::SESSION_KEY])) {
            return false;
        }

        // Check session timeout
        $config = $GLOBALS['config'] ?? [];
        $lifetime = (int) ($config['session_lifetime'] ?? 3600);
        $last = $_SESSION[self::LAST_ACTIVITY] ?? 0;
        if (time() - $last > $lifetime) {
            self::logout();
            return false;
        }
        $_SESSION[self::LAST_ACTIVITY] = time();
        return true;
    }

    public static function user(): ?array
    {
        if (!self::check()) {
            return null;
        }
        return $_SESSION[self::SESSION_KEY];
    }

    public static function requireLogin(): void
    {
        if (!self::check()) {
            header('Location: /admin/login');
            exit;
        }
    }

    public static function logout(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }
        session_destroy();
    }

    private static function attemptsFile(): string
    {
        $config = $GLOBALS['config'] ?? [];
        $base = $config['base_dir'] ?? dirname(__DIR__);
        return $base . '/data/login_attempts.json';
    }

    private static function loadAttempts(): array
    {
        $file = self::attemptsFile();
        if (!is_readable($file)) {
            return [];
        }
        $json = file_get_contents($file);
        if ($json === false) {
            return [];
        }
        $data = json_decode($json, true);
        return is_array($data) ? $data : [];
    }

    private static function saveAttempts(array $data): void
    {
        $file = self::attemptsFile();
        $dir = dirname($file);
        if (!is_dir($dir)) {
            mkdir($dir, 0750, true);
        }
        file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT), LOCK_EX);
    }

    private static function isLocked(string $ip): bool
    {
        $data = self::loadAttempts();
        $cutoff = time() - self::ATTEMPT_WINDOW;
        $recent = array_filter($data[$ip] ?? [], static fn($t) => $t > $cutoff);
        return count($recent) >= self::MAX_ATTEMPTS;
    }

    private static function recordFailure(string $ip): void
    {
        $data = self::loadAttempts();
        $cutoff = time() - self::ATTEMPT_WINDOW;
        $existing = array_filter($data[$ip] ?? [], static fn($t) => $t > $cutoff);
        $existing[] = time();
        $data[$ip] = array_values($existing);
        self::saveAttempts($data);
    }

    private static function clearFailures(string $ip): void
    {
        $data = self::loadAttempts();
        unset($data[$ip]);
        self::saveAttempts($data);
    }
}

<?php

declare(strict_types=1);

namespace App\Core;

final class Csrf
{
    private const SESSION_KEY = '_csrf_token';

    public static function token(): string
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        if (empty($_SESSION[self::SESSION_KEY])) {
            $_SESSION[self::SESSION_KEY] = bin2hex(random_bytes(32));
        }
        return $_SESSION[self::SESSION_KEY];
    }

    public static function verify(?string $token): bool
    {
        if ($token === null || $token === '') {
            return false;
        }
        // Ensure the session is active – callers (e.g. POST handlers) often
        // do not boot it themselves, so $_SESSION would be empty otherwise.
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        $expected = $_SESSION[self::SESSION_KEY] ?? '';
        if ($expected === '') {
            return false;
        }
        return hash_equals($expected, $token);
    }

    public static function field(): string
    {
        $t = htmlspecialchars(self::token(), ENT_QUOTES, 'UTF-8');
        return '<input type="hidden" name="_token" value="' . $t . '">';
    }
}

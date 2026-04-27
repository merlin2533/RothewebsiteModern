<?php

declare(strict_types=1);

namespace App\Core;

use App\Repositories\RedirectRepository;

final class RedirectMap
{
    /**
     * Sends a redirect immediately if a matching active rule is found.
     * Preserves the original query string. Path-only match against from_path.
     */
    public static function handle(RedirectRepository $repo): void
    {
        try {
            $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
            if (!in_array($method, ['GET', 'HEAD'], true)) {
                return;
            }
            $uri = (string) ($_SERVER['REQUEST_URI'] ?? '/');
            $path = parse_url($uri, PHP_URL_PATH) ?: '/';
            $query = parse_url($uri, PHP_URL_QUERY);

            // Try exact match first; fall back to with-trailing-slash variant.
            $hit = $repo->lookupActive($path);
            if (!$hit && !str_ends_with($path, '/')) {
                $hit = $repo->lookupActive($path . '/');
            } elseif (!$hit && str_ends_with($path, '/')) {
                $hit = $repo->lookupActive(rtrim($path, '/'));
            }
            if (!$hit) {
                return;
            }

            $target = (string) $hit['to_path'];
            if ($query && !str_contains($target, '?')) {
                $target .= '?' . $query;
            }

            $repo->recordHit((int) $hit['id']);

            $code = (int) $hit['status_code'];
            if (!in_array($code, [301, 302, 307, 308], true)) {
                $code = 301;
            }
            header('Location: ' . $target, true, $code);
            exit;
        } catch (\Throwable) {
            // never block normal routing on redirect-map failure
        }
    }
}

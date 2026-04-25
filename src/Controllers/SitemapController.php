<?php

declare(strict_types=1);

namespace App\Controllers;

final class SitemapController
{
    public function xml(array $args): void
    {
        $config = $GLOBALS['config'];
        $base = rtrim((string) $config['site_url'], '/');

        $pages = $GLOBALS['pageRepo']->all();
        $vehicles = $GLOBALS['vehicleRepo']->allActive();
        $services = $GLOBALS['serviceRepo']->allActive();

        $entries = [];
        foreach ($pages as $p) {
            if ((int) $p['is_published'] !== 1 || $p['slug'] === '404') {
                continue;
            }
            $path = $p['slug'] === 'home' ? '/' : '/' . $p['slug'];
            $entries[] = [$base . $path, $p['updated_at'] ?? date('Y-m-d')];
        }
        foreach ($vehicles as $v) {
            $entries[] = [$base . '/fahrzeuge/' . $v['slug'], $v['updated_at'] ?? date('Y-m-d')];
        }
        foreach ($services as $s) {
            $entries[] = [$base . '/leistungen/' . $s['slug'], $s['updated_at'] ?? date('Y-m-d')];
        }

        header('Content-Type: application/xml; charset=utf-8');
        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        foreach ($entries as [$loc, $lastmod]) {
            $lm = substr((string) $lastmod, 0, 10);
            echo "  <url>\n";
            echo "    <loc>" . htmlspecialchars($loc, ENT_QUOTES) . "</loc>\n";
            echo "    <lastmod>" . htmlspecialchars($lm, ENT_QUOTES) . "</lastmod>\n";
            echo "  </url>\n";
        }
        echo '</urlset>' . "\n";
    }

    public function robots(array $args): void
    {
        $config = $GLOBALS['config'];
        $base = rtrim((string) $config['site_url'], '/');
        header('Content-Type: text/plain; charset=utf-8');
        echo "User-agent: *\n";
        echo "Disallow: /admin/\n";
        echo "Disallow: /uploads/.htaccess\n";
        echo "Allow: /\n\n";
        echo "Sitemap: {$base}/sitemap.xml\n";
    }
}

<?php

declare(strict_types=1);

namespace App\Controllers;

final class SitemapController
{
    public function xml(array $args): void
    {
        $config = $GLOBALS['config'];
        $base = rtrim((string) $config['site_url'], '/');

        $pages    = $GLOBALS['pageRepo']->all();
        $vehicles = $GLOBALS['vehicleRepo']->allActive();
        $services = $GLOBALS['serviceRepo']->allActive();
        $landings = $GLOBALS['landingRepo']->allPublished();

        $entries = [];
        foreach ($pages as $p) {
            if ((int) $p['is_published'] !== 1 || $p['slug'] === '404') {
                continue;
            }
            $path = $p['slug'] === 'home' ? '/' : '/' . $p['slug'];
            $prio = $p['slug'] === 'home' ? '1.0' : ($p['slug'] === 'fahrzeuge' ? '0.9' : '0.8');
            $entries[] = [$base . $path, $p['updated_at'] ?? date('Y-m-d'), 'weekly', $prio];
        }
        foreach ($vehicles as $v) {
            $entries[] = [$base . '/fahrzeuge/' . $v['slug'], $v['updated_at'] ?? date('Y-m-d'), 'monthly', '0.7'];
        }
        foreach ($services as $s) {
            $entries[] = [$base . '/leistungen/' . $s['slug'], $s['updated_at'] ?? date('Y-m-d'), 'monthly', '0.7'];
        }
        foreach ($landings as $lp) {
            $entries[] = [$base . '/' . $lp['slug'], $lp['updated_at'] ?? date('Y-m-d'), 'monthly', '0.8'];
        }

        $latest = max(array_map(static fn($e) => $e[1], $entries));
        $this->sendCacheable('application/xml; charset=utf-8', $latest);

        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        foreach ($entries as [$loc, $lastmod, $cf, $prio]) {
            $lm = substr((string) $lastmod, 0, 10);
            echo "  <url>\n";
            echo "    <loc>" . htmlspecialchars($loc, ENT_QUOTES) . "</loc>\n";
            echo "    <lastmod>" . htmlspecialchars($lm, ENT_QUOTES) . "</lastmod>\n";
            echo "    <changefreq>{$cf}</changefreq>\n";
            echo "    <priority>{$prio}</priority>\n";
            echo "  </url>\n";
        }
        echo '</urlset>' . "\n";
    }

    public function imageSitemap(array $args): void
    {
        $config = $GLOBALS['config'];
        $base = rtrim((string) $config['site_url'], '/');

        $pdo = $GLOBALS['pdo'];
        $rows = $pdo->query('SELECT id, filename, alt_text, original_name FROM media ORDER BY id')->fetchAll();

        $this->sendCacheable('application/xml; charset=utf-8', date('Y-m-d'));

        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"' . "\n";
        echo '        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . "\n";

        if (!$rows) {
            // Fallback: at least the placeholder OG image for crawlers
            echo "  <url>\n";
            echo "    <loc>" . htmlspecialchars($base . '/', ENT_QUOTES) . "</loc>\n";
            echo "    <image:image>\n";
            echo "      <image:loc>" . htmlspecialchars($base . '/assets/images/placeholders/og-default.svg', ENT_QUOTES) . "</image:loc>\n";
            echo "      <image:title>Rothe-Transporte – Maschinentransporte seit 1978</image:title>\n";
            echo "    </image:image>\n";
            echo "  </url>\n";
        } else {
            echo "  <url>\n";
            echo "    <loc>" . htmlspecialchars($base . '/', ENT_QUOTES) . "</loc>\n";
            foreach ($rows as $r) {
                $img = $base . '/uploads/' . ltrim((string) $r['filename'], '/');
                echo "    <image:image>\n";
                echo "      <image:loc>" . htmlspecialchars($img, ENT_QUOTES) . "</image:loc>\n";
                if (!empty($r['alt_text'])) {
                    echo "      <image:caption>" . htmlspecialchars((string) $r['alt_text'], ENT_QUOTES) . "</image:caption>\n";
                }
                if (!empty($r['original_name'])) {
                    echo "      <image:title>" . htmlspecialchars((string) $r['original_name'], ENT_QUOTES) . "</image:title>\n";
                }
                echo "    </image:image>\n";
            }
            echo "  </url>\n";
        }
        echo '</urlset>' . "\n";
    }

    public function robots(array $args): void
    {
        $config = $GLOBALS['config'];
        $base = rtrim((string) $config['site_url'], '/');
        $this->sendCacheable('text/plain; charset=utf-8', date('Y-m-d'));

        echo "# robots.txt for {$base}\n";
        echo "# Generated dynamically from PHP – see App\\Controllers\\SitemapController\n\n";

        // Default policy: allow everything except admin/uploads .htaccess
        echo "User-agent: *\n";
        echo "Disallow: /admin/\n";
        echo "Disallow: /uploads/.htaccess\n";
        echo "Allow: /\n\n";

        // Explicitly allow the major LLM/AEO crawlers (faktenbasierte Brand-Sichtbarkeit)
        $aiBots = [
            'GPTBot',                 // OpenAI
            'OAI-SearchBot',          // OpenAI search
            'ChatGPT-User',           // ChatGPT browsing
            'PerplexityBot',          // Perplexity
            'Perplexity-User',        // Perplexity user agent
            'ClaudeBot',              // Anthropic
            'Claude-User',            // Anthropic user agent
            'Claude-Web',             // Legacy Anthropic
            'Google-Extended',        // Gemini training
            'GoogleOther',            // Google secondary crawls
            'Applebot',               // Apple Intelligence / Siri
            'Applebot-Extended',      // Apple Intelligence training
            'Bingbot',                // Bing + Copilot
            'DuckDuckBot',            // DuckDuckGo
            'YandexBot',              // Yandex
            'meta-externalagent',     // Meta AI
            'FacebookBot',            // Meta link previews
            'cohere-ai',              // Cohere
            'YouBot',                 // You.com
            'Bytespider',             // ByteDance / Doubao
            'Amazonbot',              // Amazon
        ];
        foreach ($aiBots as $bot) {
            echo "User-agent: {$bot}\n";
            echo "Allow: /\n";
            echo "Disallow: /admin/\n\n";
        }

        // Sitemaps
        echo "Sitemap: {$base}/sitemap_index.xml\n";
        echo "Sitemap: {$base}/sitemap.xml\n";
        echo "Sitemap: {$base}/sitemap-images.xml\n";
    }

    public function llmsTxt(array $args): void
    {
        $this->serveTemplate('llms.txt');
    }

    public function aiTxt(array $args): void
    {
        $this->serveTemplate('ai.txt');
    }

    /**
     * Sitemap-Index: zeigt auf alle Sub-Sitemaps.
     * Convention: Search Console findet beides automatisch via robots.txt,
     * aber ein expliziter Index hilft Bing/Yandex und macht spaetere
     * Erweiterungen trivial (Blog-Sitemap etc.).
     */
    public function index(array $args): void
    {
        $base = rtrim((string) $GLOBALS['config']['site_url'], '/');
        $this->sendCacheable('application/xml; charset=utf-8', date('Y-m-d'));
        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        foreach (['sitemap.xml', 'sitemap-images.xml'] as $sm) {
            echo "  <sitemap>\n";
            echo '    <loc>' . htmlspecialchars($base . '/' . $sm, ENT_QUOTES) . "</loc>\n";
            echo '    <lastmod>' . date('Y-m-d') . "</lastmod>\n";
            echo "  </sitemap>\n";
        }
        echo '</sitemapindex>' . "\n";
    }

    private function serveTemplate(string $filename): void
    {
        $path = $GLOBALS['config']['base_dir'] . '/src/Templates/' . $filename;
        if (!is_readable($path)) {
            http_response_code(404);
            return;
        }
        $tpl = (string) file_get_contents($path);

        $repls = [
            '{{site_url}}'         => rtrim((string) $GLOBALS['config']['site_url'], '/'),
            '{{company_name}}'     => setting('company_name'),
            '{{owners}}'           => setting('owners'),
            '{{address_street}}'   => setting('address_street'),
            '{{address_zip}}'      => setting('address_zip'),
            '{{address_city}}'     => setting('address_city'),
            '{{address_country}}'  => setting('address_country'),
            '{{phone}}'            => setting('phone'),
            '{{phone_e164}}'       => setting('phone_e164'),
            '{{email}}'            => setting('email'),
            '{{founded_year}}'     => setting('founded_year'),
            '{{areas_served}}'     => setting('areas_served'),
            '{{opening_hours}}'    => setting('opening_hours'),
        ];
        $tpl = strtr($tpl, $repls);

        $this->sendCacheable('text/plain; charset=utf-8', date('Y-m-d'));
        echo $tpl;
    }

    private function sendCacheable(string $contentType, string $lastmodDate): void
    {
        $lm = substr($lastmodDate, 0, 10);
        $ts = strtotime($lm . ' 00:00:00 UTC');
        if ($ts === false) {
            $ts = time();
        }
        $lastModifiedHttp = gmdate('D, d M Y H:i:s', $ts) . ' GMT';
        $etag = '"' . substr(md5($lastModifiedHttp . $contentType), 0, 16) . '"';

        // Conditional: 304 if client has fresh copy
        $ifNone  = $_SERVER['HTTP_IF_NONE_MATCH']     ?? '';
        $ifMod   = $_SERVER['HTTP_IF_MODIFIED_SINCE'] ?? '';
        if ($ifNone === $etag || ($ifMod !== '' && strtotime($ifMod) >= $ts)) {
            http_response_code(304);
            header('ETag: ' . $etag);
            exit;
        }

        header('Content-Type: ' . $contentType);
        header('Last-Modified: ' . $lastModifiedHttp);
        header('ETag: ' . $etag);
        header('Cache-Control: public, max-age=3600, stale-while-revalidate=86400');
    }
}

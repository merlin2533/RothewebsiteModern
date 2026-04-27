<?php

declare(strict_types=1);

namespace App\Controllers;

/**
 * Server-side tagging endpoint.
 *
 * Accepts JSON or form-encoded events and forwards them to configured sinks
 * (GA4 Measurement Protocol, Plausible Events API, Meta CAPI, …) with the
 * server's IP / User-Agent. Uses fire-and-forget HTTP via curl_multi to keep
 * the response fast.
 *
 * Body:
 *   { "event": "phone_click", "label": "+497127182310", "value": 1, "page": "/" }
 *
 * Sinks are toggled via settings:
 *   - ga4_measurement_id  (e.g. G-ABCDEFG)
 *   - ga4_api_secret      (Measurement Protocol secret)
 *   - plausible_domain    (existing setting; reused)
 *   - plausible_events_api (default https://plausible.io/api/event)
 *   - meta_pixel_id       (Meta CAPI – optional)
 *   - meta_capi_token     (Meta CAPI access token – optional)
 *
 * Server-side tagging respects the same consent cookie as client-side: events
 * are forwarded only when the visitor has granted the matching category.
 */
final class TrackController
{
    public function event(array $args): void
    {
        // Always 204 quickly – clients shouldn't wait on tag servers.
        register_shutdown_function([$this, 'forwardAsync']);

        $raw = (string) file_get_contents('php://input');
        $payload = json_decode($raw, true);
        if (!is_array($payload)) {
            // Allow form-encoded fallback
            $payload = $_POST;
        }
        $payload = is_array($payload) ? $payload : [];

        // Validate minimal shape
        $event = (string) ($payload['event'] ?? '');
        if ($event === '' || strlen($event) > 80 || !preg_match('/^[a-z0-9_]+$/i', $event)) {
            http_response_code(400);
            echo '{"error":"invalid event name"}';
            return;
        }

        $cats = (string) ($_COOKIE['rt_consent_v2'] ?? '');
        $catList = array_map('trim', explode(',', $cats));
        $hasStats     = in_array('statistics', $catList, true);
        $hasMarketing = in_array('marketing',  $catList, true);

        // Stash for forwardAsync()
        $GLOBALS['_track_ctx'] = [
            'event'       => $event,
            'label'       => substr((string) ($payload['label'] ?? ''), 0, 200),
            'value'       => is_numeric($payload['value'] ?? null) ? (float) $payload['value'] : null,
            'page'        => substr((string) ($payload['page'] ?? ($_SERVER['HTTP_REFERER'] ?? '/')), 0, 512),
            'ua'          => substr((string) ($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 255),
            'ip'          => (string) ($_SERVER['REMOTE_ADDR'] ?? ''),
            'sid'         => (string) ($_COOKIE['rt_attr'] ?? ''),
            'has_stats'   => $hasStats,
            'has_market'  => $hasMarketing,
        ];

        http_response_code(204);
        // empty body
    }

    public function forwardAsync(): void
    {
        $ctx = $GLOBALS['_track_ctx'] ?? null;
        if (!$ctx) return;

        $sinks = [];

        // ── GA4 Measurement Protocol (counts as marketing if used for ads, otherwise stats)
        $ga4Id     = trim((string) setting('ga4_measurement_id', ''));
        $ga4Secret = trim((string) setting('ga4_api_secret', ''));
        if ($ga4Id !== '' && $ga4Secret !== '' && $ctx['has_stats']) {
            $sinks[] = [
                'POST',
                'https://www.google-analytics.com/mp/collect?measurement_id=' . urlencode($ga4Id) . '&api_secret=' . urlencode($ga4Secret),
                ['Content-Type: application/json'],
                json_encode([
                    'client_id' => $ctx['sid'] ?: bin2hex(random_bytes(8)),
                    'events'    => [[
                        'name'   => $ctx['event'],
                        'params' => array_filter([
                            'label' => $ctx['label'],
                            'value' => $ctx['value'],
                            'page'  => $ctx['page'],
                        ]),
                    ]],
                ]),
            ];
        }

        // ── Plausible Events API (counts as stats)
        $plDomain = trim((string) setting('plausible_domain', ''));
        $plApi    = trim((string) setting('plausible_events_api', 'https://plausible.io/api/event'));
        if ($plDomain !== '' && $plApi !== '' && $ctx['has_stats']) {
            $sinks[] = [
                'POST',
                $plApi,
                ['Content-Type: application/json', 'User-Agent: ' . $ctx['ua'], 'X-Forwarded-For: ' . $ctx['ip']],
                json_encode([
                    'name'    => $ctx['event'],
                    'url'     => $ctx['page'],
                    'domain'  => $plDomain,
                    'props'   => array_filter([
                        'label' => $ctx['label'],
                        'value' => $ctx['value'],
                    ]),
                ]),
            ];
        }

        // ── Meta CAPI (counts as marketing)
        $metaPixel = trim((string) setting('meta_pixel_id', ''));
        $metaToken = trim((string) setting('meta_capi_token', ''));
        if ($metaPixel !== '' && $metaToken !== '' && $ctx['has_market']) {
            $sinks[] = [
                'POST',
                'https://graph.facebook.com/v19.0/' . urlencode($metaPixel) . '/events?access_token=' . urlencode($metaToken),
                ['Content-Type: application/json'],
                json_encode([
                    'data' => [[
                        'event_name' => $ctx['event'],
                        'event_time' => time(),
                        'event_source_url' => $ctx['page'],
                        'action_source'    => 'website',
                        'user_data' => array_filter([
                            'client_user_agent' => $ctx['ua'],
                            'client_ip_address' => $ctx['ip'],
                        ]),
                        'custom_data' => array_filter([
                            'label' => $ctx['label'],
                            'value' => $ctx['value'],
                        ]),
                    ]],
                ]),
            ];
        }

        if (empty($sinks)) return;

        // Fire-and-forget multi-curl. Closes connection client-side via 204
        // before this fires (register_shutdown_function).
        $mh = curl_multi_init();
        $handles = [];
        foreach ($sinks as [$method, $url, $headers, $body]) {
            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST           => $method === 'POST',
                CURLOPT_POSTFIELDS     => $body,
                CURLOPT_HTTPHEADER     => $headers,
                CURLOPT_TIMEOUT_MS     => 1500,
                CURLOPT_CONNECTTIMEOUT_MS => 800,
                CURLOPT_FOLLOWLOCATION => false,
            ]);
            curl_multi_add_handle($mh, $ch);
            $handles[] = $ch;
        }
        $running = null;
        do {
            curl_multi_exec($mh, $running);
            curl_multi_select($mh, 0.1);
        } while ($running > 0);

        foreach ($handles as $ch) {
            curl_multi_remove_handle($mh, $ch);
            curl_close($ch);
        }
        curl_multi_close($mh);
    }
}

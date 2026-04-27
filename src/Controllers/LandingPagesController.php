<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;

final class LandingPagesController
{
    /** Slugs that must never resolve to a landing page – defence-in-depth. */
    private const RESERVED = [
        'admin', 'api', 'og', 'install', 'install.php', 'sitemap.xml',
        'sitemap-images.xml', 'robots.txt', 'llms.txt', 'ai.txt',
        'assets', 'uploads', 'manifest.webmanifest',
        'favicon.ico', 'favicon-16.png', 'favicon-32.png',
        'apple-touch-icon.png', 'icon-192.png', 'icon-512.png', 'icon-1024.png',
    ];

    public function show(array $args): void
    {
        $slug = (string) ($args['slug'] ?? '');
        if ($slug === '' || in_array(strtolower($slug), self::RESERVED, true)) {
            (new LegalController())->notFound([]);
            return;
        }
        $lp = $GLOBALS['landingRepo']->findBySlug($slug);
        if (!$lp || (int) $lp['is_published'] !== 1) {
            (new LegalController())->notFound([]);
            return;
        }

        // Resolve related vehicles + services
        $vehicleSlugs = array_filter(array_map('trim', explode(',', (string) ($lp['related_vehicles'] ?? ''))));
        $serviceSlugs = array_filter(array_map('trim', explode(',', (string) ($lp['related_services'] ?? ''))));

        $relatedVehicles = [];
        foreach ($vehicleSlugs as $vs) {
            $v = $GLOBALS['vehicleRepo']->findBySlug($vs);
            if ($v) $relatedVehicles[] = $v;
        }
        $relatedServices = [];
        foreach ($serviceSlugs as $ss) {
            $s = $GLOBALS['serviceRepo']->findBySlug($ss);
            if ($s) $relatedServices[] = $s;
        }

        // Schema.org Service for industry pages, LocalBusiness add-on for city pages
        $cfg = $GLOBALS['config'];
        $base = rtrim((string) $cfg['site_url'], '/');
        $schemas = [];

        $service = [
            '@context'    => 'https://schema.org',
            '@type'       => 'Service',
            'name'        => $lp['title'],
            'description' => $lp['sub'] ?? '',
            'serviceType' => $lp['title'],
            'url'         => $base . '/' . $lp['slug'],
            'provider'    => [
                '@type' => 'MovingCompany',
                'name'  => setting('company_name'),
                'url'   => $base,
                'telephone' => setting('phone_e164'),
            ],
        ];
        if (!empty($lp['region_name'])) {
            $service['areaServed'] = ['@type' => 'City', 'name' => $lp['region_name']];
        }
        $schemas[] = $service;

        echo View::render('landing_page', [
            'lp'              => $lp,
            'relatedVehicles' => $relatedVehicles,
            'relatedServices' => $relatedServices,
            'currentSlug'     => '',
            'structured_data' => $schemas,
            'page'            => [
                'title'            => $lp['title'],
                'meta_title'       => $lp['meta_title'] ?? ($lp['title'] . ' – Rothe-Transporte'),
                'meta_description' => $lp['meta_description'] ?? '',
                'hero_headline'    => $lp['headline'] ?? $lp['title'],
                'hero_sub'         => $lp['sub'] ?? '',
            ],
        ]);
    }
}

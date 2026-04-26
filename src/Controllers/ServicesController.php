<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;

final class ServicesController
{
    public function index(array $args): void
    {
        $page = $GLOBALS['pageRepo']->findBySlug('leistungen');
        $services = $GLOBALS['serviceRepo']->allActive();

        echo View::render('services', [
            'page'        => $page,
            'currentSlug' => 'leistungen',
            'services'    => $services,
        ]);
    }

    public function show(array $args): void
    {
        $service = $GLOBALS['serviceRepo']->findBySlug((string) ($args['slug'] ?? ''));
        if (!$service) {
            (new LegalController())->notFound([]);
            return;
        }

        $cfg = $GLOBALS['config'];
        $base = rtrim((string) $cfg['site_url'], '/');
        $serviceSchema = [
            '@context'   => 'https://schema.org',
            '@type'      => 'Service',
            'serviceType'=> $service['title'],
            'name'       => $service['title'],
            'description'=> $service['summary'] ?? '',
            'url'        => $base . '/leistungen/' . $service['slug'],
            'provider'   => [
                '@type' => 'MovingCompany',
                'name'  => setting('company_name'),
                'url'   => $base,
            ],
            'areaServed' => array_map(
                static fn($c) => ['@type' => 'Country', 'name' => trim($c)],
                explode(',', (string) setting('areas_served', 'DE'))
            ),
        ];

        echo View::render('service_detail', [
            'service'         => $service,
            'currentSlug'     => 'leistungen',
            'structured_data' => [$serviceSchema],
            'page'            => [
                'title'            => $service['title'],
                'meta_title'       => $service['title'] . ' – Rothe-Transporte',
                'meta_description' => $service['summary'] ?? '',
                'hero_headline'    => $service['title'],
                'hero_sub'         => $service['summary'] ?? '',
            ],
        ]);
    }
}

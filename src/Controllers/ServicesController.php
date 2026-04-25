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
        echo View::render('service_detail', [
            'service'     => $service,
            'currentSlug' => 'leistungen',
            'page'        => [
                'title'            => $service['title'],
                'meta_title'       => $service['title'] . ' – Rothe-Transporte',
                'meta_description' => $service['summary'] ?? '',
                'hero_headline'    => $service['title'],
                'hero_sub'         => $service['summary'] ?? '',
            ],
        ]);
    }
}

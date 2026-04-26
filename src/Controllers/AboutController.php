<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;

final class AboutController
{
    public function index(array $args): void
    {
        $page = $GLOBALS['pageRepo']->findBySlug('ueber-uns');
        $events = $GLOBALS['timelineRepo']->allActive();

        $cfg = $GLOBALS['config'];
        $base = rtrim((string) $cfg['site_url'], '/');
        $articleSchema = [
            '@context' => 'https://schema.org',
            '@type'    => 'AboutPage',
            'mainEntityOfPage' => [
                '@type' => 'WebPage',
                '@id'   => $base . '/ueber-uns',
            ],
            'headline'      => $page['hero_headline'] ?? 'Über uns',
            'description'   => $page['meta_description'] ?? '',
            'datePublished' => substr((string) ($page['created_at'] ?? '2026-01-01'), 0, 10),
            'dateModified'  => substr((string) ($page['updated_at'] ?? date('Y-m-d')), 0, 10),
            'inLanguage'    => 'de-DE',
            'author'        => [
                '@type' => 'Organization',
                'name'  => setting('company_name'),
                'url'   => $base,
            ],
            'publisher'     => [
                '@type' => 'Organization',
                'name'  => setting('company_name'),
                'logo'  => [
                    '@type' => 'ImageObject',
                    'url'   => $base . '/assets/images/logo.svg',
                ],
            ],
        ];

        echo View::render('about', [
            'page'            => $page,
            'currentSlug'     => 'ueber-uns',
            'events'          => $events,
            'structured_data' => [$articleSchema],
        ]);
    }
}

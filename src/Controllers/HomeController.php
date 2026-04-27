<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;

final class HomeController
{
    public function index(array $args): void
    {
        $page     = $GLOBALS['pageRepo']->findBySlug('home');
        $services = $GLOBALS['serviceRepo']->allActive();
        $vehicles = $GLOBALS['vehicleRepo']->allActive();
        $faqs     = $GLOBALS['faqRepo']->activeForPage('home');

        // LCP-Image (Hero) markieren – wenn ein hero_image_id gesetzt ist,
        // verwenden wir die 1600px-Variante daraus, sonst den SVG-Platzhalter.
        $heroImageId = (int) (setting('hero_image_id', '0'));
        $heroUrl     = '/assets/images/placeholders/hero-home.svg';
        $heroType    = 'image/svg+xml';
        if ($heroImageId > 0) {
            $hero = $GLOBALS['mediaRepo']->find($heroImageId);
            if ($hero) {
                $heroUrl  = (string) (\App\Core\MediaResolver::variantUrl($hero, 1600, 'jpg') ?? $heroUrl);
                $heroType = 'image/jpeg';
            }
        }
        if ($page === null) $page = [];
        $page['lcp_image_url']  = $heroUrl;
        $page['lcp_image_type'] = $heroType;

        // FAQPage schema
        $structured = [];
        if (!empty($faqs)) {
            $structured[] = [
                '@context' => 'https://schema.org',
                '@type'    => 'FAQPage',
                'mainEntity' => array_map(
                    static fn(array $f) => [
                        '@type' => 'Question',
                        'name'  => $f['question'],
                        'acceptedAnswer' => [
                            '@type' => 'Answer',
                            'text'  => trim((string) $f['answer_html']),
                        ],
                    ],
                    $faqs
                ),
            ];
        }

        echo View::render('home', [
            'page'            => $page,
            'currentSlug'     => 'home',
            'services'        => $services,
            'vehicles'        => array_slice($vehicles, 0, 2),
            'allVehicles'     => $vehicles,
            'faqs'            => $faqs,
            'structured_data' => $structured,
        ]);
    }
}

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

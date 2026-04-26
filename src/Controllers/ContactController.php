<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;

final class ContactController
{
    public function index(array $args): void
    {
        $page = $GLOBALS['pageRepo']->findBySlug('kontakt');

        $howTo = [
            '@context'    => 'https://schema.org',
            '@type'       => 'HowTo',
            'name'        => 'Maschinentransport bei Rothe-Transporte buchen',
            'description' => 'In drei Schritten zum verbindlichen Festpreis – telefonisch oder per E-Mail.',
            'totalTime'   => 'PT10M',
            'step' => [
                [
                    '@type' => 'HowToStep',
                    'position' => 1,
                    'name' => 'Eckdaten zusammenstellen',
                    'text' => 'Notieren Sie Abhol- und Zielort, Wunschtermin sowie Maße und Gewicht der Sendung.',
                ],
                [
                    '@type' => 'HowToStep',
                    'position' => 2,
                    'name' => 'Disposition kontaktieren',
                    'text' => 'Rufen Sie uns unter ' . setting('phone', '07127 18231') . ' an oder schreiben Sie an ' . setting('email', 'info@rothe-transporte.de') . '.',
                ],
                [
                    '@type' => 'HowToStep',
                    'position' => 3,
                    'name' => 'Festpreis und Termin erhalten',
                    'text' => 'Wir nennen Ihnen Festpreis und Abholfenster und bestätigen die Buchung schriftlich.',
                ],
            ],
        ];

        echo View::render('contact', [
            'page'            => $page,
            'currentSlug'     => 'kontakt',
            'structured_data' => [$howTo],
        ]);
    }
}

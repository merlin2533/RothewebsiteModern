<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;

final class LegalController
{
    public function imprint(array $args): void
    {
        $page = $GLOBALS['pageRepo']->findBySlug('impressum');
        echo View::render('legal', [
            'page'        => $page,
            'currentSlug' => 'impressum',
        ]);
    }

    public function privacy(array $args): void
    {
        $page = $GLOBALS['pageRepo']->findBySlug('datenschutz');
        echo View::render('legal', [
            'page'        => $page,
            'currentSlug' => 'datenschutz',
        ]);
    }

    public function notFound(array $args): void
    {
        http_response_code(404);
        $page = $GLOBALS['pageRepo']->findBySlug('404') ?? [
            'title'            => 'Seite nicht gefunden',
            'meta_title'       => 'Seite nicht gefunden | Rothe-Transporte',
            'meta_description' => 'Diese Seite existiert nicht oder wurde verschoben.',
            'hero_headline'    => 'Diese Strecke führt ins Leere.',
            'hero_sub'         => 'Die gesuchte Seite gibt es nicht (mehr).',
            'content_html'     => '<p>Bitte nutzen Sie die Navigation oben oder rufen Sie uns an: <a href="tel:+497127182310">07127 18231</a>.</p>',
        ];
        echo View::render('not_found', [
            'page'        => $page,
            'currentSlug' => '',
        ]);
    }
}

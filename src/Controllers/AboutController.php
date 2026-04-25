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

        echo View::render('about', [
            'page'        => $page,
            'currentSlug' => 'ueber-uns',
            'events'      => $events,
        ]);
    }
}
